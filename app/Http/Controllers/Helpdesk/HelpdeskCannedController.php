<?php

namespace App\Http\Controllers\Helpdesk;

use App\Http\Controllers\Controller;
use App\Models\HelpdeskCannedResponse;
use App\Models\HelpdeskDivision;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HelpdeskCannedController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if (!$user) return redirect()->route('login');

        $cannedResponses = HelpdeskCannedResponse::with(['division', 'creator'])
            ->orderBy('title', 'asc')
            ->get();

        $divisions = HelpdeskDivision::where('is_active', true)->orderBy('name', 'asc')->get();

        return view('helpdesk.canned.index', compact('cannedResponses', 'divisions', 'user'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'title' => 'required|string|max:150',
            'content' => 'required|string',
            'division_id' => 'nullable|exists:helpdesk_divisions,id',
        ]);

        HelpdeskCannedResponse::create([
            'title' => trim($request->input('title')),
            'content' => trim($request->input('content')),
            'division_id' => $request->input('division_id'),
            'created_by' => $user->id,
        ]);

        return redirect()->route('helpdesk.canned.index')->with('success', 'Template balasan cepat berhasil ditambahkan!');
    }

    public function destroy($id)
    {
        $canned = HelpdeskCannedResponse::findOrFail($id);
        $canned->delete();

        return redirect()->route('helpdesk.canned.index')->with('success', 'Template balasan cepat berhasil dihapus.');
    }
}
