<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Candidate;
use App\Models\WorkExperience;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CbtModuleTest extends TestCase
{
    use RefreshDatabase;
    private function getOrCreateCandidate(): Candidate
    {
        $candidate = Candidate::firstOrCreate(
            ['nik' => '3578014508980002'],
            [
                'full_name' => 'Nabila Putri Santika',
                'birth_date' => '1998-08-15',
                'birth_place' => 'Surabaya',
                'gender' => 'Perempuan',
                'education' => 'SMA / SMK',
                'phone' => '085712345678',
                'religion' => 'Islam',
                'address_ktp' => 'Jl. Rungkut Asri No. 45, Surabaya',
                'address_domicile' => 'Jl. Rungkut Asri No. 45, Surabaya',
                'marital_status' => 'Belum Menikah',
                'applied_job' => 'Sales Promotion Girl (SPG)',
                'area' => 'Surabaya',
                'height' => 165,
                'weight' => 50,
                'mother_name' => 'Siti Aminah',
                'emergency_contact_name' => 'Budi Santika',
                'emergency_contact_phone' => '081299887766',
                'emergency_contact_relation' => 'Orang Tua',
                'bank_name' => 'BCA',
                'bank_account_number' => '8880192831',
                'bank_account_holder' => 'Nabila Putri Santika',
                'work_motivation' => 'Ingin berkembang bersama ESA Groups',
                'strengths' => 'Komunikatif dan disiplin',
                'weaknesses' => 'Kurang sabar jika target lambat',
                'current_activity' => 'Mencari karir tetap',
                'vehicle' => 'Sepeda Motor Pribadi',
                'driving_license' => 'SIM C',
                'signature_path' => 'ttd_sample.png',
                'statement_agreed' => true,
            ]
        );

        WorkExperience::firstOrCreate(
            ['candidate_id' => $candidate->id, 'company_name' => 'PT Retail Nusantara'],
            [
                'position' => 'Promotor',
                'start_date' => '2023-01-01',
                'end_date' => '2024-01-01',
                'reason_for_leaving' => 'Habis kontrak'
            ]
        );

        $candidate->checkProfileCompleteness();
        return $candidate;
    }

    public function test_cbt_login_page_renders_successfully(): void
    {
        $response = $this->get('/cbt/login');
        $response->assertStatus(200);
        $response->assertSee('ESA GROUPS CBT');
    }

    public function test_unauthenticated_candidate_is_redirected(): void
    {
        $response = $this->get('/cbt');
        $response->assertRedirect('/cbt/login');
    }

    public function test_candidate_can_login_with_dob_default_password(): void
    {
        $candidate = $this->getOrCreateCandidate();
        $dobPassword = $candidate->birth_date->format('dmY'); // 15081998

        $response = $this->post('/cbt/login', [
            'nik' => $candidate->nik,
            'password' => $dobPassword,
        ]);

        $response->assertRedirect('/cbt');
        $this->assertEquals(session('cbt_candidate_id'), $candidate->id);
    }

    public function test_authenticated_candidate_can_access_dashboard_and_profile(): void
    {
        $candidate = $this->getOrCreateCandidate();

        $response = $this->withSession(['cbt_candidate_id' => $candidate->id])
                         ->get('/cbt');
        $response->assertStatus(200);
        $response->assertSee($candidate->full_name);

        $profileResponse = $this->withSession(['cbt_candidate_id' => $candidate->id])
                                ->get('/cbt/profile');
        $profileResponse->assertStatus(200);
        $profileResponse->assertSee('Formulir Kelengkapan Data Kandidat');
    }

    public function test_candidate_can_execute_personality_disc_test(): void
    {
        $candidate = $this->getOrCreateCandidate();

        $pageResponse = $this->withSession(['cbt_candidate_id' => $candidate->id])
                             ->get('/cbt/kepribadian');
        $pageResponse->assertStatus(200);
        $pageResponse->assertSee('Tes Kepribadian (DISC Assessment)');

        // Submit dummy answers for 24 questions
        $answers = ['timeElapsed' => 450];
        for ($i = 1; $i <= 24; $i++) {
            $answers["q{$i}"] = ['a', 'b', 'c', 'd'][($i % 4)];
        }

        $submitResponse = $this->withSession(['cbt_candidate_id' => $candidate->id])
                               ->post('/cbt/kepribadian', $answers);

        $submitResponse->assertRedirect(route('cbt.kepribadian.result'));

        $resultResponse = $this->withSession(['cbt_candidate_id' => $candidate->id])
                               ->get('/cbt/kepribadian/result');
        $resultResponse->assertStatus(200);
        $resultResponse->assertSee('Hasil Tes Kepribadian (DISC)');
    }

    public function test_candidate_can_execute_math_test(): void
    {
        $candidate = $this->getOrCreateCandidate();

        $pageResponse = $this->withSession(['cbt_candidate_id' => $candidate->id])
                             ->get('/cbt/matematika');
        $pageResponse->assertStatus(200);
        $pageResponse->assertSee('Tes Matematika');

        // Submit answers
        $answers = [
            'timeElapsed' => 320,
            'answers' => [
                1 => 'b',
                2 => '3',
                3 => 'a',
                4 => '128',
                5 => 'b',
                6 => '170',
                7 => 'c',
                8 => '20',
                9 => 'b',
                10 => '360'
            ]
        ];

        $submitResponse = $this->withSession(['cbt_candidate_id' => $candidate->id])
                               ->post('/cbt/matematika', $answers);

        $submitResponse->assertRedirect(route('cbt.matematika.result'));

        $resultResponse = $this->withSession(['cbt_candidate_id' => $candidate->id])
                               ->get('/cbt/matematika/result');
        $resultResponse->assertStatus(200);
        $resultResponse->assertSee('Hasil Tes Matematika');
        $resultResponse->assertSee('100'); // 10 correct = 100 points
    }

    public function test_candidate_can_update_profile_and_add_experience(): void
    {
        $candidate = $this->getOrCreateCandidate();

        // 1. Update Profile Tab Keuangan
        $profileResponse = $this->withSession(['cbt_candidate_id' => $candidate->id])
                                ->post('/cbt/profile', [
                                    'tab' => 'keuangan',
                                    'bank_name' => 'Bank Mandiri',
                                    'bank_account_number' => '1234567890',
                                    'bank_account_holder' => 'Nabila Putri Santika',
                                    'npwp' => '99.888.777.6-555.000',
                                    'last_salary' => 4500000,
                                    'expected_salary' => 5000000,
                                ]);

        $profileResponse->assertRedirect('/cbt/profile?tab=keuangan');

        $candidate->refresh();
        $this->assertEquals('Bank Mandiri', $candidate->bank_name);
        $this->assertEquals('1234567890', $candidate->bank_account_number);

        // 2. Add Experience
        $expResponse = $this->withSession(['cbt_candidate_id' => $candidate->id])
                            ->post('/cbt/experience', [
                                'company_name' => 'PT Mitra Sejahtera',
                                'position' => 'Team Leader Promotor',
                                'start_date' => '2021-01-01',
                                'end_date' => '2022-12-31',
                                'reason_for_leaving' => 'Melanjutkan pendidikan',
                            ]);

        $expResponse->assertRedirect('/cbt/profile?tab=pengalaman');
        $this->assertDatabaseHas('work_experiences', [
            'candidate_id' => $candidate->id,
            'company_name' => 'PT Mitra Sejahtera',
        ]);
    }

    public function test_candidate_can_execute_computer_test(): void
    {
        $candidate = $this->getOrCreateCandidate();

        $pageResponse = $this->withSession(['cbt_candidate_id' => $candidate->id])
                             ->get('/cbt/komputer');
        $pageResponse->assertStatus(200);
        $pageResponse->assertSee('Tes Praktik Komputer');

        // Fake image upload
        $file = \Illuminate\Http\UploadedFile::fake()->image('bukti_excel.png', 600, 400);

        $submitResponse = $this->withSession(['cbt_candidate_id' => $candidate->id])
                               ->post('/cbt/komputer', [
                                   'elapsedTime' => 420,
                                   'bukti_file' => $file,
                               ]);

        $submitResponse->assertRedirect(route('cbt.dashboard'));

        $candidate->refresh();
        $this->assertNotNull($candidate->tes_komputer);
        $this->assertNotNull($candidate->buktikomputer);
    }
}
