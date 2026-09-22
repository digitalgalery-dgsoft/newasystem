<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Mengembalikan data kandidat interview yang sebelumnya menggunakan akun demo jamil@asystem.co.id ke user aslinya (admin/rekruter asli).
     */
    public function up(): void
    {
        // 1. Dapatkan user admin resmi sebagai default rekruter yang sah
        $adminUser = DB::table('users')->where('role', 'admin')->orderBy('id')->first();
        $adminId = $adminUser ? $adminUser->id : 1;
        $adminEmail = $adminUser ? $adminUser->email : 'admin@asystem.co.id';

        // 2. Kembalikan data kandidat yang memakai akun demo jamil@asystem.co.id ke user resmi
        DB::table('candidates')
            ->where('useras', 'jamil@asystem.co.id')
            ->orWhere('recruiter_id', 3)
            ->update([
                'useras' => $adminEmail,
                'recruiter_id' => $adminId,
                'updated_at' => now(),
            ]);

        // 3. Kembalikan nama_as pada record hasilinterview ID 524 ke user aslinya (Anton Purnama Wijaya)
        if (Schema::hasTable('hasilinterview')) {
            DB::table('hasilinterview')
                ->where('id', 524)
                ->where('nama_as', 'Abdurrahman Jamil')
                ->update([
                    'nama_as' => 'Anton Purnama Wijaya',
                ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Tidak perlu rollback perubahan data demo
    }
};
