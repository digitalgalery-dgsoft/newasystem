<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Candidate;
use App\Models\WorkExperience;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Sinkronisasi data lengkap Novianti Waruwu dari record 66212 (arsip salah kamar) ke record aktif 66219
        $source = Candidate::find(66212);
        $target = Candidate::find(66219);

        if ($source && $target) {
            $target->education                  = $source->education ?: 'SMA / SMK';
            $target->birth_place                = $source->birth_place ?: $target->birth_place;
            $target->religion                   = $source->religion ?: $target->religion;
            $target->marital_status             = $source->marital_status ?: $target->marital_status;
            $target->address_ktp                = $source->address_ktp ?: $target->address_ktp;
            $target->address_domicile           = $source->address_domicile ?: $target->address_domicile;
            $target->expected_salary            = $source->expected_salary ?: $target->expected_salary;
            $target->last_salary                = $source->last_salary ?: $target->last_salary;
            $target->height                     = $source->height ?: $target->height;
            $target->weight                     = $source->weight ?: $target->weight;
            $target->mother_name                = $source->mother_name ?: 'Diorma nainggolan';
            $target->emergency_contact_name     = $source->emergency_contact_name ?: 'Sokialulu waruwu';
            $target->emergency_contact_phone    = $source->emergency_contact_phone ?: '085376289144';
            $target->emergency_contact_relation = $source->emergency_contact_relation ?: 'Orang Tua';
            $target->bank_name                  = $source->bank_name ?: 'BCA';
            $target->bank_account_number        = $source->bank_account_number ?: '8215338432';
            $target->bank_account_holder        = $source->bank_account_holder ?: 'NOVIANTI WARUWU';
            $target->npwp                       = $source->npwp ?: '-';
            $target->work_motivation            = $source->work_motivation ?: $target->work_motivation;
            $target->strengths                  = $source->strengths ?: $target->strengths;
            $target->weaknesses                 = $source->weaknesses ?: $target->weaknesses;
            $target->current_activity           = $source->current_activity ?: 'Fokus mencari kerja';
            $target->vehicle                    = $source->vehicle ?: 'Tidak Memiliki';
            $target->driving_license            = $source->driving_license ?: 'Tidak Memiliki SIM';
            $target->computer_skill             = $source->computer_skill ?: 'Word';
            $target->english_skill              = $source->english_skill ?: 'Pasif';
            $target->photo_path                 = $source->photo_path ?: $target->photo_path;
            $target->cv_path                    = $source->cv_path ?: $target->cv_path;
            $target->signature_path             = $source->signature_path ?: $target->signature_path;
            $target->statement_agreed           = 1;
            $target->is_profile_complete        = 1;
            $target->is_komputer                = 0; // Posisi BA GT tidak memerlukan tes komputer
            $target->save();

            // Pindahkan seluruh riwayat kerja dari 66212 ke 66219
            WorkExperience::where('candidate_id', 66212)->update(['candidate_id' => 66219]);
        }

        // 2. Pemulihan Umum: Jika ada kandidat aktif lain yang field education-nya '0'
        $zeroEdus = Candidate::where('education', '0')->whereNotIn('status', ['Arsip', 'archived'])->get();
        foreach ($zeroEdus as $cand) {
            // Cari data sebelumnya dengan NIK yang sama yang memiliki data pendidikan valid
            $donor = Candidate::where('nik', $cand->nik)
                ->where('id', '!=', $cand->id)
                ->whereNotNull('education')
                ->where('education', '!=', '0')
                ->where('education', '!=', '')
                ->orderByDesc('id')
                ->first();

            if ($donor) {
                $cand->education = $donor->education;
                if (empty($cand->mother_name) && !empty($donor->mother_name)) {
                    $cand->mother_name = $donor->mother_name;
                }
                if (empty($cand->emergency_contact_name) && !empty($donor->emergency_contact_name)) {
                    $cand->emergency_contact_name = $donor->emergency_contact_name;
                    $cand->emergency_contact_phone = $donor->emergency_contact_phone;
                    $cand->emergency_contact_relation = $donor->emergency_contact_relation;
                }
                if (empty($cand->bank_name) && !empty($donor->bank_name)) {
                    $cand->bank_name = $donor->bank_name;
                    $cand->bank_account_number = $donor->bank_account_number;
                    $cand->bank_account_holder = $donor->bank_account_holder;
                }
                if (empty($cand->photo_path) && !empty($donor->photo_path)) {
                    $cand->photo_path = $donor->photo_path;
                }
                if (empty($cand->cv_path) && !empty($donor->cv_path)) {
                    $cand->cv_path = $donor->cv_path;
                }
                $cand->save();
            } else {
                // Fallback default jika '0' dan tidak ada donor
                $cand->education = 'SMA / SMK';
                $cand->save();
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No revert needed
    }
};
