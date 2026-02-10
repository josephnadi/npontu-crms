<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\QueryException;

return new class extends Migration {
    public function up(): void
    {
        try {
            Schema::table('leads', function (Blueprint $table) {
                $table->index('status');
                $table->index('source');
                $table->index('owner_id');
            });
        } catch (QueryException $e) {}

        try {
            Schema::table('deals', function (Blueprint $table) {
                $table->index('owner_id');
                $table->index('status');
                $table->index('expected_close_date');
            });
        } catch (QueryException $e) {}

        try {
            Schema::table('activities', function (Blueprint $table) {
                $table->index('activity_date');
                $table->index('status');
                $table->index('owner_id');
                $table->index(['activityable_type', 'activityable_id']);
            });
        } catch (QueryException $e) {}

        try {
            Schema::table('contacts', function (Blueprint $table) {
                $table->index('owner_id');
            });
        } catch (QueryException $e) {}

        try {
            Schema::table('clients', function (Blueprint $table) {
                $table->index('owner_id');
                $table->index('name');
            });
        } catch (QueryException $e) {}

        try {
            Schema::table('deal_stages', function (Blueprint $table) {
                $table->index('order_column');
                $table->index('is_active');
            });
        } catch (QueryException $e) {}
    }

    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['source']);
            $table->dropIndex(['owner_id']);
        });

        Schema::table('deals', function (Blueprint $table) {
            $table->dropIndex(['owner_id']);
            $table->dropIndex(['status']);
            $table->dropIndex(['expected_close_date']);
        });

        Schema::table('activities', function (Blueprint $table) {
            $table->dropIndex(['activity_date']);
            $table->dropIndex(['status']);
            $table->dropIndex(['owner_id']);
            $table->dropIndex(['activityable_type', 'activityable_id']);
        });

        Schema::table('contacts', function (Blueprint $table) {
            $table->dropIndex(['owner_id']);
        });

        Schema::table('clients', function (Blueprint $table) {
            $table->dropIndex(['owner_id']);
            $table->dropIndex(['name']);
        });

        Schema::table('deal_stages', function (Blueprint $table) {
            $table->dropIndex(['order_column']);
            $table->dropIndex(['is_active']);
        });
    }
};
