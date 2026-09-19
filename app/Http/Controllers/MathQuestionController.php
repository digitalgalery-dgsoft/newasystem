<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MathQuestion;
use Illuminate\Support\Facades\DB;

class MathQuestionController extends Controller
{
    /**
     * Tampilkan daftar master soal matematika
     */
    public function index(Request $request)
    {
        $search = trim($request->query('search', ''));
        $type = $request->query('type', 'all');
        $status = $request->query('status', 'all');

        $query = MathQuestion::query();

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('question_text', 'like', "%{$search}%")
                  ->orWhere('correct_answer', 'like', "%{$search}%");
            });
        }

        if ($type !== 'all' && in_array($type, ['multiple_choice', 'fill_in_the_blank'])) {
            $query->where('question_type', $type);
        }

        if ($status !== 'all') {
            $query->where('is_active', $status === '1' ? 1 : 0);
        }

        $questions = $query->orderBy('id', 'asc')->get();

        // Statistik Metrik
        $totalQuestions = MathQuestion::count();
        $activeQuestions = MathQuestion::where('is_active', 1)->count();
        $multipleChoiceCount = MathQuestion::where('question_type', 'multiple_choice')->count();
        $fillInBlankCount = MathQuestion::where('question_type', 'fill_in_the_blank')->count();

        return view('master.math.index', compact(
            'questions',
            'search',
            'type',
            'status',
            'totalQuestions',
            'activeQuestions',
            'multipleChoiceCount',
            'fillInBlankCount'
        ));
    }

    /**
     * Simpan soal baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'question_text' => 'required|string',
            'question_type' => 'required|in:multiple_choice,fill_in_the_blank',
            'correct_answer' => 'required|string',
        ]);

        $choices = null;
        if ($request->question_type === 'multiple_choice') {
            $rawChoices = $request->input('choices', []);
            $cleanChoices = [];
            foreach (['A', 'B', 'C', 'D', 'E'] as $key) {
                if (!empty($rawChoices[$key])) {
                    $cleanChoices[$key] = trim($rawChoices[$key]);
                }
            }
            if (empty($cleanChoices)) {
                return back()->withInput()->with('error', 'Pilihan ganda minimal harus memiliki opsi A dan B.');
            }
            $choices = $cleanChoices;
        }

        $question = MathQuestion::create([
            'question_text' => trim($request->question_text),
            'question_type' => $request->question_type,
            'choices' => $choices ? json_encode($choices) : null,
            'correct_answer' => trim($request->correct_answer),
            'is_active' => $request->has('is_active') ? 1 : 0,
        ]);

        return redirect()->route('master.math.index')
            ->with('success', 'Soal matematika berhasil ditambahkan ke bank soal aktif.');
    }

    /**
     * Perbarui data soal
     */
    public function update(Request $request, $id)
    {
        $question = MathQuestion::findOrFail($id);

        $request->validate([
            'question_text' => 'required|string',
            'question_type' => 'required|in:multiple_choice,fill_in_the_blank',
            'correct_answer' => 'required|string',
        ]);

        $choices = null;
        if ($request->question_type === 'multiple_choice') {
            $rawChoices = $request->input('choices', []);
            $cleanChoices = [];
            foreach (['A', 'B', 'C', 'D', 'E'] as $key) {
                if (!empty($rawChoices[$key])) {
                    $cleanChoices[$key] = trim($rawChoices[$key]);
                }
            }
            if (empty($cleanChoices)) {
                return back()->withInput()->with('error', 'Pilihan ganda minimal harus memiliki opsi A dan B.');
            }
            $choices = $cleanChoices;
        }

        $question->question_text = trim($request->question_text);
        $question->question_type = $request->question_type;
        $question->choices = $choices ? json_encode($choices) : null;
        $question->correct_answer = trim($request->correct_answer);
        $question->is_active = $request->has('is_active') ? 1 : 0;
        $question->save();

        return redirect()->route('master.math.index')
            ->with('success', "Soal ID #{$question->id} berhasil diperbarui.");
    }

    /**
     * Toggle status aktif / non-aktif
     */
    public function toggleStatus($id)
    {
        $question = MathQuestion::findOrFail($id);
        $question->is_active = !$question->is_active;
        $question->save();

        $statusText = $question->is_active ? 'Diaktifkan di CBT' : 'Dinonaktifkan';

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'is_active' => $question->is_active,
                'message' => "Soal #{$question->id} berhasil {$statusText}.",
            ]);
        }

        return redirect()->back()
            ->with('success', "Status soal #{$question->id} berhasil {$statusText}.");
    }

    /**
     * Hapus soal dari master
     */
    public function destroy($id)
    {
        $question = MathQuestion::findOrFail($id);
        $qId = $question->id;

        // Cek apakah ada jawaban kandidat terkait di tb_hasilmath
        $answersCount = DB::table('tb_hasilmath')->where('id_soal', $qId)->count();
        if ($answersCount > 0) {
            // Jika ada riwayat jawaban, non-aktifkan saja agar integritas audit evaluasi kandidat terjaga
            $question->is_active = 0;
            $question->save();

            return redirect()->route('master.math.index')
                ->with('info', "Soal #{$qId} memiliki {$answersCount} riwayat jawaban pengerjaan kandidat terdahulu, sehingga status soal otomatis dinonaktifkan (tidak dihapus permanen demi integritas data).");
        }

        $question->delete();

        return redirect()->route('master.math.index')
            ->with('success', "Soal #{$qId} berhasil dihapus permanen dari sistem.");
    }
}
