<?php

namespace Database\Seeders;

use App\Models\Candidate;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CbtCandidateSeeder extends Seeder
{
    public function run(): void
    {
        $candidates = [
            [
                'nik' => '3578014508980002',
                'full_name' => 'Nabila Putri Santika',
                'birth_date' => '1998-08-15',
                'birth_place' => 'Surabaya',
                'gender' => 'Perempuan',
                'religion' => 'Islam',
                'education' => 'SMA / SMK',
                'phone' => '085712345678',
                'whatsapp' => '085712345678',
                'email' => 'nabila.putri98@gmail.com',
                'address_ktp' => 'Jl. Rungkut Asri No. 45, Surabaya',
                'address_domicile' => 'Jl. Rungkut Asri No. 45, Surabaya',
                'marital_status' => 'Belum Menikah',
                'expected_salary' => 4800000,
                'last_salary' => 4200000,
                'work_motivation' => 'Ingin mengembangkan karir dan memberikan kontribusi terbaik bagi perusahaan.',
                'status' => 'new',
                'source_type' => 'job_portal',
                'applied_job' => 'Sales Promotion Girl (SPG)',
                'area' => 'Surabaya',
                'principle_id' => null,
                'password' => Hash::make('15081998'),
                'is_profile_complete' => false,
                'is_komputer' => 1,
            ],
            [
                'nik' => '3171012304950001',
                'full_name' => 'Ahmad Faisal Rahman',
                'birth_date' => '1995-04-23',
                'birth_place' => 'Jakarta',
                'gender' => 'Laki-laki',
                'religion' => 'Islam',
                'education' => 'S1',
                'phone' => '081234567891',
                'whatsapp' => '081234567891',
                'email' => 'faisal.rahman@gmail.com',
                'address_ktp' => 'Jl. Kebon Jeruk No. 12, Jakarta Barat',
                'address_domicile' => 'Jl. Kebon Jeruk No. 12, Jakarta Barat',
                'marital_status' => 'Belum Menikah',
                'expected_salary' => 5500000,
                'last_salary' => 5000000,
                'work_motivation' => 'Memiliki dedikasi tinggi dalam pencapaian target dan kerja sama tim.',
                'status' => 'new',
                'source_type' => 'walk_in',
                'applied_job' => 'Team Leader Promotor',
                'area' => 'Jakarta',
                'principle_id' => null,
                'password' => Hash::make('23041995'),
                'is_profile_complete' => false,
                'is_komputer' => 1,
            ],
        ];

        foreach ($candidates as $cData) {
            Candidate::updateOrCreate(
                ['nik' => $cData['nik']],
                $cData
            );
        }
    }
}
