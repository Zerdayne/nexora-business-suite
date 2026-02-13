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
        Schema::create('tenant_entitlement_overrides', function (Blueprint $table) {
            $table->foreignUuid('tenant_id')->references('id')->on('tenants')->cascadeOnDelete();

            $table->string('feature_key');
            $table->jsonb('value');
            $table->string('reason')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->primary(['tenant_id', 'feature_key']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenant_entitlement_overrides');
    }
};
