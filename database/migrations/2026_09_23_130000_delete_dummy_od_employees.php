<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('employees')
            ->where('nik', 'like', 'OD-%')
            ->orWhereNull('nik')
            ->orWhere('nik', '')
            ->delete();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No reverse needed for deleted dummy system accounts
    }
};
