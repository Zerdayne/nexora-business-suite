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
        Schema::create('plan_entitlements', function (Blueprint $table) {
            $table->uuid('plan_id');
            $table->uuid('feature_id');

            $table->foreignUuid('plan_id')->references('id')->on('plans')->cascadeOnDelete();
            $table->foreignUuid('feature_id')->references('id')->on('features')->cascadeOnDelete();

            $table->jsonb('value');

            $table->timestamps();
            $table->softDeletes();

            $table->primary(['plan_id', 'feature_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plan_entitlements');
    }
};
