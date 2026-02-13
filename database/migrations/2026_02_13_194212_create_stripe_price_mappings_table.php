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
        Schema::create('stripe_price_mappings', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->string('type'); // plan_base | plan_seat | plan_api_usage | module
            $table->string('key'); // plan_key or module_key (e.g. "base" or "crm")

            $table->string('stripe_product_id')->nullable();
            $table->string('stripe_price_id')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->unique(['type', 'key']);
            $table->index(['type', 'key']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stripe_price_mappings');
    }
};
