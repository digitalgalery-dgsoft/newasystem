<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Employee;

class FeatureController extends Controller
{
    /**
     * Halaman Beranda / Home (Sambutan Pengguna)
     */
    public function index()
    {
        $user = Auth::user();
        $employee = null;

        if ($user && $user->email) {
            $employee = Employee::where('email', $user->email)->first();
        }

        return view('fitur.index', compact('user', 'employee'));
    }
}
