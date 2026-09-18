@extends('layouts.cbt')

@section('title', 'Tes Komputer & Praktik Spreadsheet | ESA Groups CBT')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <!-- MAIN CASE & TIMER CARD -->
    <div class="bg-white rounded-3xl p-6 sm:p-10 shadow-sm border border-slate-200 text-center relative overflow-hidden">
        <div class="w-20 h-20 rounded-3xl bg-teal-50 text-teal-600 flex items-center justify-center mx-auto text-3xl shadow-inner border border-teal-200 mb-4">
            <i class="fa-solid fa-laptop-code"></i>
        </div>

        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-teal-100 text-teal-800 text-xs font-bold mb-2">
            <i class="fa-solid fa-file-excel"></i>
            Tahap 3 Evaluasi Rekrutmen
        </span>

        <h1 class="text-2xl font-black text-slate-900 tracking-tight">Tes Praktik Komputer & Operasional</h1>
        <p class="text-xs text-slate-500 max-w-lg mx-auto mt-1">
            Uji kemampuan aplikasi perkantoran (Microsoft Excel / Google Spreadsheet). Tekan tombol <strong>"Mulai Tes"</strong> untuk mengaktifkan timer pengerjaan.
        </p>

        <!-- LIVE COUNT-UP TIMER -->
        <div class="mt-6 bg-slate-100 border border-slate-200 p-5 rounded-2xl inline-block min-w-[220px]">
            <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block">Waktu Pengerjaan Anda</span>
            <span id="timerDisplay" class="text-3xl font-black text-teal-600 tracking-widest font-mono block mt-1">00:00:00</span>
        </div>

        <!-- INSTRUKSI TUGAS PRAKTIK -->
        <div class="mt-8 text-left bg-slate-50 border border-slate-200 rounded-2xl p-6 text-xs text-slate-700 leading-relaxed">
            <h3 class="text-sm font-bold text-slate-900 mb-3 flex items-center gap-2">
                <i class="fa-solid fa-list-ol text-teal-600"></i>
                <span>Instruksi Tugas Praktik Spreadsheet:</span>
            </h3>
            <ol class="list-decimal list-inside space-y-2 text-slate-600">
                <li>Buka aplikasi <strong>Microsoft Excel</strong> atau <strong>Google Spreadsheet</strong> di perangkat komputer/laptop Anda.</li>
                <li>Buat sebuah tabel laporan penjualan <strong>5 Toko / Outlet</strong> dengan struktur kolom:
                    <ul class="list-disc list-inside ml-5 mt-1 space-y-0.5 text-[11px] text-slate-500">
                        <li>No, Nama Toko / Outlet, Target Penjualan (Rp), Realisasi Penjualan (Rp), Achievement (%), dan Status.</li>
                    </ul>
                </li>
                <li>Terapkan rumus perhitungan:
                    <ul class="list-disc list-inside ml-5 mt-1 space-y-0.5 text-[11px] text-slate-500">
                        <li><strong>Achievement (%)</strong> = Realisasi / Target (Format persentase).</li>
                        <li><strong>Status</strong> = Gunakan rumus <code>IF</code>, jika Ach >= 100% maka <em>"Tercapai"</em>, jika kurang maka <em>"Belum Tercapai"</em>.</li>
                        <li><strong>Total & Rata-rata</strong> pada baris bawah menggunakan rumus <code>SUM</code> dan <code>AVERAGE</code>.</li>
                    </ul>
                </li>
                <li>Setelah selesai, <strong>ambil tangkapan layar (screenshot)</strong> atau foto jelas layar monitor Anda (format JPG/PNG).</li>
                <li>Klik tombol <strong>"Saya Sudah Selesai"</strong> di bawah untuk mengunggah bukti pengerjaan Anda.</li>
            </ol>
        </div>

        <!-- ACTION BUTTONS -->
        <div class="mt-8 flex flex-col sm:flex-row items-center justify-center gap-3">
            <button type="button" id="startBtn" class="w-full sm:w-auto px-8 py-3.5 rounded-xl bg-teal-600 hover:bg-teal-700 text-white font-bold text-xs shadow-lg shadow-teal-600/25 transition-all flex items-center justify-center gap-2">
                <i class="fa-solid fa-play"></i>
                <span>Mulai Tes Komputer</span>
            </button>

            <button type="button" id="finishBtn" class="hidden w-full sm:w-auto px-8 py-3.5 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-bold text-xs shadow-lg shadow-emerald-600/25 transition-all flex items-center justify-center gap-2 animate-pulse">
                <i class="fa-solid fa-flag-checkered"></i>
                <span>Saya Sudah Selesai (Kirim Bukti)</span>
            </button>
        </div>
    </div>

    <!-- MODAL UPLOAD BUKTI PENGERJAAN -->
    <div id="uploadModal" class="hidden fixed inset-0 z-50 bg-slate-900/70 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-md w-full p-6 sm:p-8 shadow-2xl border border-slate-100">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                    <i class="fa-solid fa-cloud-arrow-up text-teal-600"></i>
                    <span>Unggah Bukti Hasil Praktik</span>
                </h3>
                <button type="button" id="cancelUploadBtn" class="text-slate-400 hover:text-slate-600 text-lg">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <form method="POST" action="{{ route('cbt.komputer.submit') }}" enctype="multipart/form-data" class="space-y-4 mt-4 text-xs">
                @csrf
                <input type="hidden" name="elapsedTime" id="elapsedTimeInput" value="0">

                <div class="p-3 bg-teal-50 border border-teal-200 rounded-xl text-teal-800">
                    Durasi pengerjaan Anda: <strong id="modalDuration" class="font-mono text-sm font-black">00:00:00</strong>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Pilih File Screenshot / Foto Bukti (JPG / PNG / PDF) <span class="text-rose-500">*</span></label>
                    <input type="file" name="bukti_file" id="buktiFile" required accept="image/jpeg,image/png,application/pdf" class="block w-full text-xs text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-teal-50 file:text-teal-700 hover:file:bg-teal-100">
                    <p class="text-[11px] text-slate-400 mt-1">Ukuran maksimal file: 5 MB.</p>
                </div>

                <div class="pt-4 flex justify-end gap-2 border-t border-slate-100">
                    <button type="button" id="modalCancelBtn" class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 font-bold hover:bg-slate-50">Batal</button>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-teal-600 text-white font-bold hover:bg-teal-700 shadow-md flex items-center gap-2">
                        <i class="fa-solid fa-upload text-xs"></i>
                        <span>Kirim Bukti Tes</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const timerDisplay = document.getElementById('timerDisplay');
    const startBtn = document.getElementById('startBtn');
    const finishBtn = document.getElementById('finishBtn');
    const uploadModal = document.getElementById('uploadModal');
    const cancelUploadBtn = document.getElementById('cancelUploadBtn');
    const modalCancelBtn = document.getElementById('modalCancelBtn');
    const modalDuration = document.getElementById('modalDuration');
    const elapsedTimeInput = document.getElementById('elapsedTimeInput');

    const timerKey = 'cbt_kompt_timer_{{ $candidate->id }}';
    const isRunningKey = 'cbt_kompt_running_{{ $candidate->id }}';

    let seconds = parseInt(localStorage.getItem(timerKey) || 0);
    let isRunning = localStorage.getItem(isRunningKey) === 'true';
    let timerInterval = null;

    function formatTime(sec) {
        const h = Math.floor(sec / 3600).toString().padStart(2, '0');
        const m = Math.floor((sec % 3600) / 60).toString().padStart(2, '0');
        const s = (sec % 60).toString().padStart(2, '0');
        return `${h}:${m}:${s}`;
    }

    function startTimer() {
        isRunning = true;
        localStorage.setItem(isRunningKey, 'true');
        startBtn.classList.add('hidden');
        finishBtn.classList.remove('hidden');

        timerInterval = setInterval(() => {
            seconds++;
            timerDisplay.textContent = formatTime(seconds);
            localStorage.setItem(timerKey, seconds);
        }, 1000);
    }

    timerDisplay.textContent = formatTime(seconds);

    if (isRunning) {
        startTimer();
    }

    startBtn.addEventListener('click', () => {
        startTimer();
    });

    finishBtn.addEventListener('click', () => {
        clearInterval(timerInterval);
        isRunning = false;
        localStorage.setItem(isRunningKey, 'false');
        
        modalDuration.textContent = formatTime(seconds);
        elapsedTimeInput.value = seconds;
        uploadModal.classList.remove('hidden');
    });

    cancelUploadBtn.addEventListener('click', () => {
        uploadModal.classList.add('hidden');
    });

    modalCancelBtn.addEventListener('click', () => {
        uploadModal.classList.add('hidden');
    });
});
</script>
@endpush
