<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Candidate;
use App\Models\Principle;
use Carbon\Carbon;

class AiRankingController extends Controller
{
    /**
     * Halaman AI Candidate Ranking & Leaderboard (Replikasi ai_ranking.php)
     */
    public function index(Request $request)
    {
        $selectedJob = $request->query('job');
        $selectedTier = $request->query('tier');
        $search = $request->query('search') ?? $request->query('q');

        // Base Query: Hanya kandidat yang memiliki nilai ai_score > 0
        $baseQuery = Candidate::with(['principle'])
            ->whereNotNull('ai_score')
            ->where('ai_score', '>', 0);

        // List semua jabatan unik yang sudah dianalisis AI
        $availableJobs = Candidate::whereNotNull('ai_score')
            ->where('ai_score', '>', 0)
            ->whereNotNull('applied_job')
            ->where('applied_job', '!=', '')
            ->distinct()
            ->orderBy('applied_job', 'asc')
            ->pluck('applied_job');

        // Metrik Statistik
        $totalAnalyzed = (clone $baseQuery)->count();
        $topCandidate = (clone $baseQuery)->orderBy('ai_score', 'desc')->first();
        $avgScore = $totalAnalyzed > 0 ? round((clone $baseQuery)->avg('ai_score'), 1) : 0;
        $countGreen = (clone $baseQuery)->where('ai_score', '>=', 85)->count();
        $countYellow = (clone $baseQuery)->whereBetween('ai_score', [60, 84])->count();
        $countRed = (clone $baseQuery)->where('ai_score', '<', 60)->count();

        // Query tabel terfilter
        $query = clone $baseQuery;

        if (!empty($selectedJob)) {
            $query->where('applied_job', $selectedJob);
        }

        if (!empty($selectedTier)) {
            if ($selectedTier === 'green') {
                $query->where('ai_score', '>=', 85);
            } elseif ($selectedTier === 'yellow') {
                $query->whereBetween('ai_score', [60, 84]);
            } elseif ($selectedTier === 'red') {
                $query->where('ai_score', '<', 60);
            }
        }

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%")
                  ->orWhere('applied_job', 'like', "%{$search}%");
            });
        }

        // Leaderboard candidates
        $candidates = $query->orderBy('ai_score', 'desc')->orderBy('id', 'desc')->paginate(20)->withQueryString();

        // Top 3 Podium (dari query saat ini)
        $podiumCandidates = (clone $query)->orderBy('ai_score', 'desc')->orderBy('id', 'desc')->take(3)->get();

        return view('airanking.index', compact(
            'candidates',
            'availableJobs',
            'selectedJob',
            'selectedTier',
            'search',
            'totalAnalyzed',
            'topCandidate',
            'avgScore',
            'countGreen',
            'countYellow',
            'countRed',
            'podiumCandidates'
        ));
    }
}
