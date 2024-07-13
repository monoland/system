<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Module\System\Support\AuditMigration;

return new class () extends AuditMigration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('system_user_logs', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->enum('event', [
                'approved',
                'confirmed',
                'created',
                'deleted',
                'trashed',
                'printed',
                'published',
                'rejected',
                'restored',
                'signed',
                'submitted',
                'synced',
                'updated',
                'verified'
            ])->default('created');
            $table->string('module')->index();
            $table->morphs('subjectable');
            $table->jsonb('dirties')->nullable();
            $table->jsonb('origins')->nullable();
            $table->foreignId('user_id');
            $table->string('user_name')->index()->default('system');
            $table->boolean('impersonate')->index()->default(false);
            $table->foreignId('impersonate_id')->nullable();
            $table->string('impersonate_name')->index()->nullable();
            $table->jsonb('coords')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_user_logs');
    }
};
