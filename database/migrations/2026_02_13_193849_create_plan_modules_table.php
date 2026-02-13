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
        Schema::create('plan_modules', function (Blueprint $table) {
            $table->uuid('plan_id');
            $table->uuid('module_id');

            $table->foreignUuid('plan_id')->references('id')->on('plans')->cascadeOnDelete();
            $table->foreignUuid('module_id')->references('id')->on('modules')->cascadeOnDelete();

            $table->boolean('is_active')->default(false);

            $table->timestamps();
            $table->softDeletes();

            $table->primary(['plan_id', 'module_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plan_modules');
    }
};
