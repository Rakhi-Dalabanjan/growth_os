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
        Schema::create('content_calendars', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('organization_id')
                  ->constrained('organizations')
                  ->onDelete('cascade');
                  
            $table->foreignId('marketing_strategy_id')
                  ->constrained('marketing_strategies')
                  ->onDelete('cascade');

            $table->integer('month');
            $table->integer('year');
            $table->string('platform');
            $table->string('title');
            $table->text('topic');
            $table->string('content_pillar');
            $table->string('campaign_name');
            $table->string('goal');
            $table->string('content_type');
            $table->string('post_format');
            $table->string('status')->default('Draft'); // Draft, Approved, Rejected, Scheduled, Published
            $table->date('planned_date');
            $table->time('planned_time')->nullable();
            $table->string('priority')->default('Medium'); // Low, Medium, High
            $table->text('notes')->nullable();
            
            // Metadata
            $table->string('provider')->nullable();
            $table->string('model')->nullable();
            $table->timestamp('generated_at')->nullable();

            $table->timestamps();

            // Indexes
            $table->index(['organization_id', 'month', 'year'], 'cc_org_month_year_idx');
            $table->index('planned_date', 'cc_planned_date_idx');
            $table->index('status', 'cc_status_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('content_calendars');
    }
};
