<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ownpay_webhook_logs', function (Blueprint $table) {
            $table->id();
            $table->string('event', 100)->index();
            $table->string('payload_hash', 64)->index();
            $table->string('status', 20)->default('received');
            $table->json('payload')->nullable();
            $table->text('error')->nullable();
            $table->string('request_id')->nullable();
            $table->timestamps();

            // Index for deduplication
            $table->index(['payload_hash', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ownpay_webhook_logs');
    }
};
