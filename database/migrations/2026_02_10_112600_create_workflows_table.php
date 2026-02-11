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
        Schema::create('workflows', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description')->nullable();
            $table->string('event_type'); // e.g., lead.created, deal.updated
            $table->json('conditions')->nullable(); // JSON configuration of conditions
            $table->json('actions')->nullable(); // JSON configuration of actions
            $table->boolean('is_active')->default(true);
            $table->integer('priority')->default(0);
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
        Schema::dropIfExists('workflows');
    }
};
