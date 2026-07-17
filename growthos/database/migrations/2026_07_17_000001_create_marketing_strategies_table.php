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
        Schema::create('marketing_strategies', function (Blueprint $table) {
            $table->id();

            // Ensure one active marketing strategy per organization
            $table->foreignId('organization_id')
                  ->unique()
                  ->constrained('organizations')
                  ->onDelete('cascade');

            $table->foreignId('brand_intelligence_id')
                  ->constrained('brand_intelligence')
                  ->onDelete('cascade');

            $table->string('strategy_name')->nullable();
            $table->text('business_goal')->nullable();
            $table->text('marketing_goal')->nullable();

            // JSON casting arrays
            $table->json('recommended_platforms')->nullable();
            $table->json('content_pillars')->nullable();
            $table->json('campaign_ideas')->nullable();
            $table->string('posting_frequency')->nullable();
            $table->json('recommended_formats')->nullable();
            $table->json('tone_guidelines')->nullable();
            $table->json('audience_segments')->nullable();
            $table->json('hashtags_strategy')->nullable();
            $table->json('cta_strategy')->nullable();
            $table->json('kpis')->nullable();
            $table->json('growth_recommendations')->nullable();
            $table->json('risk_considerations')->nullable();

            // Metadata
            $table->integer('confidence_score')->nullable();
            $table->string('provider')->nullable();
            $table->string('model')->nullable();
            $table->decimal('execution_time', 8, 2)->nullable();
            $table->timestamp('generated_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('marketing_strategies');
    }
};
