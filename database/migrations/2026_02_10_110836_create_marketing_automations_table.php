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
        Schema::create('marketing_automations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['email_campaign', 'drip_sequence', 'newsletter', 'sms_alert'])->default('email_campaign');
            $table->enum('status', ['draft', 'active', 'paused', 'completed'])->default('draft');
            $table->text('description')->nullable();
            $table->json('trigger_config')->nullable(); // Config for what triggers the automation
            $table->json('content_config')->nullable(); // Config for content (e.g., email templates)
            $table->integer('sent_count')->default(0);
            $table->integer('open_count')->default(0);
            $table->integer('click_count')->default(0);
            $table->foreignId('owner_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('marketing_automations');
    }
};
