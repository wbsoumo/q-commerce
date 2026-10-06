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
        if (!Schema::hasTable('support_tickets')) {
            Schema::create('support_tickets', function (Blueprint $table) {
                $table->id();
                $table->string('ticket_number')->unique()->index();
                $table->string('user_phone')->index();
                $table->string('user_name')->nullable();
                $table->unsignedBigInteger('store_id')->nullable()->index();
                $table->unsignedBigInteger('order_id')->nullable()->index();
                $table->string('order_number')->nullable();
                $table->string('category');
                $table->string('sub_category')->nullable();
                $table->text('description')->nullable();
                $table->enum('status', ['pending', 'in_progress', 'resolved', 'closed'])->default('pending');
                $table->text('admin_remarks')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('support_tickets');
    }
};
