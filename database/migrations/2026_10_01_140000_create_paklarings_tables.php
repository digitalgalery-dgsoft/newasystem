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
        if (!Schema::hasTable('paklarings')) {
            Schema::create('paklarings', function (Blueprint $table) {
                $table->id();
                $table->string('kode_validasi', 16)->unique();
                $table->string('kode_validasiref', 16)->nullable();
                $table->string('nik', 16)->index();
                $table->string('nama_lengkap', 150);
                $table->string('tempat_lahir', 100)->nullable();
                $table->date('tgl_lahir')->nullable();
                $table->string('jenis_kelamin', 20)->nullable();
                $table->text('alamat')->nullable();
                
                // Penempatan & Pekerjaan
                $table->string('kantor', 100)->nullable(); // Entitas inhouse e.g. AMK, AKP, ATK, ABO, ATB
                $table->string('prinsiple', 150)->index();
                $table->string('area', 100)->index();
                $table->string('region', 100)->nullable();
                $table->string('jabatan', 150);
                $table->string('no_hp', 30);
                $table->string('email', 150);
                $table->string('alasan', 255)->default('Mengundurkan Diri');
                $table->date('tgl_masuk')->nullable();
                $table->date('tgl_keluar')->nullable();

                // Rekening & Deposit (Pencairan Deposit jika ada)
                $table->string('status_deposit', 10)->nullable(); // 'Ya' atau null
                $table->string('nomor_rekening', 50)->nullable();
                $table->string('bank', 50)->nullable();
                $table->string('nama_rekening', 150)->nullable();
                $table->decimal('jml_deposit', 15, 2)->nullable();
                $table->string('kebank', 50)->nullable();
                $table->string('kerekening', 50)->nullable();
                $table->date('tanggaldeposit')->nullable();

                // Lampiran Berkas / Dokumen
                $table->string('foto_ktp', 255)->nullable();
                $table->string('form_request', 255)->nullable();
                $table->string('exit_cl', 255)->nullable();
                $table->string('pengunduran_diri', 255)->nullable();
                $table->string('kartu_bpjs', 255)->nullable();
                $table->string('serah_terima', 255)->nullable();
                $table->string('surat_cl', 255)->nullable(); // Dokumen pernyataan khusus Loreal
                $table->string('buku_tabungan', 255)->nullable();
                $table->string('bukti_deposit', 255)->nullable();

                // Pengiriman Dokumen Fisik & Penomoran Resmi
                $table->date('tgl_kirimsurat')->nullable();
                $table->string('xpdc', 100)->nullable(); // Ekspedisi (JNE, J&T, SiCepat, dll)
                $table->string('noresi', 100)->nullable();
                $table->string('nomor_ref', 100)->nullable(); // Nomor Surat Referensi Resmi
                $table->integer('nomor_urutref')->nullable();

                // Workflow & Tahapan Approval
                $table->string('status_bagian', 50)->default('Area')->index(); // Area, HRD, DB, BPJS, Selesai
                $table->string('status', 50)->default('Proses')->index(); // Proses, Selesai, Tolak, HOLD
                $table->unsignedInteger('current_step_order')->default(1);
                
                // Catatan per Bagian
                $table->text('catatan_aro')->nullable();
                $table->text('catatan_hrd')->nullable();
                $table->text('catatan_db')->nullable();
                $table->text('catatan_bpjs')->nullable();
                $table->text('alasan_penolakan')->nullable();

                // Durasi Pengerjaan
                $table->string('lama_aro', 100)->nullable();
                $table->string('lama_hrd', 100)->nullable();
                $table->string('lama_db', 100)->nullable();
                $table->string('lama_bpjs', 100)->nullable();

                // Metadata & Audit
                $table->string('pengguna', 150)->nullable(); // User terakhir yang memproses
                $table->string('ttd_digital', 255)->nullable(); // TTD Penerima Area
                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamp('waktu_input')->nullable();
                $table->string('ip', 45)->nullable();
                $table->text('user_agent')->nullable();

                $table->timestamps();
            });
        }

        if (!Schema::hasTable('paklaring_approvals')) {
            Schema::create('paklaring_approvals', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('paklaring_id')->index();
                $table->unsignedBigInteger('step_id')->nullable()->index();
                $table->unsignedInteger('step_order');
                $table->string('step_name', 100);
                $table->unsignedBigInteger('user_id')->nullable()->index();
                $table->string('user_name', 150);
                $table->string('user_email', 150)->nullable();
                $table->string('user_role', 50)->nullable();
                $table->string('action', 50); // approved, rejected, hold, returned, edited
                $table->string('action_to', 50)->nullable(); // misal HRD ke DB atau BPJS
                $table->text('notes')->nullable();
                $table->json('data_changes')->nullable();
                $table->string('ip_address', 45)->nullable();
                $table->timestamps();

                $table->foreign('paklaring_id')->references('id')->on('paklarings')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('paklaring_approvals');
        Schema::dropIfExists('paklarings');
    }
};
