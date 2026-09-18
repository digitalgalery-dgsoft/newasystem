@extends('layouts.cbt')

@section('title', 'Tes Matematika & Logika Berhitung | ESA Groups CBT')

@push('styles')
<style>
    input[type="radio"]:checked + label {
        border-color: #2563eb;
        background-color: #eff6ff;
        color: #1e40af;
        font-weight: 700;
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.12);
    }
</style>
@endpush

@section('content')
<div class="space-y-6">

    <!-- TEST HEADER & COUNTDOWN TIMER -->
    <div class="bg-white rounded-3xl p-5 sm:p-6 shadow-sm border border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-blue-50 text-blue-700 text-[11px] font-bold mb-1">
                <i class="fa-solid fa-calculator text-[10px]"></i>
                Tahap 2 Evaluasi Rekrutmen
            </div>
            <h1 class="text-xl font-black text-slate-900 tracking-tight">Tes Matematika & Logika Aritmetika</h1>
            <p class="text-xs text-slate-500">Kerjakan 10 butir soal hitung dagang, persentase, dan deret angka berikut dengan cermat.</p>
        </div>

        <div class="flex items-center gap-3 self-end sm:self-auto">
            <!-- Countdown Timer Display -->
            <div id="timerCard" class="bg-red-50 border border-red-200 px-5 py-2.5 rounded-2xl text-center min-w-[130px] transition-colors">
                <span class="text-[10px] font-bold text-red-600 block uppercase tracking-wider">Sisa Waktu</span>
                <span id="countdownDisplay" class="text-xl font-black text-red-600 font-mono tracking-wider">10:00</span>
            </div>
        </div>
    </div>

    <!-- PETUNJUK PENGERJAAN -->
    <div class="bg-blue-50/80 border border-blue-200 rounded-2xl p-4 text-xs text-slate-700 leading-relaxed">
        <div class="flex items-start gap-2.5">
            <i class="fa-solid fa-circle-info text-primary mt-0.5 text-sm flex-shrink-0"></i>
            <div>
                <strong class="text-slate-900 block mb-1">Petunjuk Pengisian Jawaban:</strong>
                <ul class="list-disc list-inside space-y-1 text-[11px] text-slate-600">
                    <li>Untuk soal <strong>Pilihan Ganda</strong>: klik opsi jawaban A, B, C, atau D yang paling benar.</li>
                    <li>Untuk soal <strong>Isian Singkat</strong>: ketik <strong>angka saja</strong> tanpa titik pemisah ribuan (contoh: <code class="bg-blue-100 px-1 rounded text-primary font-bold">150000</code>).</li>
                    <li>Jika jawaban berupa angka desimal, gunakan <strong>tanda titik (.)</strong> (contoh: <code class="bg-blue-100 px-1 rounded text-primary font-bold">10.5</code>).</li>
                    <li>Jawaban Anda tersimpan otomatis di perangkat ini. Jawaban akan dikirim otomatis saat waktu habis.</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- QUESTIONS FORM -->
    <form id="mathForm" method="POST" action="{{ route('cbt.matematika.submit') }}">
        @csrf
        <input type="hidden" name="timeElapsed" id="timeElapsedInput" value="0">

        <div class="space-y-5">
            @foreach($questions as $idx => $q)
                <div class="bg-white rounded-3xl p-5 sm:p-6 shadow-sm border border-slate-200">
                    <div class="flex items-start gap-3 mb-4">
                        <span class="w-7 h-7 rounded-xl bg-blue-50 text-blue-700 flex items-center justify-center font-black text-xs flex-shrink-0 border border-blue-200">
                            {{ $loop->iteration }}
                        </span>
                        <div>
                            <h3 class="text-sm font-bold text-slate-800 leading-relaxed">{{ $q['question_text'] }}</h3>
                            <span class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider block mt-1">
                                {{ $q['question_type'] === 'multiple_choice' ? 'Pilihan Ganda' : 'Isian Angka' }}
                            </span>
                        </div>
                    </div>

                    @if($q['question_type'] === 'multiple_choice')
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-xs mt-3">
                            @foreach($q['choices'] as $key => $choice)
                                <div>
                                    <input type="radio" name="answers[{{ $q['id'] }}]" id="m_{{ $q['id'] }}_{{ $key }}" value="{{ $key }}" class="hidden">
                                    <label for="m_{{ $q['id'] }}_{{ $key }}" class="block p-3.5 border-2 border-slate-200 rounded-2xl cursor-pointer hover:bg-slate-50 transition-all font-medium">
                                        <div class="flex items-center gap-2.5">
                                            <span class="w-5 h-5 rounded-full border-2 border-slate-400 flex items-center justify-center text-[10px] font-bold uppercase flex-shrink-0">{{ $key }}</span>
                                            <span>{{ $choice }}</span>
                                        </div>
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="mt-3 max-w-sm">
                            <input type="text" name="answers[{{ $q['id'] }}]" id="m_{{ $q['id'] }}" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-mono font-bold text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all" placeholder="Ketik jawaban angka di sini...">
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

        <!-- FINISH & SUBMIT BAR -->
        <div class="bg-white rounded-3xl p-5 shadow-sm border border-slate-200 flex items-center justify-between gap-4 mt-6">
            <span class="text-xs text-slate-500 font-medium">Periksa kembali seluruh jawaban sebelum mengirim.</span>
            <button type="submit" id="submitMathBtn" class="px-8 py-3.5 rounded-xl bg-gradient-to-r from-blue-700 to-indigo-600 hover:from-blue-800 hover:to-indigo-700 text-white font-bold text-xs shadow-lg shadow-blue-500/25 transition-all flex items-center gap-2">
                <i class="fa-solid fa-check-double text-xs"></i>
                <span>Selesai & Kirim Jawaban</span>
            </button>
        </div>
    </form>

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('mathForm');
    const countdownDisplay = document.getElementById('countdownDisplay');
    const timerCard = document.getElementById('timerCard');
    const timeElapsedInput = document.getElementById('timeElapsedInput');

    const totalSeconds = 600; // 10 menit
    const storageKey = 'cbt_math_answers_{{ $candidate->id }}';
    const timerKey = 'cbt_math_timer_{{ $candidate->id }}';

    let remainingSeconds = parseInt(localStorage.getItem(timerKey) ?? totalSeconds);
    let elapsedSeconds = totalSeconds - remainingSeconds;

    function updateDisplay(sec) {
        const m = Math.floor(sec / 60).toString().padStart(2, '0');
        const s = (sec % 60).toString().padStart(2, '0');
        countdownDisplay.textContent = `${m}:${s}`;

        if (sec <= 120) {
            timerCard.classList.remove('bg-red-50', 'border-red-200');
            timerCard.classList.add('bg-rose-500', 'border-rose-600', 'animate-pulse');
            countdownDisplay.classList.remove('text-red-600');
            countdownDisplay.classList.add('text-white');
        }
    }

    updateDisplay(remainingSeconds);
    timeElapsedInput.value = elapsedSeconds;

    const timerInterval = setInterval(() => {
        if (remainingSeconds > 0) {
            remainingSeconds--;
            elapsedSeconds++;
            updateDisplay(remainingSeconds);
            timeElapsedInput.value = elapsedSeconds;
            localStorage.setItem(timerKey, remainingSeconds);
        } else {
            clearInterval(timerInterval);
            localStorage.removeItem(timerKey);
            localStorage.removeItem(storageKey);
            Swal.fire({
                icon: 'info',
                title: 'Waktu Pengerjaan Habis!',
                text: 'Sesi waktu 10 menit telah selesai. Jawaban Anda akan otomatis dikirimkan sekarang.',
                timer: 2500,
                showConfirmButton: false,
                willClose: () => { form.submit(); }
            });
        }
    }, 1000);

    // Restore Saved Answers
    const saved = JSON.parse(localStorage.getItem(storageKey) || '{}');
    for (const [name, val] of Object.entries(saved)) {
        const input = form.querySelector(`[name="${name}"][value="${val}"]`) || form.querySelector(`[name="${name}"]`);
        if (input) {
            if (input.type === 'radio') input.checked = true;
            else input.value = val;
        }
    }

    // Auto Save Answers
    form.addEventListener('input', () => {
        const formData = new FormData(form);
        const currentAnswers = {};
        for (const [k, v] of formData.entries()) {
            if (k.startsWith('answers[')) currentAnswers[k] = v;
        }
        localStorage.setItem(storageKey, JSON.stringify(currentAnswers));
    });

    form.addEventListener('submit', function(e) {
        localStorage.removeItem(timerKey);
        localStorage.removeItem(storageKey);
    });
});
</script>
@endpush
