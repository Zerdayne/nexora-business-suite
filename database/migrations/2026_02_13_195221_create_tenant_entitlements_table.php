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
        Schema::create('tenant_entitlements', function (Blueprint $table) {
            $table->uuid('tenant_id');
            $table->foreignUuid('tenant_id')->references('id')->on('tenants')->cascadeOnDelete();

            $table->string('feature_key');
            $table->jsonb('value');
            $table->unsignedBigInteger('version'); // match tenants.entitlements_version

            $table->timestamps();
            $table->softDeletes();

            $table->primary(['tenant_id', 'feature_key']);
            $table->index(['tenant_id', 'version']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenant_entitlements');
    }
};
