<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Tabel Roles
        if (!Schema::hasTable('roles')) {
            Schema::create('roles', function (Blueprint $table) {
                $table->id();
                $table->string('name')->unique();
                $table->string('display_name');
                $table->text('description')->nullable();
                $table->boolean('is_system')->default(true);
                $table->timestamps();
            });
        }

        // 2. Tabel Permissions
        if (!Schema::hasTable('permissions')) {
            Schema::create('permissions', function (Blueprint $table) {
                $table->id();
                $table->string('name')->unique();
                $table->string('display_name');
                $table->string('module')->index();
                $table->text('description')->nullable();
                $table->timestamps();
            });
        }

        // 3. Tabel Pivot Role Permissions (Matriks Perizinan Per Role)
        if (!Schema::hasTable('role_permissions')) {
            Schema::create('role_permissions', function (Blueprint $table) {
                $table->foreignId('role_id')->constrained('roles')->onDelete('cascade');
                $table->foreignId('permission_id')->constrained('permissions')->onDelete('cascade');
                $table->primary(['role_id', 'permission_id']);
            });
        }

        // 4. Tabel Pivot User Permissions (Override Perizinan Spesifik Per User)
        if (!Schema::hasTable('user_permissions')) {
            Schema::create('user_permissions', function (Blueprint $table) {
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('permission_id')->constrained('permissions')->onDelete('cascade');
                $table->boolean('is_granted')->default(true); // true = allow, false = explicit deny
                $table->primary(['user_id', 'permission_id']);
            });
        }

        // 5. Seed Default Roles
        $rolesData = [
            [
                'name' => 'admin',
                'display_name' => 'Administrator HR',
                'description' => 'Akses penuh ke seluruh modul sistem, pengaturan data master, sinkronisasi Odoo, dan manajemen hak akses RBAC.',
                'is_system' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'recruiter',
                'display_name' => 'Tim Rekruter & AS',
                'description' => 'Akses ke talent pool kandidat, penilaian interview, referensi kerja, portal lowongan kerja, dan CBT evaluasi.',
                'is_system' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'head_hr',
                'display_name' => 'Head of HR',
                'description' => 'Akses tingkat pimpinan ke proses approval kandidat inhouse, laporan evaluasi, perankingan AI, dan rekrutmen.',
                'is_system' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'karyawan_inhouse',
                'display_name' => 'Karyawan Inhouse',
                'description' => 'Akses portal karyawan internal untuk melihat profil pribadi, riwayat kerja, dan layanan internal ESA Groups.',
                'is_system' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'karyawan_ratecard',
                'display_name' => 'Karyawan RateCard',
                'description' => 'Akses terbatas untuk karyawan proyek/lapangan yang telah diizinkan login oleh administrator.',
                'is_system' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($rolesData as $role) {
            DB::table('roles')->updateOrInsert(['name' => $role['name']], $role);
        }

        // 6. Seed Default Permissions Catalog
        $permissionsData = [
            // Module: Master Data
            [
                'name' => 'master.karyawan',
                'display_name' => 'Master Data Karyawan',
                'module' => 'Master Data',
                'description' => 'Melihat, menambah, mengedit data karyawan inhouse dan ratecard, serta mengatur login access karyawan.',
            ],
            [
                'name' => 'master.prinsiple',
                'display_name' => 'Master Data Prinsiple',
                'module' => 'Master Data',
                'description' => 'Melihat, menambah, mengedit, dan menghapus master data prinsiple untuk 5 entitas (AMK, AKP, ATK, ABO, ATB).',
            ],
            [
                'name' => 'master.math',
                'display_name' => 'Master Soal Matematika',
                'module' => 'Master Data',
                'description' => 'Mengelola bank soal matematika CBT dan mengatur status aktif/nonaktif butir soal.',
            ],
            [
                'name' => 'master.personality',
                'display_name' => 'Master Soal Kepribadian',
                'module' => 'Master Data',
                'description' => 'Mengelola 40 butir pertanyaan profil kepribadian DISC / Florence Littauer.',
            ],

            // Module: System Setting
            [
                'name' => 'odoo.sync',
                'display_name' => 'Sinkronisasi Odoo ERP',
                'module' => 'System Setting',
                'description' => 'Mengakses halaman konfigurasi Odoo, uji koneksi, dan menjalankan sinkronisasi data 5 entitas secara manual.',
            ],
            [
                'name' => 'rbac.manage',
                'display_name' => 'Manajemen Hak Akses (RBAC)',
                'module' => 'System Setting',
                'description' => 'Mengatur matriks hak akses per role, menetapkan role pengguna, dan konfigurasi perizinan individual.',
            ],
            [
                'name' => 'ai.setting',
                'display_name' => 'Setting AI & WhatsApp Gateway',
                'module' => 'System Setting',
                'description' => 'Mengatur kunci API Google Gemini AI, prompt sistem, dan kredensial WhatsApp Gateway.',
            ],

            // Module: Rekrutmen & Interview
            [
                'name' => 'interview.view',
                'display_name' => 'Akses Talent Pool Interview',
                'module' => 'Rekrutmen & Interview',
                'description' => 'Melihat data kandidat masuk, hasil tes online CBT, dan membuka halaman evaluasi interview.',
            ],
            [
                'name' => 'interview.assess',
                'display_name' => 'Penilaian & Catatan Rekrutmen',
                'module' => 'Rekrutmen & Interview',
                'description' => 'Memberikan nilai interview, catatan rekruter, refcek, dan mengirim remidi tes matematika.',
            ],
            [
                'name' => 'interview.inhouse',
                'display_name' => 'Kandidat Inhouse & Approval',
                'module' => 'Rekrutmen & Interview',
                'description' => 'Mengakses portal kandidat inhouse, persetujuan penempatan, dan unduh berkas approval.',
            ],
            [
                'name' => 'user.prinsiple',
                'display_name' => 'Master User Prinsiple',
                'module' => 'Rekrutmen & Interview',
                'description' => 'Menambahkan user prinsiple, mengelola akses verifikasi user, dan mengirim link portal prinsiple.',
            ],

            // Module: Fitur Pendukung & Laporan
            [
                'name' => 'ai.ranking',
                'display_name' => 'Fitur AI Candidate Ranking',
                'module' => 'AI & Laporan',
                'description' => 'Mengakses evaluasi perangkingan kandidat otomatis berbasis kecerdasan buatan Gemini AI.',
            ],
            [
                'name' => 'job.manage',
                'display_name' => 'Manajemen Lowongan Kerja',
                'module' => 'Portal Lowongan',
                'description' => 'Membuat, mengedit, mempublikasikan lowongan kerja ke portal publik, dan mengelola pelamar.',
            ],
            [
                'name' => 'export.data',
                'display_name' => 'Export Data ke Excel & PDF',
                'module' => 'AI & Laporan',
                'description' => 'Mengekspor data kandidat rekrutmen dan data karyawan inhouse ke file XLSX dan dokumen PDF resmi.',
            ],
        ];

        foreach ($permissionsData as $perm) {
            $perm['created_at'] = now();
            $perm['updated_at'] = now();
            DB::table('permissions')->updateOrInsert(['name' => $perm['name']], $perm);
        }

        // 7. Seed Initial Role-Permission Relationships
        $roles = DB::table('roles')->get()->keyBy('name');
        $perms = DB::table('permissions')->get()->keyBy('name');

        // A. Admin: Diberikan seluruh permissions
        if (isset($roles['admin'])) {
            $adminId = $roles['admin']->id;
            foreach ($perms as $p) {
                DB::table('role_permissions')->updateOrInsert([
                    'role_id' => $adminId,
                    'permission_id' => $p->id,
                ]);
            }
        }

        // B. Recruiter: Rekrutmen, Interview, User Prinsiple, Job Manage, AI Ranking, Export
        if (isset($roles['recruiter'])) {
            $recruiterId = $roles['recruiter']->id;
            $recruiterPerms = [
                'interview.view',
                'interview.assess',
                'user.prinsiple',
                'job.manage',
                'ai.ranking',
                'export.data',
            ];
            foreach ($recruiterPerms as $pName) {
                if (isset($perms[$pName])) {
                    DB::table('role_permissions')->updateOrInsert([
                        'role_id' => $recruiterId,
                        'permission_id' => $perms[$pName]->id,
                    ]);
                }
            }
        }

        // C. Head HR: Interview view & assess, Inhouse approval, AI ranking, Export
        if (isset($roles['head_hr'])) {
            $headHrId = $roles['head_hr']->id;
            $headHrPerms = [
                'interview.view',
                'interview.assess',
                'interview.inhouse',
                'ai.ranking',
                'export.data',
            ];
            foreach ($headHrPerms as $pName) {
                if (isset($perms[$pName])) {
                    DB::table('role_permissions')->updateOrInsert([
                        'role_id' => $headHrId,
                        'permission_id' => $perms[$pName]->id,
                    ]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_permissions');
        Schema::dropIfExists('role_permissions');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('roles');
    }
};
