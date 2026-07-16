<?php

declare(strict_types=1);

namespace OwnPay\Laravel\Laravel\Console\Commands;

use Illuminate\Console\Command;
use OwnPay\Laravel\Client\OwnPayClient;
use OwnPay\Laravel\Exception\OwnPayExceptionInterface;

/**
 * Artisan command to test the OwnPay API connection.
 *
 * This command verifies that the API key is valid and the
 * OwnPay instance is reachable.
 */
class TestConnectionCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'ownpay:test
        {--json : Output the result as JSON}';

    /**
     * The console command description.
     */
    protected $description = 'Test the connection to the OwnPay API';

    /**
     * Execute the console command.
     */
    public function handle(OwnPayClient $client): int
    {
        $this->info('Testing OwnPay API connection...');

        try {
            $health = $client->health();

            if ($this->option('json')) {
                $this->line(json_encode($health, JSON_PRETTY_PRINT));

                return self::SUCCESS;
            }

            $this->newLine();
            $this->info('✓ Connection successful!');
            $this->newLine();

            $this->table(
                ['Property', 'Value'],
                [
                    ['Status', $health['status'] ?? 'unknown'],
                    ['Version', $health['version'] ?? 'unknown'],
                    ['Database', $health['db'] ?? 'unknown'],
                    ['Gateways', (string) ($health['gateways'] ?? 0)],
                    ['Customers', (string) ($health['customers'] ?? 0)],
                    ['Time', $health['time'] ?? 'unknown'],
                ],
            );

            return self::SUCCESS;
        } catch (OwnPayExceptionInterface $e) {
            if ($this->option('json')) {
                $this->line(json_encode([
                    'success' => false,
                    'error' => $e->getMessage(),
                    'error_code' => $e->getErrorCode(),
                    'http_status' => $e->getHttpStatusCode(),
                ], JSON_PRETTY_PRINT));

                return self::FAILURE;
            }

            $this->error('✗ Connection failed!');
            $this->newLine();
            $this->error("Error: {$e->getMessage()}");

            if ($e->getErrorCode()) {
                $this->error("Code: {$e->getErrorCode()}");
            }

            if ($e->getHttpStatusCode()) {
                $this->error("HTTP Status: {$e->getHttpStatusCode()}");
            }

            return self::FAILURE;
        } catch (\Throwable $e) {
            if ($this->option('json')) {
                $this->line(json_encode([
                    'success' => false,
                    'error' => $e->getMessage(),
                ], JSON_PRETTY_PRINT));

                return self::FAILURE;
            }

            $this->error('✗ Connection failed!');
            $this->newLine();
            $this->error("Error: {$e->getMessage()}");

            return self::FAILURE;
        }
    }
}
