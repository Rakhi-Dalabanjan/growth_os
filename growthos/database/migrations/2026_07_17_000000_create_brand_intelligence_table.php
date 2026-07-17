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
        Schema::create('brand_intelligence', function (Blueprint $table) {
            $table->id();
            
            // Unique to ensure one brand intelligence record per organization
            $table->foreignId('organization_id')
                  ->unique()
                  ->constrained('organizations')
                  ->onDelete('cascade');

            $table->foreignId('brand_profile_id')
                  ->constrained('brand_profiles')
                  ->onDelete('cascade');

            $table->text('summary')->nullable();
            
            // Structured fields
            $table->json('brand_personality')->nullable();
            $table->json('brand_voice')->nullable();
            $table->json('ideal_customer')->nullable();
            $table->json('customer_problems')->nullable();
            $table->json('customer_goals')->nullable();
            $table->json('marketing_objectives')->nullable();
            $table->text('competitor_summary')->nullable();
            
            // Recommendations
            $table->json('recommended_content_pillars')->nullable();
            $table->string('recommended_posting_frequency')->nullable();
            $table->json('recommended_cta')->nullable();
            $table->json('recommended_hashtags')->nullable();

            // SWOT
            $table->json('strengths')->nullable();
            $table->json('weaknesses')->nullable();
            $table->json('opportunities')->nullable();
            $table->json('risks')->nullable();

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
        Schema::dropIfExists('brand_intelligence');
    }
};
