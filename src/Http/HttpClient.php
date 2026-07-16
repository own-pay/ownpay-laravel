<?php

declare(strict_types=1);

namespace OwnPay\Laravel\Http;

use Illuminate\Support\Facades\Http;
use OwnPay\Laravel\Auth\AuthenticatorInterface;
use OwnPay\Laravel\Exception\AuthenticationException;
use OwnPay\Laravel\Exception\ConnectionException;
use OwnPay\Laravel\Exception\IdempotencyException;
use OwnPay\Laravel\Exception\InvalidRequestException;
use OwnPay\Laravel\Exception\NotFoundException;
use OwnPay\Laravel\Exception\OwnPayExceptionInterface;
use OwnPay\Laravel\Exception\PaymentFailedException;
use OwnPay\Laravel\Exception\RateLimitException;

/**
 * HTTP client for making requests to the OwnPay API.
 *
 * This client handles authentication, request signing, response parsing,
 * and error mapping. It uses Laravel's HTTP client under the hood.
 */
final class HttpClient
{
    /**
     * The base URL for the OwnPay API.
     */
    private string $baseUrl;

    /**
     * The request timeout in seconds.
     */
    private int $timeout;

    /**
     * Whether to verify SSL certificates.
     */
    private bool $verifySsl;

    /**
     * Create a new HttpClient.
     *
     * @param  AuthenticatorInterface  $authenticator  The authenticator for adding credentials.
     * @param  string  $baseUrl  The base URL for the API.
     * @param  int  $timeout  The request timeout in seconds.
     * @param  bool  $verifySsl  Whether to verify SSL certificates.
     */
    public function __construct(
        private readonly AuthenticatorInterface $authenticator,
        string $baseUrl = 'https://pay.ownpay.org',
        int $timeout = 30,
        bool $verifySsl = true,
    ) {
        $this->baseUrl = rtrim($baseUrl, '/');
        $this->timeout = $timeout;
        $this->verifySsl = $verifySsl;
    }

    /**
     * Make a GET request to the API.
     *
     * @param  string  $endpoint  The API endpoint (e.g., "/api/v1/payments").
     * @param  array<string, mixed>  $query  Query parameters.
     * @param  array<string, string>  $headers  Additional headers.
     *
     * @throws OwnPayExceptionInterface
     */
    public function get(string $endpoint, array $query = [], array $headers = []): Response
    {
        $url = $this->buildUrl($endpoint, $query);

        return $this->request('GET', $url, $headers);
    }

    /**
     * Make a POST request to the API.
     *
     * @param  string  $endpoint  The API endpoint.
     * @param  array<string, mixed>  $data  The request body data.
     * @param  array<string, string>  $headers  Additional headers.
     *
     * @throws OwnPayExceptionInterface
     */
    public function post(string $endpoint, array $data = [], array $headers = []): Response
    {
        $url = $this->buildUrl($endpoint);

        return $this->request('POST', $url, $headers, $data);
    }

    /**
     * Make a PUT request to the API.
     *
     * @param  string  $endpoint  The API endpoint.
     * @param  array<string, mixed>  $data  The request body data.
     * @param  array<string, string>  $headers  Additional headers.
     *
     * @throws OwnPayExceptionInterface
     */
    public function put(string $endpoint, array $data = [], array $headers = []): Response
    {
        $url = $this->buildUrl($endpoint);

        return $this->request('PUT', $url, $headers, $data);
    }

    /**
     * Make a PATCH request to the API.
     *
     * @param  string  $endpoint  The API endpoint.
     * @param  array<string, mixed>  $data  The request body data.
     * @param  array<string, string>  $headers  Additional headers.
     *
     * @throws OwnPayExceptionInterface
     */
    public function patch(string $endpoint, array $data = [], array $headers = []): Response
    {
        $url = $this->buildUrl($endpoint);

        return $this->request('PATCH', $url, $headers, $data);
    }

    /**
     * Make a DELETE request to the API.
     *
     * @param  string  $endpoint  The API endpoint.
     * @param  array<string, string>  $headers  Additional headers.
     *
     * @throws OwnPayExceptionInterface
     */
    public function delete(string $endpoint, array $headers = []): Response
    {
        $url = $this->buildUrl($endpoint);

        return $this->request('DELETE', $url, $headers);
    }

    /**
     * Make an HTTP request.
     *
     * @param  string  $method  The HTTP method.
     * @param  string  $url  The full URL.
     * @param  array<string, string>  $headers  Additional headers.
     * @param  array<string, mixed>|null  $data  The request body data.
     *
     * @throws OwnPayExceptionInterface
     */
    private function request(string $method, string $url, array $headers = [], ?array $data = null): Response
    {
        // Apply authentication
        $headers = $this->authenticator->authenticate($headers);

        // Set content type for requests with body
        if ($data !== null) {
            $headers['Content-Type'] = 'application/json';
        }

        try {
            $response = Http::withHeaders($headers)
                ->timeout($this->timeout)
                ->withOptions([
                    'verify' => $this->verifySsl,
                ])
                ->send($method, $url, [
                    'json' => $data,
                ]);

            $apiResponse = Response::fromHttpResponse(
                $response->status(),
                $response->headers(),
                $response->body(),
            );

            // Throw exceptions for error responses
            if (! $apiResponse->isSuccess()) {
                $this->throwExceptionForResponse($apiResponse);
            }

            return $apiResponse;
        } catch (OwnPayExceptionInterface $e) {
            throw $e;
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            throw new ConnectionException(
                "Failed to connect to OwnPay API: {$e->getMessage()}",
                null,
                'CONNECTION_FAILED',
                null,
                $e,
            );
        } catch (\Throwable $e) {
            throw new ConnectionException(
                "Unexpected error communicating with OwnPay API: {$e->getMessage()}",
                null,
                'UNEXPECTED_ERROR',
                null,
                $e,
            );
        }
    }

    /**
     * Build the full URL for an API endpoint.
     *
     * @param  string  $endpoint  The API endpoint.
     * @param  array<string, mixed>  $query  Query parameters.
     */
    private function buildUrl(string $endpoint, array $query = []): string
    {
        $url = $this->baseUrl.'/'.ltrim($endpoint, '/');

        if (! empty($query)) {
            $url .= '?'.http_build_query($query);
        }

        return $url;
    }

    /**
     * Throw an appropriate exception for an error response.
     *
     * @param  Response  $response  The error response.
     *
     * @throws OwnPayExceptionInterface
     */
    private function throwExceptionForResponse(Response $response): never
    {
        $message = $response->getErrorMessage() ?? 'Unknown API error';
        $errorCode = $response->getErrorCode();
        $statusCode = $response->statusCode;

        /** @var array<string, mixed> $details */
        $details = $response->getErrors();

        $exception = match ($statusCode) {
            401, 403 => new AuthenticationException($message, $statusCode, $errorCode, $details),
            404 => new NotFoundException($message, $statusCode, $errorCode, $details),
            409 => new IdempotencyException($message, $statusCode, $errorCode, $details),
            422 => new InvalidRequestException($message, $statusCode, $errorCode, $details),
            429 => new RateLimitException($message, $statusCode, $errorCode, [
                ...$details,
                'retry_after' => $response->getRetryAfter(),
            ]),
            default => match (true) {
                $statusCode >= 400 && $statusCode < 500 => new InvalidRequestException($message, $statusCode, $errorCode, $details),
                $statusCode >= 500 => new PaymentFailedException($message, $statusCode, $errorCode, $details),
                default => new ConnectionException($message, $statusCode, $errorCode, $details),
            },
        };

        throw $exception;
    }
}
