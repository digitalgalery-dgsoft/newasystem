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
        // 1. Tabel Utama Surat Peringatan
        if (!Schema::hasTable('warning_letters')) {
            Schema::create('warning_letters', function (Blueprint $table) {
                $table->id();
                $table->string('nomor_surat', 100)->nullable()->unique();
                $table->unsignedInteger('nomor_urut')->nullable();
                $table->foreignId('employee_id')->nullable()->constrained('employees')->nullOnDelete();
                $table->string('nip', 50)->nullable();
                $table->string('nik', 50)->index();
                $table->string('nama_karyawan', 150);
                $table->string('jabatan', 100)->nullable();
                $table->string('area', 100)->nullable();
                $table->string('singkatan_area', 20)->nullable();
                $table->string('prinsiple', 150)->nullable();
                $table->string('entity', 20)->default('AMK'); // AMK, AKP, ATK, ABO, ATB
                $table->enum('tingkat_sp', ['sp1', 'sp2', 'sp3'])->default('sp1');
                $table->enum('tingkat_sp_diajukan', ['sp1', 'sp2', 'sp3'])->default('sp1');
                $table->date('tanggal_surat');
                $table->date('tanggal_expired'); // +6 bulan
                $table->text('pasal_pelanggaran')->nullable(); // Diinput oleh HRD saat approval
                $table->text('tindakan_perbaikan')->nullable();
                $table->enum('status', ['draft', 'review_head', 'review_hrd', 'approved', 'rejected', 'expired'])->default('review_hrd');
                $table->string('posisi_approval', 100)->default('HRD');
                $table->json('file_pendukung')->nullable(); // Bukti / BAP / Dokumen
                $table->string('file_ttd_karyawan', 255)->nullable(); // Scan berkas fisik bertandatangan
                $table->dateTime('tgl_upload_ttd')->nullable();
                $table->text('alasan_penolakan')->nullable();
                $table->foreignId('created_by')->constrained('users');
                $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
                $table->dateTime('approved_at')->nullable();
                $table->timestamps();
            });
        }

        // 2. Tabel Butir-butir Pelanggaran (Multiple Items)
        if (!Schema::hasTable('warning_letter_violations')) {
            Schema::create('warning_letter_violations', function (Blueprint $table) {
                $table->id();
                $table->foreignId('warning_letter_id')->constrained('warning_letters')->cascadeOnDelete();
                $table->date('tanggal_pelanggaran');
                $table->text('pelanggaran');
                $table->longText('kronologi')->nullable();
                $table->timestamps();
            });
        }

        // 3. Tabel Log Riwayat Approval / Workflow
        if (!Schema::hasTable('warning_letter_approvals')) {
            Schema::create('warning_letter_approvals', function (Blueprint $table) {
                $table->id();
                $table->foreignId('warning_letter_id')->constrained('warning_letters')->cascadeOnDelete();
                $table->string('stage', 50); // e.g. pimpinan_head, hrd
                $table->foreignId('user_id')->constrained('users');
                $table->enum('action', ['approve', 'reject', 'revise']);
                $table->string('perubahan_tingkat', 50)->nullable();
                $table->text('catatan')->nullable();
                $table->timestamps();
            });
        }

        // 4. Tabel Counter Nomor Surat Resmi per Entitas & Tahun
        if (!Schema::hasTable('warning_letter_counters')) {
            Schema::create('warning_letter_counters', function (Blueprint $table) {
                $table->id();
                $table->string('entity', 20); // AMK, AKP, ATK, ABO, ATB
                $table->unsignedSmallInteger('tahun');
                $table->unsignedInteger('last_number')->default(0);
                $table->timestamps();

                $table->unique(['entity', 'tahun']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warning_letter_approvals');
        Schema::dropIfExists('warning_letter_violations');
        Schema::dropIfExists('warning_letters');
        Schema::dropIfExists('warning_letter_counters');
    }
};
