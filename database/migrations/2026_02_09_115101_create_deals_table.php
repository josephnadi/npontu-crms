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
        Schema::create('deals', function (Blueprint $table) {
            $table->id();
            $table->string('title', 255);
            $table->text('description')->nullable();
            $table->decimal('value', 15, 2)->default(0);
            $table->string('currency', 3)->default('USD');
            
            $table->foreignId('deal_stage_id')->constrained();
            $table->string('contact_name')->nullable(); // Temporary until contacts table exists
            $table->string('client_name')->nullable();  // Temporary until clients table exists
            
            $table->date('expected_close_date')->nullable();
            $table->date('actual_close_date')->nullable();
            $table->integer('probability')->default(0);
            
            $table->string('status', 20)->default('open'); // open/won/lost
            $table->text('lost_reason')->nullable();
            
            $table->json('tags')->nullable();
            $table->json('custom_fields')->nullable();
            
            $table->foreignId('owner_id')->nullable()->constrained('users');
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('deal_stage_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deals');
    }
};
