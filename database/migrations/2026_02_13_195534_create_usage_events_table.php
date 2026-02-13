<?php

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
        Schema::create('usage_events', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignUuid('tenant_id')->references('id')->on('tenants')->cascadeOnDelete();

            $table->string('metric_key'); // e.g. 'api.calls'
            $table->unsignedBigInteger('value');
            $table->timestampTz('occurred_at');
            $table->string('idempotency_key')->nullable()->unique();

            $table->jsonb('dimensions')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'metric_key', 'occurred_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('usage_events');
    }
};
