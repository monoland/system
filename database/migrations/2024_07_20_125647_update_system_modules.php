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
        if (!Schema::hasColumn('system_modules', 'git_address')) {
            Schema::table('system_modules', function (Blueprint $table) {
                $table->string('git_address')->after('ownerable_id')->nullable();
                $table->string('git_version')->after('git_address')->nullable();
                $table->timestamp('git_updated_at')->after('git_version')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('system_modules', 'git_address')) {
            Schema::table('system_modules', function (Blueprint $table) {
                $table->dropColumn('git_address');
                $table->dropColumn('git_version');
                $table->dropColumn('git_updated_at');
            });
        }
    }
};
