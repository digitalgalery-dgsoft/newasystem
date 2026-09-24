<?php

namespace Database\Seeders;

use App\Models\HelpdeskDivision;
use App\Models\HelpdeskTicketTemplate;
use Illuminate\Database\Seeder;

class HelpdeskTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $itDiv = HelpdeskDivision::where('name', 'like', '%IT%')->first();
        $hrDiv = HelpdeskDivision::where('name', 'like', '%HRD%')->orWhere('name', 'like', '%Personalia%')->first();
        $finDiv = HelpdeskDivision::where('name', 'like', '%Keuangan%')->orWhere('name', 'like', '%Finance%')->orWhere('name', 'like', '%Payroll%')->first();
        $gaDiv = HelpdeskDivision::where('name', 'like', '%General%')->orWhere('name', 'like', '%GA%')->first();
        $opsDiv = HelpdeskDivision::where('name', 'like', '%Operasional%')->orWhere('name', 'like', '%OPS%')->first();

        $templates = [
            // --- IT SUPPORT ---
            [
                'division_id' => $itDiv?->id,
                'title' => 'Permohonan Reset Password Akun / Email',
                'subject' => '[Permohonan IT] Reset Password Akun Email / Sistem ASystem',
                'message' => "[FORMAT LAPORAN KENDALA AKUN / PASSWORD]\n" .
                             "Nama Pengguna: \n" .
                             "NIK Karyawan: \n" .
                             "Akun yang Ingin Direset: [Email Kantor / Akun ASystem / Akun Odoo]\n" .
                             "Alamat Email Terkait: \n" .
                             "Alasan Permohonan: [Lupa Password / Terkunci / Perangkat Hilang]\n" .
                             "Nomor WhatsApp Aktif: ",
                'order_num' => 1,
            ],
            [
                'division_id' => $itDiv?->id,
                'title' => 'Kendala Hardware, PC & Jaringan Kantor',
                'subject' => '[Kendala IT] Perbaikan / Pengecekan Hardware & Konektivitas',
                'message' => "[FORMAT LAPORAN KENDALA PERANGKAT & JARINGAN]\n" .
                             "Perangkat yang Mengalami Kendala: [Laptop / PC / Printer / Koneksi WiFi]\n" .
                             "Lokasi / Lantai / Ruangan: \n" .
                             "Nama Pengguna Perangkat: \n" .
                             "Uraian Kendala yang Dialami: \n" .
                             "Pesan Error yang Muncul di Layar: \n" .
                             "Langkah Perbaikan yang Sudah Dicoba: ",
                'order_num' => 2,
            ],
            [
                'division_id' => $itDiv?->id,
                'title' => 'Laporan Error / Bug Aplikasi ASystem Portal',
                'subject' => '[Bug Report] Laporan Kendala Fitur Portal ASystem',
                'message' => "[FORMAT LAPORAN BUG / KENDALA PORTAL]\n" .
                             "Menu / Halaman yang Error: \n" .
                             "URL Halaman: \n" .
                             "Pesan Error yang Tampil: \n" .
                             "Langkah-langkah Mereproduksi Error: \n" .
                             "1. \n" .
                             "2. \n" .
                             "Hasil yang Diharapkan: \n" .
                             "Catatan: Harap lampirkan tangkapan layar (screenshot) pesan error pada form lampiran di bawah.",
                'order_num' => 3,
            ],

            // --- HRD & PERSONALIA ---
            [
                'division_id' => $hrDiv?->id,
                'title' => 'Permohonan Surat Keterangan Kerja (Paklaring / SK Kerja)',
                'subject' => '[Permohonan HRD] Pengajuan Surat Keterangan Kerja (SK / Paklaring)',
                'message' => "[FORMAT PENGAJUAN SURAT KETERANGAN KERJA]\n" .
                             "Nama Lengkap: \n" .
                             "NIK: \n" .
                             "Jabatan Saat Ini: \n" .
                             "Divisi / Penempatan: \n" .
                             "Tanggal Mulai Bekerja: \n" .
                             "Tujuan Pembuatan Surat: [Pembuatan Rekening / Pengajuan KPR / Keperluan Visa / Lainnya]\n" .
                             "Kebutuhan Format Surat: [Bahasa Indonesia / Bahasa Inggris]\n" .
                             "Tanggal Dibutuhkan: ",
                'order_num' => 4,
            ],
            [
                'division_id' => $hrDiv?->id,
                'title' => 'Pertanyaan & Kendala Slip Gaji / Data Karyawan',
                'subject' => '[HRD Personalia] Klarifikasi Data Karyawan / Slip Gaji',
                'message' => "[FORMAT KLARIFIKASI DATA PERSONALIA]\n" .
                             "Nama Karyawan: \n" .
                             "NIK: \n" .
                             "Periode Penggajian / Bulan: \n" .
                             "Rincian yang Ingin Diklarifikasi: \n" .
                             "Nomor Rekening Terdaftar: \n" .
                             "Nomor WhatsApp: ",
                'order_num' => 5,
            ],

            // --- KEUANGAN & PAYROLL ---
            [
                'division_id' => $finDiv?->id,
                'title' => 'Pengajuan Klaim Operasional & Reimbursement',
                'subject' => '[Finance] Pengajuan Klaim Biaya Operasional / Reimbursement',
                'message' => "[FORMAT PENGAJUAN KLAIM / REIMBURSEMENT]\n" .
                             "Nama Pengaju: \n" .
                             "NIK & Divisi: \n" .
                             "Total Biaya yang Diklaim: Rp. \n" .
                             "Keperluan Pengeluaran: \n" .
                             "Tanggal Pengeluaran: \n" .
                             "Nomor Rekening Pencairan: [Bank, No Rekening, Atas Nama]\n" .
                             "Catatan: Seluruh kuitansi/bukti transfer wajib diunggah pada lampiran tiket.",
                'order_num' => 6,
            ],

            // --- GENERAL AFFAIRS (GA) ---
            [
                'division_id' => $gaDiv?->id,
                'title' => 'Permintaan ATK & Pengadaan Fasilitas Kantor',
                'subject' => '[GA Fasilitas] Permintaan Kebutuhan Alat Tulis Kantor & Sarana',
                'message' => "[FORMAT PERMINTAAN FASILITAS / ATK]\n" .
                             "Divisi Pemohon: \n" .
                             "Daftar Barang yang Dibutuhkan: \n" .
                             "1. [Nama Barang] - Jumlah: [Qty]\n" .
                             "2. [Nama Barang] - Jumlah: [Qty]\n" .
                             "Alasan Kebutuhan: \n" .
                             "Tanggal Kebutuhan Barang: ",
                'order_num' => 7,
            ],

            // --- OPERASIONAL ---
            [
                'division_id' => $opsDiv?->id,
                'title' => 'Laporan Kendala Operasional Mitra / Prinsiple',
                'subject' => '[OPS] Laporan Kendala Penempatan Lapangan / Mitra Prinsiple',
                'message' => "[FORMAT LAPORAN KENDALA OPERASIONAL LAPANGAN]\n" .
                             "Nama Mitra / Prinsiple: \n" .
                             "Area / Kota Penempatan: \n" .
                             "Jumlah Personil Terkait: \n" .
                             "Kronologi Permasalahan di Lapangan: \n" .
                             "Tindakan Sementara yang Telah Diambil: \n" .
                             "Dukungan yang Diharapkan dari Kantor Pusat: ",
                'order_num' => 8,
            ],
        ];

        foreach ($templates as $t) {
            HelpdeskTicketTemplate::updateOrCreate(
                ['title' => $t['title']],
                $t
            );
        }
    }
}
