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
        Schema::table('deals', function (Blueprint $table) {
            $table->foreignId('contact_id')->nullable()->after('deal_stage_id')->constrained()->onDelete('set null');
            $table->foreignId('client_id')->nullable()->after('contact_id')->constrained()->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('deals', function (Blueprint $table) {
            $table->dropForeign(['contact_id']);
            $table->dropForeign(['client_id']);
            $table->dropColumn(['contact_id', 'client_id']);
        });
    }
};
