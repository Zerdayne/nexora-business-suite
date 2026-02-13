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
        Schema::create('features', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('module_id')->references('id')->on('modules')->cascadeOnDelete();

            $table->string('key');
            $table->string('name');
            $table->string('type');
            $table->jsonb('default_value')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->unique(['module_id', 'key']);
            $table->index(['module_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('features');
    }
};
