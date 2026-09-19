<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PersonalityQuestion;

class PersonalityQuestionController extends Controller
{
    /**
     * Tampilkan master soal tes kepribadian (40 butir DISC / Florence Littauer)
     */
    public function index(Request $request)
    {
        $search = trim($request->query('search', ''));
        $category = $request->query('category', 'all');

        $query = PersonalityQuestion::query();

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('id', $search)
                  ->orWhere('pilihan_a', 'like', "%{$search}%")
                  ->orWhere('pilihan_b', 'like', "%{$search}%")
                  ->orWhere('pilihan_c', 'like', "%{$search}%")
                  ->orWhere('pilihan_d', 'like', "%{$search}%");
            });
        }

        if ($category === 'strengths') {
            $query->where('id', '<=', 20);
        } elseif ($category === 'weaknesses') {
            $query->where('id', '>', 20);
        }

        $questions = $query->orderBy('id', 'asc')->get();

        $totalQuestions = PersonalityQuestion::count();
        $strengthsCount = PersonalityQuestion::where('id', '<=', 20)->count();
        $weaknessesCount = PersonalityQuestion::where('id', '>', 20)->count();

        return view('master.personality.index', compact(
            'questions',
            'search',
            'category',
            'totalQuestions',
            'strengthsCount',
            'weaknessesCount'
        ));
    }

    /**
     * Perbarui butir opsi soal kepribadian
     */
    public function update(Request $request, $id)
    {
        $question = PersonalityQuestion::findOrFail($id);

        $request->validate([
            'pilihan_a' => 'required|string|max:255',
            'pilihan_b' => 'required|string|max:255',
            'pilihan_c' => 'required|string|max:255',
            'pilihan_d' => 'required|string|max:255',
        ]);

        $question->pilihan_a = trim($request->pilihan_a);
        $question->pilihan_b = trim($request->pilihan_b);
        $question->pilihan_c = trim($request->pilihan_c);
        $question->pilihan_d = trim($request->pilihan_d);
        $question->save();

        return redirect()->route('master.personality.index')
            ->with('success', "Butir Soal Kepribadian #{$question->id} berhasil diperbarui.");
    }
}
