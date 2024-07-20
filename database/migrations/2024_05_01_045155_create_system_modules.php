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
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->string('icon');
            $table->string('color')->default('blue-grey');
            $table->enum('type', ['administrator', 'personal'])->default('administrator');
            $table->string('domain')->index()->default('backend');
            $table->string('prefix')->index()->nullable();
            $table->nullableMorphs('ownerable');
            $table->string('git_address')->after('ownerable_id')->nullable();
            $table->string('git_version')->after('git_address')->nullable();
            $table->timestamp('git_updated_at')->after('git_version')->nullable();
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
