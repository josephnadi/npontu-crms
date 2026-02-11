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
        Schema::create('engagements', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // email/call/meeting/webinar/form_submitted/content_viewed/other
            $table->string('subject');
            $table->text('description')->nullable();
            
            $table->integer('score')->default(0);
            $table->timestamp('engagement_date')->nullable();
            
            // Polymorphic relationship (Lead, Client, Contact, Deal)
            $table->morphs('engageable');
            
            $table->json('metadata')->nullable();
            
            $table->foreignId('user_id')->nullable()->constrained('users'); // The person who recorded it
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            
            $table->timestamps();
            $table->softDeletes();

            $table->index('type');
            $table->index('score');
            $table->index('engagement_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('engagements');
    }
};
