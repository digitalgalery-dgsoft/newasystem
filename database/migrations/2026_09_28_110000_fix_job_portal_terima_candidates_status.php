<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Candidate;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 11 Kandidat spesifik di portal pelamar baru yang terpengaruh riwayat lama / sinkronisasi Odoo masa lalu
        $targetNiks = [
            '3515145511940005', // an nisaa mulyono
            '3578154904950001', // Vivi Vidiyanti
            '3523156603010001', // LESTARI DWI WURYANI
            '3515045301940001', // Hikmah Aulia
            '3578061503850001', // Robin Muh mansyuri
            '3514086410940007', // Devi vransiskah
            '3208306407030004', // Tiara nurul insyani
            '3578044309770003', // Dayatika
            '3578192807950002', // HEHAN CITA NUSA
            '3578191802870001', // Ahsanul Yaum
            '3528044204980003', // Yayuk Dewi Utari
        ];

        Candidate::whereIn('nik', $targetNiks)
            ->where('jenis', 'Job Portal')
            ->update([
                'status_kandidat'   => 'Baru',
                'status'            => 'Active',
                'ttd_prinsiple'     => null,
                'idprinsiple'       => null,
                'status_approval'   => null,
                'time_prinsiple'    => null,
                'note_principle'    => null,
                'odoo_stage_name'   => null,
                'odoo_applicant_id' => null,
                'odoo_entity'       => null,
                'odoo_synced_at'    => now(),
            ]);

        // General cleanup: Jika ada pelamar baru di Job Portal (status_kandidat = 'Baru') tapi kolom ttd_prinsiple masih terisi berkas lama
        Candidate::where('jenis', 'Job Portal')
            ->where('status_kandidat', 'Baru')
            ->whereNotNull('ttd_prinsiple')
            ->where('ttd_prinsiple', '!=', '')
            ->update([
                'ttd_prinsiple'   => null,
                'idprinsiple'     => null,
                'status_approval' => null,
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Tidak perlu revert karena ini data sanitization
    }
};
