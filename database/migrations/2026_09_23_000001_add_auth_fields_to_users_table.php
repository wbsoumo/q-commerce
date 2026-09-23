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
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'phone')) {
                $table->string('phone')->nullable()->unique()->after('email');
            }
            if (!Schema::hasColumn('users', 'device_info')) {
                $table->text('device_info')->nullable()->after('password');
            }
            if (!Schema::hasColumn('users', 'ip_address')) {
                $table->string('ip_address', 45)->nullable()->after('device_info');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $columns = [];
            if (Schema::hasColumn('users', 'phone')) $columns[] = 'phone';
            if (Schema::hasColumn('users', 'device_info')) $columns[] = 'device_info';
            if (Schema::hasColumn('users', 'ip_address')) $columns[] = 'ip_address';
            if (!empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};
