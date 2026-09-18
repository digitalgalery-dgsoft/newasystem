<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. tb_kandidat
        if (!Schema::hasTable('tb_kandidat')) {
            Schema::create('tb_kandidat', function (Blueprint $table) {
                $table->integer('id')->primary();
                $table->date('tanggal')->nullable();
                $table->string('no_ktp', 50)->nullable()->index();
                $table->string('applicants_name', 150)->nullable()->index();
                $table->text('alamat_ktp')->nullable();
                $table->text('alamat_domisili')->nullable();
                $table->string('kota_lahir', 100)->nullable();
                $table->date('tanggal_lahir')->nullable();
                $table->text('height')->nullable();
                $table->text('weight')->nullable();
                $table->string('religion', 50)->nullable();
                $table->string('pendidikan_terakhir', 100)->nullable();
                $table->string('phone', 120)->nullable();
                $table->string('mobile', 100)->nullable();
                $table->string('nama_ibu_kandung', 150)->nullable();
                $table->string('contact_name', 150)->nullable();
                $table->string('contact_number', 50)->nullable();
                $table->string('emergency_relation', 50)->nullable();
                $table->string('area', 100)->nullable()->index();
                $table->string('secondary_city', 255)->nullable();
                $table->string('nama_as', 255)->nullable();
                $table->string('principle', 150)->nullable()->index();
                $table->string('applied_job', 150)->nullable()->index();
                $table->text('password')->nullable();
                $table->string('tes_kepribadian', 50)->nullable();
                $table->string('tes_matematika', 50)->nullable();
                $table->integer('tes_ke')->nullable();
                $table->string('tes_komputer', 50)->nullable();
                $table->string('status_kawin', 100)->nullable();
                $table->string('nama_suamiistri', 150)->nullable();
                $table->string('pekerjaan_suamiistri', 150)->nullable();
                $table->string('bank', 50)->nullable();
                $table->string('rekening', 50)->nullable();
                $table->string('atas_nama', 150)->nullable();
                $table->text('NPWP')->nullable();
                $table->integer('anak_ke')->nullable();
                $table->integer('jumlah_anak')->nullable();
                $table->decimal('gaji_terakhir', 15, 2)->nullable();
                $table->decimal('gaji_diminta', 15, 2)->nullable();
                $table->text('motivasi_kerja')->nullable();
                $table->text('kelebihan')->nullable();
                $table->text('kekurangan')->nullable();
                $table->text('kegiatan_sekarang')->nullable();
                $table->string('kendaraan', 50)->nullable();
                $table->string('sim', 50)->nullable();
                $table->text('keahlian_komputer')->nullable();
                $table->text('keahlian_bahasa_inggris')->nullable();
                $table->text('keahlian_lain')->nullable();
                $table->text('keahlian_lain_level')->nullable();
                $table->text('ttdfile')->nullable();
                $table->dateTime('time_ttd')->nullable();
                $table->text('pernyataan')->nullable();
                $table->string('status', 100)->nullable()->index();
                $table->text('idprinsiple')->nullable();
                $table->text('ttd_prinsiple')->nullable();
                $table->dateTime('time_prinsiple')->nullable();
                $table->text('note_prinsiple')->nullable();
                $table->text('status_approve')->nullable();
                $table->string('useras', 100)->nullable();
                $table->string('jenis', 100)->nullable();
                $table->text('info')->nullable();
                $table->text('undangan')->nullable();
                $table->text('alasanarsip')->nullable();
                $table->text('berkas_lamaran')->nullable();
                $table->text('catatan')->nullable();
                $table->text('fotoprofil')->nullable();
                $table->text('buktikomputer')->nullable();
                $table->text('filecv')->nullable();
                $table->text('status_kandidat')->nullable();
                $table->text('kategori_kandidat')->nullable();
                $table->text('kategori_industri')->nullable();
                $table->dateTime('waktukirim')->nullable();
                $table->tinyInteger('is_komputer')->default(1);
                $table->text('ai_cv_analysis')->nullable();
                $table->integer('ai_score')->default(0);
                $table->string('status_wa', 50)->default('Belum Dikirim');
                $table->text('wa_sent_text')->nullable();
                $table->dateTime('wa_sent_at')->nullable();
            });
        }

        // 2. tb_hasilpsikotes
        if (!Schema::hasTable('tb_hasilpsikotes')) {
            Schema::create('tb_hasilpsikotes', function (Blueprint $table) {
                $table->integer('id')->primary();
                $table->string('id_kandidat', 50)->index();
                $table->integer('id_soal')->index();
                $table->string('jawaban', 10);
                $table->string('waktu_pengerjaan', 30)->nullable();
                $table->timestamp('created_at')->nullable();
            });
        }

        // 3. tb_hasilmath
        if (!Schema::hasTable('tb_hasilmath')) {
            Schema::create('tb_hasilmath', function (Blueprint $table) {
                $table->integer('id')->primary();
                $table->integer('id_kandidat')->index();
                $table->integer('id_soal')->index();
                $table->text('jawaban');
                $table->string('waktu_pengerjaan', 30)->nullable();
                $table->integer('tes_ke')->default(1);
                $table->timestamp('created_at')->nullable();
            });
        }

        // 4. hasil_kompt
        if (!Schema::hasTable('hasil_kompt')) {
            Schema::create('hasil_kompt', function (Blueprint $table) {
                $table->integer('id')->primary();
                $table->string('id_kandidat', 50)->index();
                $table->string('nomor_ktp', 50)->index();
                $table->text('vlookup')->nullable();
                $table->text('hlookup')->nullable();
                $table->text('pivot')->nullable();
                $table->text('fungsiif')->nullable();
                $table->text('average')->nullable();
                $table->text('hitung')->nullable();
                $table->text('teliti')->nullable();
                $table->text('cepat')->nullable();
                $table->text('hasilkerja')->nullable();
            });
        }

        // 5. hasilinterview
        if (!Schema::hasTable('hasilinterview')) {
            Schema::create('hasilinterview', function (Blueprint $table) {
                $table->integer('id')->primary();
                $table->string('id_kandidat', 50)->index();
                $table->string('nomor_ktp', 50)->index();
                $table->string('kemauan_kerja', 50)->nullable();
                $table->string('penampilan', 50)->nullable();
                $table->string('attitude', 50)->nullable();
                $table->string('daya_tangkap', 50)->nullable();
                $table->text('catatan_lain')->nullable();
                $table->string('email', 100)->nullable();
                $table->integer('salary')->nullable();
                $table->string('penempatan', 100)->nullable();
                $table->date('tanggal')->nullable();
                $table->text('ttd_rekrutor')->nullable();
                $table->dateTime('time_ttdrek')->nullable();
                $table->text('ttd_as')->nullable();
                $table->dateTime('time_ttdas')->nullable();
                $table->text('nama_as')->nullable();
                $table->text('kategori')->nullable();
            });
        }

        // 6. tb_pengalaman
        if (!Schema::hasTable('tb_pengalaman')) {
            Schema::create('tb_pengalaman', function (Blueprint $table) {
                $table->integer('id')->primary();
                $table->string('id_kandidat', 50)->index();
                $table->string('nomor_ktp', 50)->index();
                $table->string('nama_perusahaan', 255)->nullable();
                $table->string('telp_perusahaan', 50)->nullable();
                $table->string('jabatan', 150)->nullable();
                $table->date('tgl_masuk')->nullable();
                $table->date('tgl_keluar')->nullable();
                $table->text('alasan_keluar')->nullable();
                $table->string('spv', 255)->nullable();
                $table->text('performa')->nullable();
                $table->text('disiplin')->nullable();
                $table->text('tanggungjawab')->nullable();
                $table->text('masalah')->nullable();
                $table->text('streng')->nullable();
                $table->text('week')->nullable();
                $table->timestamp('created_at')->nullable();
                $table->date('tanggal')->nullable();
                $table->text('file_cek')->nullable();
                $table->text('ttduser')->nullable();
            });
        }

        // 7. tb_kepribadian (Bank Soal DISC)
        if (!Schema::hasTable('tb_kepribadian')) {
            Schema::create('tb_kepribadian', function (Blueprint $table) {
                $table->integer('id')->primary();
                $table->text('pilihan_a')->nullable();
                $table->text('pilihan_b')->nullable();
                $table->text('pilihan_c')->nullable();
                $table->text('pilihan_d')->nullable();
            });
        }

        // 8. tb_math (Bank Soal Matematika)
        if (!Schema::hasTable('tb_math')) {
            Schema::create('tb_math', function (Blueprint $table) {
                $table->integer('id')->primary();
                $table->string('question_type', 50)->default('multiple_choice');
                $table->text('question_text');
                $table->text('choices')->nullable();
                $table->string('correct_answer', 255);
                $table->timestamp('created_at')->nullable();
            });
        }

        // 9. userprinsiple
        if (!Schema::hasTable('userprinsiple')) {
            Schema::create('userprinsiple', function (Blueprint $table) {
                $table->integer('id')->primary();
                $table->string('nama_lengkap', 255);
                $table->string('jabatan', 150)->nullable();
                $table->string('prinsiple', 150)->nullable()->index();
                $table->string('email', 255)->nullable()->index();
                $table->string('no_wa', 50)->nullable();
                $table->text('katakunci')->nullable();
                $table->text('area')->nullable();
                $table->string('status', 50)->default('Aktiv');
                $table->dateTime('created_at')->nullable();
            });
        }

        // 10. tb_catataninhouse
        if (!Schema::hasTable('tb_catataninhouse')) {
            Schema::create('tb_catataninhouse', function (Blueprint $table) {
                $table->integer('id')->primary();
                $table->string('id_kandidat', 50)->index();
                $table->text('nama_approver')->nullable();
                $table->text('jabatan_aprover')->nullable();
                $table->text('catatan_approver')->nullable();
                $table->string('status', 50)->nullable();
                $table->text('ttd_approver')->nullable();
                $table->dateTime('time_approver')->nullable();
            });
        }

        // 11. tb_replace
        if (!Schema::hasTable('tb_replace')) {
            Schema::create('tb_replace', function (Blueprint $table) {
                $table->integer('id')->primary();
                $table->string('id_kandidat', 50)->index();
                $table->string('status', 50)->nullable();
                $table->text('menggantikan')->nullable();
                $table->date('tgl_resign')->nullable();
                $table->text('alasan_resign')->nullable();
            });
        }

        // 12. tb_jabatan
        if (!Schema::hasTable('tb_jabatan')) {
            Schema::create('tb_jabatan', function (Blueprint $table) {
                $table->integer('id')->primary();
                $table->text('nama_jabatan');
            });
        }

        // 13. tb_ai_setting
        if (!Schema::hasTable('tb_ai_setting')) {
            Schema::create('tb_ai_setting', function (Blueprint $table) {
                $table->integer('id')->primary();
                $table->text('gemini_keys')->nullable();
                $table->string('gemini_model', 100)->default('gemini-2.5-flash');
                $table->string('sumopod_key', 255)->nullable();
                $table->string('sumopod_model', 100)->default('gpt-4o-mini');
                $table->string('wa_api_key', 255)->nullable();
                $table->string('wa_device', 50)->nullable();
                $table->text('wa_template')->nullable();
            });
        }

        // 14. tb_ai_setting_user
        if (!Schema::hasTable('tb_ai_setting_user')) {
            Schema::create('tb_ai_setting_user', function (Blueprint $table) {
                $table->string('email', 255)->primary();
                $table->string('sumopod_key', 255)->nullable();
                $table->string('sumopod_model', 100)->nullable();
                $table->string('wa_device', 50)->nullable();
                $table->text('wa_template')->nullable();
                $table->tinyInteger('wa_use_pusat')->default(1);
                $table->string('area', 100)->nullable();
            });
        }

        // 15. tb_wa_area_setting
        if (!Schema::hasTable('tb_wa_area_setting')) {
            Schema::create('tb_wa_area_setting', function (Blueprint $table) {
                $table->string('area', 100)->primary();
                $table->text('wa_template')->nullable();
                $table->string('locked_by_email', 255)->nullable();
                $table->string('locked_by_nama', 255)->nullable();
                $table->dateTime('updated_at')->nullable();
            });
        }

        // 16. tb_log
        if (!Schema::hasTable('tb_log')) {
            Schema::create('tb_log', function (Blueprint $table) {
                $table->integer('kode')->primary();
                $table->text('kode_validasi')->nullable();
                $table->string('pengguna', 100)->nullable();
                $table->string('aktivitas', 500)->nullable();
                $table->date('tanggal')->nullable();
                $table->string('ip', 50)->nullable();
                $table->text('jenisbrowser')->nullable();
                $table->text('sistem_operasi')->nullable();
                $table->text('perangkat')->nullable();
                $table->timestamp('waktu')->nullable();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('tb_log');
        Schema::dropIfExists('tb_wa_area_setting');
        Schema::dropIfExists('tb_ai_setting_user');
        Schema::dropIfExists('tb_ai_setting');
        Schema::dropIfExists('tb_jabatan');
        Schema::dropIfExists('tb_replace');
        Schema::dropIfExists('tb_catataninhouse');
        Schema::dropIfExists('userprinsiple');
        Schema::dropIfExists('tb_math');
        Schema::dropIfExists('tb_kepribadian');
        Schema::dropIfExists('tb_pengalaman');
        Schema::dropIfExists('hasilinterview');
        Schema::dropIfExists('hasil_kompt');
        Schema::dropIfExists('tb_hasilmath');
        Schema::dropIfExists('tb_hasilpsikotes');
        Schema::dropIfExists('tb_kandidat');
    }
};
