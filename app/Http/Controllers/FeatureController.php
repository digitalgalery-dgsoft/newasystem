<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Candidate;
use App\Models\Employee;
use App\Models\Principle;

class FeatureController extends Controller
{
    public function index()
    {
        // Live counts for feature hub cards
        $interviewStats = [
            'total' => Candidate::count(),
            'active' => Candidate::where('status', 'Active')->count(),
            'done' => Candidate::where('status', 'Passed')->count(),
            'walk' => Candidate::where('status', 'Active')->count(),
        ];

        $employeeStats = [
            'total' => Employee::count(),
            'aktif' => Employee::where('status', 'Aktiv')->count(),
            'review' => Employee::where('status', 'Review')->count(),
        ];

        $principleStats = [
            'total' => Principle::count(),
            'active' => Principle::where('is_active', true)->count(),
        ];

        return view('fitur.index', compact('interviewStats', 'employeeStats', 'principleStats'));
    }
}