<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Staff Members Table with Multiple Roles (JSON array)
        Schema::create('staff_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_id')->nullable()->constrained('stores')->onDelete('cascade');
            $table->string('name');
            $table->string('phone')->unique();
            $table->string('email')->nullable();
            $table->json('roles'); // JSON array of roles: ['store_staff', 'delivery_staff', 'cleaning_staff', 'inventory_staff', 'billing_staff', 'security_staff']
            $table->enum('status', ['Active', 'Inactive', 'On Leave'])->default('Active');
            $table->string('profile_image')->nullable();
            $table->timestamps();
        });

        // 2. Roles Master Reference Table
        Schema::create('staff_roles', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('staff_members');
        Schema::dropIfExists('staff_roles');
    }
};
