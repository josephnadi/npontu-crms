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
        Schema::table('tasks', function (Blueprint $table) {
            // Polymorphic relation
            $table->string('taskable_type')->nullable()->after('due_date');
            $table->unsignedBigInteger('taskable_id')->nullable()->after('taskable_type');
            $table->index(['taskable_type', 'taskable_id']);

            // Intelligence attributes
            $table->integer('sla_minutes')->nullable()->after('taskable_id');
            $table->timestamp('escalated_at')->nullable()->after('sla_minutes');
            $table->integer('priority_score')->default(0)->after('escalated_at');
            $table->json('suggested_actions')->nullable()->after('priority_score');
            
            // Task type
            $table->string('type')->default('general')->after('title'); // call, email, meeting, demo, etc.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn([
                'taskable_type', 
                'taskable_id', 
                'sla_minutes', 
                'escalated_at', 
                'priority_score', 
                'suggested_actions',
                'type'
            ]);
        });
    }
};
