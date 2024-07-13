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
        Schema::create('system_modules', function (Blueprint $table) {
            $table->id();
            $table->text('name')->unique();
            $table->text('slug')->unique();
            $table->text('icon');
            $table->text('color')->default('blue-grey');
            $table->enum('type', ['administrator', 'personal'])->default('administrator');
            $table->text('domain')->index()->default('backend');
            $table->text('prefix')->index()->nullable();
            $table->nullableMorphs('ownerable');
            $table->jsonb('meta')->nullable();
            $table->boolean('desktop')->default(true);
            $table->boolean('mobile')->default(true);
            $table->boolean('enabled')->default(true);
            $table->boolean('published')->default(true);
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
        Schema::dropIfExists('system_modules');
    }
};
