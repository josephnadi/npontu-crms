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
        Schema::create('communications', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // email, sms, whatsapp, call
            $table->string('direction'); // inbound, outbound
            $table->string('subject')->nullable();
            $table->text('content');
            $table->string('status')->default('sent'); // sent, delivered, read, failed
            
            // From/To details
            $table->string('from_identifier'); // email address or phone number
            $table->string('to_identifier');   // email address or phone number
            
            // Polymorphic link to Contact, Lead, etc.
            $table->string('communicable_type')->nullable();
            $table->unsignedBigInteger('communicable_id')->nullable();
            $table->index(['communicable_type', 'communicable_id']);

            $table->json('metadata')->nullable(); // For raw API responses, headers, etc.
            
            $table->foreignId('owner_id')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('communications');
    }
};
