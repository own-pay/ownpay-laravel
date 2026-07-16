<?php

declare(strict_types=1);

namespace OwnPay\Laravel\Resources;

use OwnPay\Laravel\Http\Response;

/**
 * Customer resource.
 *
 * Represents a customer from the OwnPay API.
 */
final readonly class Customer
{
    /**
     * Create a new Customer instance.
     *
     * @param  int|null  $id  The customer ID.
     * @param  string|null  $uuid  The customer UUID.
     * @param  string|null  $name  The customer name.
     * @param  string|null  $email  The customer email.
     * @param  string|null  $phone  The customer phone.
     * @param  string|null  $emailMasked  The masked email.
     * @param  string|null  $phoneMasked  The masked phone.
     * @param  string|null  $createdAt  The creation timestamp.
     */
    public function __construct(
        public ?int $id,
        public ?string $uuid,
        public ?string $name,
        public ?string $email,
        public ?string $phone,
        public ?string $emailMasked,
        public ?string $phoneMasked,
        public ?string $createdAt,
    ) {
        //
    }

    /**
     * Create a Customer from an API response.
     *
     * @param  Response  $response  The API response.
     * @return static
     */
    public static function fromResponse(Response $response): static
    {
        $data = $response->getData() ?? [];

        return static::fromArray($data);
    }

    /**
     * Create a Customer from an array.
     *
     * @param  array<string, mixed>  $data  The customer data.
     * @return static
     */
    public static function fromArray(array $data): static
    {
        return new static(
            id: isset($data['id']) ? (int) $data['id'] : null,
            uuid: $data['uuid'] ?? null,
            name: $data['name'] ?? null,
            email: $data['email'] ?? null,
            phone: $data['phone'] ?? null,
            emailMasked: $data['email_masked'] ?? null,
            phoneMasked: $data['phone_masked'] ?? null,
            createdAt: $data['created_at'] ?? null,
        );
    }

    /**
     * Get the display name.
     */
    public function getDisplayName(): string
    {
        return $this->name ?? $this->email ?? $this->phone ?? 'Unknown Customer';
    }

    /**
     * Convert to array.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return array_filter([
            'id' => $this->id,
            'uuid' => $this->uuid,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'email_masked' => $this->emailMasked,
            'phone_masked' => $this->phoneMasked,
            'created_at' => $this->createdAt,
        ], fn (mixed $value) => $value !== null);
    }

    /**
     * Get the JSON serialization.
     *
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
