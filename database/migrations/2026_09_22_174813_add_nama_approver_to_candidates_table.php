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
        if (!Schema::hasColumn('candidates', 'nama_approver')) {
            Schema::table('candidates', function (Blueprint $table) {
                $table->string('nama_approver')->nullable()->after('status_approval');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('candidates', 'nama_approver')) {
            Schema::table('candidates', function (Blueprint $table) {
                $table->dropColumn('nama_approver');
            });
        }
    }
};
