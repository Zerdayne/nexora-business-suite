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
        Schema::create('user_roles', function (Blueprint $table) {
            $table->uuid('tenant_id');
            $table->foreignUuid('tenant_id')->references('id')->on('tenants')->cascadeOnDelete();

            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->uuid('role_id');
            $table->foreignUuid('role_id')->references('id')->on('roles')->cascadeOnDelete();

            $table->timestamps();
            $table->softDeletes();

            $table->primary(['tenant_id', 'user_id', 'role_id']);
            $table->index(['tenant_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_roles');
    }
};
