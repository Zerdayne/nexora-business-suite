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
        Schema::create('subscription_modules', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->uuid('subscription_id');
            $table->foreignUuid('subscription_id')->references('id')->on('subscriptions')->cascadeOnDelete();

            $table->uuid('module_id');
            $table->foreignUuid('module_id')->references('id')->on('modules')->cascadeOnDelete();

            $table->string('status')->default('active'); // active|suspended|canceled
            $table->timestampTz('activated_at')->nullable();
            $table->timestampTz('canceled_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->unique(['subscription_id', 'module_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_modules');
    }
};
