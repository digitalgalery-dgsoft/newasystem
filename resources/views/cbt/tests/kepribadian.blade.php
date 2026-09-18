@extends('layouts.cbt')

@section('title', 'Tes Kepribadian DISC | ESA Groups CBT')

@push('styles')
<style>
    input[type="radio"]:checked + label {
        border-color: #0F52BA;
        background-color: #eff6ff;
        color: #0b3275;
        font-weight: 700;
        box-shadow: 0 4px 12px rgba(15, 82, 186, 0.12);
    }
</style>
@endpush

@section('content')
<div class="space-y-6">

    <!-- TEST HEADER & RUNNING TIMER -->
    <div class="bg-white rounded-3xl p-5 sm:p-6 shadow-sm border border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-indigo-50 text-indigo-700 text-[11px] font-bold mb-1">
                <i class="fa-solid fa-brain text-[10px]"></i>
                Tahap 1 Evaluasi Rekrutmen
            </div>
            <h1 class="text-xl font-black text-slate-900 tracking-tight">Tes Kepribadian (DISC Assessment)</h1>
            <p class="text-xs text-slate-500">Pilih satu pernyataan yang paling menggambarkan kecenderungan karakter diri Anda.</p>
        </div>

        <div class="flex items-center gap-3 self-end sm:self-auto">
            <!-- Timer Display -->
            <div class="bg-slate-100 px-4 py-2 rounded-2xl text-center border border-slate-200 min-w-[120px]">
                <span class="text-[10px] font-bold text-slate-500 block uppercase tracking-wider">Waktu Berjalan</span>
                <span id="timerDisplay" class="text-lg font-black text-primary font-mono">00:00</span>
            </div>

            <!-- Pause Button -->
            <button type="button" id="pauseBtn" class="px-4 py-2.5 rounded-2xl bg-amber-100 hover:bg-amber-200 text-amber-800 text-xs font-bold transition-all flex items-center gap-1.5 shadow-sm">
                <i class="fa-solid fa-pause"></i>
                <span class="hidden sm:inline">Jeda</span>
            </button>
        </div>
    </div>

    <!-- QUESTIONS CONTAINER -->
    <div id="testArea" class="relative">
        <form id="personalityForm" method="POST" action="{{ route('cbt.kepribadian.submit') }}">
            @csrf
            <input type="hidden" name="timeElapsed" id="timeElapsedInput" value="0">

            @php
                $chunks = array_chunk($questions, 10);
                $totalChunks = count($chunks);
            @endphp

            @foreach($chunks as $pageIndex => $pageQuestions)
                <div class="page-container space-y-4 {{ $pageIndex > 0 ? 'hidden' : '' }}" data-page="{{ $pageIndex + 1 }}">
                    @foreach($pageQuestions as $q)
                        <div class="bg-white rounded-3xl p-5 sm:p-6 shadow-sm border border-slate-200 transition-all">
                            <div class="flex items-start gap-3 mb-4">
                                <span class="w-7 h-7 rounded-xl bg-primary-50 text-primary flex items-center justify-center font-black text-xs flex-shrink-0 border border-primary-200">
                                    {{ $q['id'] }}
                                </span>
                                <div>
                                    <h3 class="text-sm font-bold text-slate-800">{{ $q['prompt'] }}</h3>
                                    <p class="text-[11px] text-slate-400">Pilih salah satu dari 4 pilihan di bawah ini:</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-xs">
                                <!-- Option A -->
                                <div>
                                    <input type="radio" name="q{{ $q['id'] }}" id="q{{ $q['id'] }}_a" value="a" required class="hidden">
                                    <label for="q{{ $q['id'] }}_a" class="block p-4 border-2 border-slate-200 rounded-2xl cursor-pointer hover:bg-slate-50 transition-all font-medium leading-relaxed">
                                        <div class="flex items-start gap-2.5">
                                            <span class="w-5 h-5 rounded-full border-2 border-slate-400 flex items-center justify-center text-[10px] font-bold flex-shrink-0 mt-0.5">A</span>
                                            <span>{{ $q['a'] }}</span>
                                        </div>
                                    </label>
                                </div>

                                <!-- Option B -->
                                <div>
                                    <input type="radio" name="q{{ $q['id'] }}" id="q{{ $q['id'] }}_b" value="b" required class="hidden">
                                    <label for="q{{ $q['id'] }}_b" class="block p-4 border-2 border-slate-200 rounded-2xl cursor-pointer hover:bg-slate-50 transition-all font-medium leading-relaxed">
                                        <div class="flex items-start gap-2.5">
                                            <span class="w-5 h-5 rounded-full border-2 border-slate-400 flex items-center justify-center text-[10px] font-bold flex-shrink-0 mt-0.5">B</span>
                                            <span>{{ $q['b'] }}</span>
                                        </div>
                                    </label>
                                </div>

                                <!-- Option C -->
                                <div>
                                    <input type="radio" name="q{{ $q['id'] }}" id="q{{ $q['id'] }}_c" value="c" required class="hidden">
                                    <label for="q{{ $q['id'] }}_c" class="block p-4 border-2 border-slate-200 rounded-2xl cursor-pointer hover:bg-slate-50 transition-all font-medium leading-relaxed">
                                        <div class="flex items-start gap-2.5">
                                            <span class="w-5 h-5 rounded-full border-2 border-slate-400 flex items-center justify-center text-[10px] font-bold flex-shrink-0 mt-0.5">C</span>
                                            <span>{{ $q['c'] }}</span>
                                        </div>
                                    </label>
                                </div>

                                <!-- Option D -->
                                <div>
                                    <input type="radio" name="q{{ $q['id'] }}" id="q{{ $q['id'] }}_d" value="d" required class="hidden">
                                    <label for="q{{ $q['id'] }}_d" class="block p-4 border-2 border-slate-200 rounded-2xl cursor-pointer hover:bg-slate-50 transition-all font-medium leading-relaxed">
                                        <div class="flex items-start gap-2.5">
                                            <span class="w-5 h-5 rounded-full border-2 border-slate-400 flex items-center justify-center text-[10px] font-bold flex-shrink-0 mt-0.5">D</span>
                                            <span>{{ $q['d'] }}</span>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endforeach

            <!-- PAGINATION & SUBMIT BUTTONS -->
            <div class="bg-white rounded-3xl p-5 shadow-sm border border-slate-200 flex items-center justify-between gap-4 mt-6">
                <button type="button" id="prevPageBtn" disabled class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 font-bold text-xs disabled:opacity-40 disabled:cursor-not-allowed hover:bg-slate-50 transition-all flex items-center gap-2">
                    <i class="fa-solid fa-arrow-left text-[10px]"></i>
                    <span>Sebelumnya</span>
                </button>

                <div class="text-xs font-bold text-slate-500">
                    Halaman <span id="currentPageNum" class="text-primary font-black">1</span> dari <span class="font-black text-slate-800">{{ $totalChunks }}</span>
                </div>

                <button type="button" id="nextPageBtn" class="px-5 py-2.5 rounded-xl bg-primary text-white font-bold text-xs hover:bg-primary-700 shadow-md shadow-primary/20 transition-all flex items-center gap-2">
                    <span>Berikutnya</span>
                    <i class="fa-solid fa-arrow-right text-[10px]"></i>
                </button>

                <button type="submit" id="submitTestBtn" class="hidden px-6 py-2.5 rounded-xl bg-emerald-600 text-white font-bold text-xs hover:bg-emerald-700 shadow-md shadow-emerald-600/20 transition-all flex items-center gap-2">
                    <i class="fa-solid fa-check-double text-xs"></i>
                    <span>Selesai & Kirim Jawaban</span>
                </button>
            </div>
        </form>
    </div>

    <!-- PAUSE OVERLAY MODAL -->
    <div id="pauseOverlay" class="hidden fixed inset-0 z-50 bg-slate-900/80 backdrop-blur-md flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl p-8 max-w-sm w-full text-center shadow-2xl border border-slate-100">
            <div class="w-16 h-16 rounded-3xl bg-amber-100 text-amber-600 flex items-center justify-center mx-auto text-3xl mb-4">
                <i class="fa-solid fa-circle-pause"></i>
            </div>
            <h3 class="text-lg font-black text-slate-900">Tes Dijeda Sementara</h3>
            <p class="text-xs text-slate-500 mt-2 leading-relaxed">Timer pengerjaan telah dihentikan sementara. Klik tombol di bawah ini jika Anda sudah siap untuk melanjutkan kembali.</p>
            <button type="button" id="resumeBtn" class="mt-6 w-full py-3.5 px-6 rounded-2xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-lg shadow-emerald-600/30 transition-all">
                Lanjutkan Mengerjakan
            </button>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const pages = document.querySelectorAll('.page-container');
    const totalPages = pages.length;
    let currentPage = 1;

    const prevBtn = document.getElementById('prevPageBtn');
    const nextBtn = document.getElementById('nextPageBtn');
    const submitBtn = document.getElementById('submitTestBtn');
    const pageNumDisplay = document.getElementById('currentPageNum');
    const timerDisplay = document.getElementById('timerDisplay');
    const timeElapsedInput = document.getElementById('timeElapsedInput');
    const pauseBtn = document.getElementById('pauseBtn');
    const resumeBtn = document.getElementById('resumeBtn');
    const pauseOverlay = document.getElementById('pauseOverlay');
    const form = document.getElementById('personalityForm');

    // LocalStorage Keys
    const storageKey = 'cbt_psiko_answers_{{ $candidate->id }}';
    const timerKey = 'cbt_psiko_seconds_{{ $candidate->id }}';

    // Timer Logic
    let seconds = parseInt(localStorage.getItem(timerKey) || 0);
    let timerRunning = true;

    function formatTime(sec) {
        const m = Math.floor(sec / 60).toString().padStart(2, '0');
        const s = (sec % 60).toString().padStart(2, '0');
        return `${m}:${s}`;
    }

    timerDisplay.textContent = formatTime(seconds);
    timeElapsedInput.value = seconds;

    const interval = setInterval(() => {
        if (timerRunning) {
            seconds++;
            timerDisplay.textContent = formatTime(seconds);
            timeElapsedInput.value = seconds;
            localStorage.setItem(timerKey, seconds);
        }
    }, 1000);

    // Pause / Resume
    pauseBtn.addEventListener('click', () => {
        timerRunning = false;
        pauseOverlay.classList.remove('hidden');
    });

    resumeBtn.addEventListener('click', () => {
        timerRunning = true;
        pauseOverlay.classList.add('hidden');
    });

    // Restore Saved Answers
    const savedAnswers = JSON.parse(localStorage.getItem(storageKey) || '{}');
    for (const [name, val] of Object.entries(savedAnswers)) {
        const radio = form.querySelector(`input[name="${name}"][value="${val}"]`);
        if (radio) radio.checked = true;
    }

    // Auto Save Answers on Change
    form.addEventListener('change', (e) => {
        if (e.target.type === 'radio') {
            const formData = new FormData(form);
            const currentAnswers = {};
            for (const [k, v] of formData.entries()) {
                if (k.startsWith('q')) currentAnswers[k] = v;
            }
            localStorage.setItem(storageKey, JSON.stringify(currentAnswers));
        }
    });

    // Paging UI Handler
    function updatePaging() {
        pages.forEach((p, idx) => {
            p.classList.toggle('hidden', idx !== (currentPage - 1));
        });

        pageNumDisplay.textContent = currentPage;
        prevBtn.disabled = (currentPage === 1);

        if (currentPage === totalPages) {
            nextBtn.classList.add('hidden');
            submitBtn.classList.remove('hidden');
        } else {
            nextBtn.classList.remove('hidden');
            submitBtn.classList.add('hidden');
        }

        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    prevBtn.addEventListener('click', () => {
        if (currentPage > 1) {
            currentPage--;
            updatePaging();
        }
    });

    nextBtn.addEventListener('click', () => {
        // Validate required questions on current page
        const currentPageEl = pages[currentPage - 1];
        const radios = currentPageEl.querySelectorAll('input[type="radio"]:checked');
        const totalInPage = currentPageEl.querySelectorAll('.grid').length;

        if (radios.length < totalInPage) {
            Swal.fire({
                icon: 'warning',
                title: 'Jawaban Belum Lengkap',
                text: 'Harap jawab seluruh pertanyaan di halaman ini sebelum beralih ke halaman berikutnya.',
                confirmButtonColor: '#0F52BA'
            });
            return;
        }

        if (currentPage < totalPages) {
            currentPage++;
            updatePaging();
        }
    });

    form.addEventListener('submit', (e) => {
        // Clear local storage on submit
        localStorage.removeItem(storageKey);
        localStorage.removeItem(timerKey);
    });

    updatePaging();
});
</script>
@endpush
