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
        Schema::create('social_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->onDelete('cascade');
            $table->string('platform'); // facebook, instagram
            $table->string('platform_user_id')->nullable();
            $table->string('page_id')->nullable();
            $table->string('page_name')->nullable();
            $table->string('instagram_business_id')->nullable();
            $table->string('account_name')->nullable();
            $table->text('access_token')->nullable(); // nullable because we clear it on disconnect
            $table->text('refresh_token')->nullable();
            $table->timestamp('token_expires_at')->nullable();
            $table->json('permissions')->nullable();
            $table->timestamp('connected_at')->nullable();
            $table->string('status'); // connected, disconnected, expired, error, pending
            $table->timestamp('last_sync')->nullable();
            $table->timestamps();

            // Indexes to prevent duplicate connections of the same Facebook page / Instagram account
            // Since SQLite allows multiple nulls in unique index, these are perfectly safe.
            $table->unique(['organization_id', 'platform', 'page_id'], 'sa_org_plat_page_unique');
            $table->unique(['organization_id', 'platform', 'instagram_business_id'], 'sa_org_plat_ig_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('social_accounts');
    }
};
