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
        Schema::create('content_captions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('organization_id')
                  ->constrained('organizations')
                  ->onDelete('cascade');

            $table->foreignId('content_calendar_id')
                  ->constrained('content_calendars')
                  ->onDelete('cascade');

            $table->string('platform');
            $table->string('headline')->nullable();
            $table->text('caption');
            $table->json('hashtags')->nullable();
            $table->string('cta')->nullable();
            $table->json('keywords')->nullable();
            $table->string('emoji_style')->nullable();
            $table->string('tone')->nullable();
            $table->string('language')->nullable();
            $table->integer('word_count')->default(0);
            $table->integer('character_count')->default(0);
            $table->string('status')->default('Draft'); // Draft, Approved, Rejected, Scheduled, Published

            // Metadata
            $table->string('provider')->nullable();
            $table->string('model')->nullable();
            $table->timestamp('generated_at')->nullable();

            $table->timestamps();

            // Indexes
            $table->index('organization_id', 'captions_org_idx');
            $table->index('content_calendar_id', 'captions_cc_idx');
            $table->index('status', 'captions_status_idx');
            $table->index('platform', 'captions_platform_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('content_captions');
    }
};
