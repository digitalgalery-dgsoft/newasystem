<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JobSpec;
use App\Models\Candidate;
use App\Models\Principle;

class HomeController extends Controller
{
    /**
     * Halaman Depan Web / Landing Page Publik (Replikasi & Modernisasi v3/index.php)
     */
    public function index()
    {
        // 6 Lowongan Terbaru yang Aktif
        $featuredJobs = JobSpec::where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->take(6)
            ->get();

        foreach ($featuredJobs as $j) {
            $j->applicant_count = Candidate::where('applied_job', $j->job_title)
                ->where(function ($q) use ($j) {
                    $q->where('area', $j->job_area)
                      ->orWhereNull('area');
                })->count();
        }

        // Statistik Utama
        $totalJobs = JobSpec::where('status', 'active')->count();
        $totalCandidates = Candidate::count();
        $totalPrinciples = Principle::count() ?: 8;
        $distinctAreasCount = JobSpec::where('status', 'active')->distinct('job_area')->count('job_area') ?: 12;

        return view('home.index', compact(
            'featuredJobs',
            'totalJobs',
            'totalCandidates',
            'totalPrinciples',
            'distinctAreasCount'
        ));
    }
}
