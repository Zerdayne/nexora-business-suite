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
        Schema::create('tenant_users', function (Blueprint $table) {
            $table->foreignUuid('tenant_id')->references('id')->on('tenants')->cascadeOnDelete();

            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->string('status')->default('active');
            $table->timestamp('invited_at')->nullable();
            $table->timestamp('activated_at')->nullable();

            $table->timestamps();

            $table->primary(['tenant_id', 'user_id']);
            $table->index(['tenant_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenant_users');
    }
};
