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
        if (!Schema::hasColumn('users', 'notifications_read_at')) {
            Schema::table('users', function (Blueprint $table) {
                $table->timestamp('notifications_read_at')->nullable()->after('email_aliases');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('users', 'notifications_read_at')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('notifications_read_at');
            });
        }
    }
};
