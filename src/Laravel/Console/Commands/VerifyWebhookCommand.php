<?php

declare(strict_types=1);

namespace OwnPay\Laravel\Laravel\Console\Commands;

use Illuminate\Console\Command;
use OwnPay\Laravel\Webhook\WebhookVerifier;

/**
 * Artisan command to verify a webhook payload.
 *
 * This command verifies a webhook payload signature, which is useful
 * for debugging webhook integration issues.
 */
class VerifyWebhookCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'ownpay:verify-webhook
        {--payload= : The raw webhook payload (JSON string)}
        {--signature= : The signature from the X-OP-Signature header}
        {--timestamp= : The timestamp from the X-OwnPay-Timestamp header}
        {--file= : Path to a file containing the webhook payload}';

    /**
     * The console command description.
     */
    protected $description = 'Verify a webhook payload signature';

    /**
     * Execute the console command.
     */
    public function handle(WebhookVerifier $verifier): int
    {
        // Get payload
        $payload = $this->option('payload');
        $file = $this->option('file');

        if ($file) {
            if (! file_exists($file)) {
                $this->error("File not found: {$file}");

                return self::FAILURE;
            }

            $payload = file_get_contents($file);
            if ($payload === false) {
                $this->error("Failed to read file: {$file}");

                return self::FAILURE;
            }
        }

        if (empty($payload)) {
            $this->error('No payload provided. Use --payload or --file option.');

            return self::FAILURE;
        }

        // Get signature
        $signature = $this->option('signature');
        if (empty($signature)) {
            $this->error('No signature provided. Use --signature option.');

            return self::FAILURE;
        }

        // Get timestamp
        $timestamp = $this->option('timestamp');
        $timestampInt = $timestamp !== null ? (int) $timestamp : null;

        try {
            $result = $verifier->verify($payload, $signature, $timestampInt);

            $this->info('✓ Signature verified successfully!');
            $this->newLine();
            $this->info('Decoded payload:');
            $this->line(json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error('✗ Signature verification failed!');
            $this->newLine();
            $this->error("Error: {$e->getMessage()}");

            return self::FAILURE;
        }
    }
}
