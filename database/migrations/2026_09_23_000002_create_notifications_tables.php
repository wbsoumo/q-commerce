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
        // 1. FCM Settings Table (Stores Service Account JSON & Project ID)
        if (!Schema::hasTable('fcm_settings')) {
            Schema::create('fcm_settings', function (Blueprint $table) {
                $table->id();
                $table->string('project_id')->nullable();
                $table->longText('service_account_json')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // 2. User FCM Tokens Table
        if (!Schema::hasTable('fcm_tokens')) {
            Schema::create('fcm_tokens', function (Blueprint $table) {
                $table->id();
                $table->string('user_phone')->index();
                $table->text('fcm_token');
                $table->string('device_type')->default('android');
                $table->timestamps();
            });
        }

        // 3. Notification Audit Logs Table
        if (!Schema::hasTable('notification_logs')) {
            Schema::create('notification_logs', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->text('body');
                $table->text('image_url')->nullable();
                $table->enum('target_type', ['all', 'specific_user'])->default('all');
                $table->string('target_phone')->nullable();
                $table->string('order_id')->nullable();
                $table->enum('status', ['sent', 'failed', 'queued'])->default('sent');
                $table->text('response_data')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_logs');
        Schema::dropIfExists('fcm_tokens');
        Schema::dropIfExists('fcm_settings');
    }
};
