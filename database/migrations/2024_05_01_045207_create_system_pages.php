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
        Schema::create('system_pages', function (Blueprint $table) {
            $table->id();
            $table->string('name')->index();
            $table->string('slug')->unique();
            $table->string('title')->nullable();
            $table->string('icon')->default('home');
            $table->string('path')->index();
            $table->boolean('side')->default(false);
            $table->boolean('dock')->default(false);
            $table->boolean('enabled')->default(true);
            $table->foreignId('module_id')
                ->constrained('system_modules')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->jsonb('meta')->nullable();
            $table->nestedSet();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_pages');
    }
};
