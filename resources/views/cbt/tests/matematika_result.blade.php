@extends('layouts.cbt')

@section('title', 'Hasil Tes Matematika | ESA Groups CBT')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <!-- RESULT HERO CARD -->
    <div class="bg-white rounded-3xl p-6 sm:p-10 shadow-sm border border-slate-200 text-center">
        @php
            $score = $testResult->score ?? 0;
            $isPass = $score >= 70;
        @endphp

        <div class="w-20 h-20 rounded-3xl {{ $isPass ? 'bg-emerald-50 text-emerald-600 border-emerald-200' : 'bg-amber-50 text-amber-600 border-amber-200' }} flex items-center justify-center mx-auto text-3xl shadow-inner border mb-4">
            <i class="fa-solid {{ $isPass ? 'fa-circle-check' : 'fa-clipboard-check' }}"></i>
        </div>

        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full {{ $isPass ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }} text-xs font-bold mb-2">
            <i class="fa-solid fa-calculator"></i>
            Tes Matematika Berhasil Diselesaikan
        </span>

        <h1 class="text-2xl font-black text-slate-900 tracking-tight">Hasil Tes Matematika & Logika Hitung</h1>
        <p class="text-xs text-slate-500 max-w-md mx-auto mt-1">Jawaban Anda telah dievaluasi secara otomatis oleh sistem.</p>

        <!-- SCORE METRIC BOX -->
        <div class="mt-8 p-6 rounded-3xl bg-slate-50 border border-slate-200 grid grid-cols-1 sm:grid-cols-3 gap-4 text-center">
            <div class="p-3 bg-white rounded-2xl border border-slate-100 shadow-sm">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Skor Perolehan</span>
                <span class="text-3xl font-black {{ $score >= 80 ? 'text-emerald-600' : ($score >= 60 ? 'text-amber-600' : 'text-rose-600') }} font-mono">
                    {{ $score }}
                </span>
                <span class="text-[11px] text-slate-400 block mt-0.5">dari 100 Poin</span>
            </div>

            <div class="p-3 bg-white rounded-2xl border border-slate-100 shadow-sm">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Jawaban Benar</span>
                <span class="text-3xl font-black text-slate-800 font-mono">
                    {{ $details['correct_count'] ?? 0 }} / {{ $details['total_questions'] ?? 10 }}
                </span>
                <span class="text-[11px] text-slate-400 block mt-0.5">Soal Tepat</span>
            </div>

            <div class="p-3 bg-white rounded-2xl border border-slate-100 shadow-sm">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Durasi Pengerjaan</span>
                <span class="text-3xl font-black text-blue-600 font-mono">
                    {{ $details['duration_formatted'] ?? '00:00' }}
                </span>
                <span class="text-[11px] text-slate-400 block mt-0.5">Menit : Detik</span>
            </div>
        </div>

        <!-- BREAKDOWN JAWABAN -->
        @if(!empty($details['breakdown']))
            <div class="mt-8 text-left">
                <h3 class="text-sm font-bold text-slate-800 mb-3 flex items-center gap-2">
                    <i class="fa-solid fa-list-check text-primary"></i>
                    <span>Rincian Pembahasan & Evaluasi Soal:</span>
                </h3>

                <div class="space-y-3">
                    @foreach($details['breakdown'] as $idx => $item)
                        <div class="p-4 rounded-2xl border {{ $item['is_correct'] ? 'bg-emerald-50/50 border-emerald-200' : 'bg-rose-50/50 border-rose-200' }} text-xs">
                            <div class="flex items-start justify-between gap-3">
                                <p class="font-bold text-slate-800">{{ $loop->iteration }}. {{ $item['question_text'] }}</p>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold flex-shrink-0 {{ $item['is_correct'] ? 'bg-emerald-200 text-emerald-800' : 'bg-rose-200 text-rose-800' }}">
                                    {{ $item['is_correct'] ? 'Benar' : 'Salah' }}
                                </span>
                            </div>
                            <div class="mt-2 text-[11px] text-slate-600 flex flex-wrap gap-x-4 gap-y-1">
                                <span>Jawaban Anda: <strong class="{{ $item['is_correct'] ? 'text-emerald-700' : 'text-rose-700' }}">{{ $item['user_answer'] ?: '(Kosong)' }}</strong></span>
                                <span>Kunci Jawaban: <strong class="text-slate-800">{{ $item['correct_answer'] }}</strong></span>
                            </div>
                            @if(!empty($item['explanation']))
                                <p class="mt-1 text-[11px] text-slate-500 italic">Pembahasan: {{ $item['explanation'] }}</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Back to Dashboard Button -->
        <div class="mt-8 pt-6 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-center gap-3">
            <a href="{{ route('cbt.dashboard') }}" class="w-full sm:w-auto px-8 py-3.5 rounded-xl bg-primary hover:bg-primary-700 text-white text-xs font-bold shadow-lg shadow-primary/25 transition-all flex items-center justify-center gap-2">
                <i class="fa-solid fa-house"></i>
                <span>Kembali ke Dashboard CBT</span>
            </a>
        </div>
    </div>

</div>
@endsection
