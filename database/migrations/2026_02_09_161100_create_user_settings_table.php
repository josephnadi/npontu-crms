<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('user_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('locale')->default('en');
            $table->string('timezone')->default('UTC');
            $table->string('currency')->default(config('crm.currency', 'GHS'));
            $table->string('theme')->default('light');
            $table->boolean('notif_in_app')->default(true);
            $table->boolean('notif_email')->default(false);
            $table->boolean('notif_due_reminders')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_settings');
    }
};
