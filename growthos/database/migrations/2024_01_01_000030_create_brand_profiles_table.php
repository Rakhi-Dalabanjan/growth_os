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
        Schema::create('brand_profiles', function (Blueprint $table) {
            $table->id();
            // organization_id must be unique to ensure one brand profile per organization
            $table->foreignId('organization_id')
                  ->unique()
                  ->constrained('organizations')
                  ->onDelete('cascade');
            
            // Brand Identity
            $table->string('brand_name');
            $table->string('tagline')->nullable();
            $table->text('business_description')->nullable();

            // Business & Audience
            $table->text('mission')->nullable();
            $table->text('vision')->nullable();
            $table->string('primary_market')->nullable();
            $table->text('target_audience')->nullable();

            // Brand Voice
            $table->string('brand_tone')->nullable();
            $table->string('formality')->nullable();
            $table->string('language')->nullable();
            $table->string('emoji_style')->nullable();

            // Brand Style
            $table->string('primary_color', 7)->nullable(); // e.g. #FF5733
            $table->string('secondary_color', 7)->nullable();
            $table->string('accent_color', 7)->nullable();
            $table->string('primary_font')->nullable();
            $table->string('secondary_font')->nullable();

            // Marketing
            $table->string('primary_cta')->nullable();
            $table->string('secondary_cta')->nullable();
            $table->json('preferred_words')->nullable();
            $table->json('restricted_words')->nullable();
            $table->json('competitor_names')->nullable();

            // Compliance
            $table->json('approved_claims')->nullable();
            $table->json('restricted_claims')->nullable();
            $table->text('legal_disclaimer')->nullable();

            $table->string('status')->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('brand_profiles');
    }
};
