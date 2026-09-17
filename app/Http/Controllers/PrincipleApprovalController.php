<?php

namespace App\Http\Controllers;

use App\Models\PrincipleApproval;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PrincipleApprovalController extends Controller
{
    public function show(string $token)
    {
        $approval = PrincipleApproval::with(['candidate.assessment', 'candidate.workExperiences', 'candidate.testResults', 'principle'])
            ->where('approval_token', $token)
            ->firstOrFail();

        return view('principles.approval', compact('approval'));
    }

    public function process(Request $request, string $token)
    {
        $approval = PrincipleApproval::where('approval_token', $token)->firstOrFail();

        $validated = $request->validate([
            'decision' => 'required|in:approved,rejected',
            'notes' => 'nullable|string',
            'signature_data' => 'nullable|string',
        ]);

        $signaturePath = null;
        if (!empty($validated['signature_data']) && str_contains($validated['signature_data'], 'base64')) {
            $imageData = explode(',', $validated['signature_data'])[1];
            $fileName = 'signatures/client_' . time() . '_' . Str::random(8) . '.png';
            \Storage::disk('public')->put($fileName, base64_decode($imageData));
            $signaturePath = $fileName;
        }

        $approval->update([
            'status' => $validated['decision'],
            'notes' => $validated['notes'] ?? '',
            'signature_path' => $signaturePath ?? $approval->signature_path,
            'approved_at' => now(),
        ]);

        // Update status kandidat
        if ($validated['decision'] === 'approved') {
            $approval->candidate->update(['status' => 'passed']);
        } else {
            $approval->candidate->update(['status' => 'rejected']);
        }

        return back()->with('success', 'Konfirmasi approval berhasil dikirimkan. Terima kasih!');
    }
}