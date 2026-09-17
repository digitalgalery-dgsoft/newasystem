<?php

namespace Database\Seeders;

use App\Models\Candidate;
use App\Models\Principle;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Default Users (Admin & Recruiter)
        $admin = User::firstOrCreate(
            ['email' => 'admin@asystem.co.id'],
            [
                'name' => 'Administrator HR',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'area' => 'Jakarta',
                'job_title' => 'Head of Recruitment',
                'phone' => '081234567890',
            ]
        );

        $recruiter = User::firstOrCreate(
            ['email' => 'recruiter@asystem.co.id'],
            [
                'name' => 'Recruiter Team',
                'password' => Hash::make('password'),
                'role' => 'recruiter',
                'area' => 'Surabaya',
                'job_title' => 'HR Recruiter',
                'phone' => '081987654321',
            ]
        );

        // 2. Create Principles / Clients
        $principles = [
            ['name' => 'PT ARINA MULTI KARYA', 'parent_company' => 'ARINA GROUP', 'pic_name' => 'Budi Santoso', 'pic_email' => 'pic.amk@arina.co.id'],
            ['name' => 'PT ALVA KARYA PERKASA', 'parent_company' => 'ALVA GROUP', 'pic_name' => 'Siti Rahma', 'pic_email' => 'pic.alva@alva.co.id'],
            ['name' => 'PT ANUGRAH TERPERCAYA KERJA', 'parent_company' => 'ATK GROUP', 'pic_name' => 'Hendro', 'pic_email' => 'pic.atk@atk.co.id'],
            ['name' => 'PT NESTLE INDONESIA', 'parent_company' => 'NESTLE', 'pic_name' => 'Agus Yulianto', 'pic_email' => 'hrd@nestle.com'],
            ['name' => 'PT UNILEVER INDONESIA', 'parent_company' => 'UNILEVER', 'pic_name' => 'Dewi Lestari', 'pic_email' => 'hrd@unilever.com'],
        ];

        foreach ($principles as $p) {
            Principle::firstOrCreate(['name' => $p['name']], $p);
        }

        $principleAMK = Principle::where('name', 'PT ARINA MULTI KARYA')->first();
        $principleNestle = Principle::where('name', 'PT NESTLE INDONESIA')->first();

        // 3. Create Sample Candidates
        $candidates = [
            [
                'nik' => '3171012304950001',
                'full_name' => 'Ahmad Faisal Rahman',
                'birth_date' => '1995-04-23',
                'birth_place' => 'Jakarta',
                'gender' => 'Laki-laki',
                'education' => 'S1',
                'phone' => '081234567891',
                'whatsapp' => '081234567891',
                'email' => 'faisal.rahman@gmail.com',
                'address_ktp' => 'Jl. Kebon Jeruk No. 12, Jakarta Barat',
                'marital_status' => 'Belum Menikah',
                'expected_salary' => 5500000,
                'status' => 'interview_process',
                'source_type' => 'walk_in',
                'applied_job' => 'Team Leader Promotor',
                'area' => 'Jakarta',
                'principle_id' => $principleAMK->id,
                'recruiter_id' => $admin->id,
            ],
            [
                'nik' => '3578014508980002',
                'full_name' => 'Nabila Putri Santika',
                'birth_date' => '1998-08-15',
                'birth_place' => 'Surabaya',
                'gender' => 'Perempuan',
                'education' => 'SMA / SMK',
                'phone' => '085712345678',
                'whatsapp' => '085712345678',
                'email' => 'nabila.putri98@gmail.com',
                'address_ktp' => 'Jl. Rungkut Asri No. 45, Surabaya',
                'marital_status' => 'Belum Menikah',
                'expected_salary' => 4800000,
                'status' => 'new',
                'source_type' => 'job_portal',
                'applied_job' => 'Sales Promotion Girl (SPG)',
                'area' => 'Surabaya',
                'principle_id' => $principleNestle->id,
                'recruiter_id' => $recruiter->id,
            ],
            [
                'nik' => '3374011210960003',
                'full_name' => 'Rizky Dwi Prasetyo',
                'birth_date' => '1996-10-12',
                'birth_place' => 'Semarang',
                'gender' => 'Laki-laki',
                'education' => 'D3',
                'phone' => '081398765432',
                'whatsapp' => '081398765432',
                'email' => 'rizky.prasetyo@gmail.com',
                'address_ktp' => 'Jl. Pandanaran No. 88, Semarang',
                'marital_status' => 'Menikah',
                'expected_salary' => 5000000,
                'status' => 'review_principle',
                'source_type' => 'walk_in',
                'applied_job' => 'Merchandiser (MD)',
                'area' => 'Semarang',
                'principle_id' => $principleAMK->id,
                'recruiter_id' => $admin->id,
            ],
        ];

        foreach ($candidates as $cData) {
            $cand = Candidate::firstOrCreate(['nik' => $cData['nik']], $cData);

            // Add test results
            $cand->testResults()->firstOrCreate(['test_type' => 'psychology'], ['score' => 85.50, 'duration_seconds' => 1200]);
            $cand->testResults()->firstOrCreate(['test_type' => 'math'], ['score' => 78.00, 'duration_seconds' => 900]);
            $cand->testResults()->firstOrCreate(['test_type' => 'computer'], ['score' => 92.00, 'duration_seconds' => 600]);

            // If in review principle, add approval token
            if ($cand->status === 'review_principle') {
                $cand->principleApproval()->firstOrCreate([
                    'candidate_id' => $cand->id,
                    'principle_id' => $cand->principle_id,
                ], [
                    'status' => 'pending',
                ]);
            }
        }
    }
}