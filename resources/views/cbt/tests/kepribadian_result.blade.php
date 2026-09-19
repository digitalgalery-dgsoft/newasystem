@extends('layouts.cbt')

@section('title', 'Hasil Evaluasi Tes Kepribadian DISC | ESA Groups CBT')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <!-- RESULT SUCCESS CARD -->
    <div class="bg-white rounded-3xl p-6 sm:p-10 shadow-sm border border-slate-200 text-center">
        <div class="w-20 h-20 rounded-3xl bg-emerald-50 text-emerald-600 flex items-center justify-center mx-auto text-3xl shadow-inner border border-emerald-200 mb-4">
            <i class="fa-solid fa-circle-check"></i>
        </div>

        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-100 text-emerald-800 text-xs font-bold mb-2">
            <i class="fa-solid fa-flag-checkered"></i>
            Tes Selesai & Berhasil Direkam
        </span>

        <h1 class="text-2xl font-black text-slate-900 tracking-tight">Hasil Tes Kepribadian (DISC)</h1>
        <p class="text-xs text-slate-500 max-w-md mx-auto mt-1">Terima kasih telah menyelesaikan seluruh rangkaian pertanyaan tes kepribadian. Berikut adalah profil kecenderungan karakter kerja Anda.</p>

        <!-- DOMINANT TRAIT BANNER -->
        <div class="mt-6 p-5 rounded-2xl bg-gradient-to-r from-blue-50 via-indigo-50 to-purple-50 border border-blue-200/70 text-left">
            <span class="text-[10px] font-bold text-primary uppercase tracking-wider block">Karakter Dominan Anda:</span>
            <h3 class="text-base sm:text-lg font-black text-slate-900 mt-0.5">
                {{ $details['dominant_trait'] ?? 'Tipe Profil Teridentifikasi' }}
            </h3>
            <p class="text-xs text-slate-600 mt-1">
                Waktu Pengerjaan: <strong class="text-slate-800">{{ $details['duration_formatted'] ?? '00:00:00' }}</strong>
            </p>
        </div>

        <!-- METRICS BREAKDOWN & CHART.JS -->
        <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-6 items-center">
            
            <!-- Table Counts -->
            <div class="space-y-3 text-left">
                <div class="flex items-center justify-between p-3.5 rounded-xl bg-slate-50 border border-slate-100 text-xs">
                    <div class="flex items-center gap-2">
                        <span class="w-6 h-6 rounded-lg bg-indigo-600 text-white font-black text-xs flex items-center justify-center">A</span>
                        <span class="font-bold text-slate-800">Melankolis (Analitis, Rapi, Tekun)</span>
                    </div>
                    <span class="text-sm font-black text-slate-900">{{ $details['counts']['A'] ?? $details['counts']['D'] ?? 0 }} Poin</span>
                </div>

                <div class="flex items-center justify-between p-3.5 rounded-xl bg-slate-50 border border-slate-100 text-xs">
                    <div class="flex items-center gap-2">
                        <span class="w-6 h-6 rounded-lg bg-amber-500 text-white font-black text-xs flex items-center justify-center">B</span>
                        <span class="font-bold text-slate-800">Sanguinis (Populer, Ceria, Komunikatif)</span>
                    </div>
                    <span class="text-sm font-black text-slate-900">{{ $details['counts']['B'] ?? $details['counts']['I'] ?? 0 }} Poin</span>
                </div>

                <div class="flex items-center justify-between p-3.5 rounded-xl bg-slate-50 border border-slate-100 text-xs">
                    <div class="flex items-center gap-2">
                        <span class="w-6 h-6 rounded-lg bg-rose-600 text-white font-black text-xs flex items-center justify-center">C</span>
                        <span class="font-bold text-slate-800">Koleris (Kuat, Pemimpin, Berani, Tegas)</span>
                    </div>
                    <span class="text-sm font-black text-slate-900">{{ $details['counts']['C'] ?? $details['counts']['S'] ?? 0 }} Poin</span>
                </div>

                <div class="flex items-center justify-between p-3.5 rounded-xl bg-slate-50 border border-slate-100 text-xs">
                    <div class="flex items-center gap-2">
                        <span class="w-6 h-6 rounded-lg bg-emerald-600 text-white font-black text-xs flex items-center justify-center">D</span>
                        <span class="font-bold text-slate-800">Plegmatis (Damai, Tenang, Sabar, Stabil)</span>
                    </div>
                    <span class="text-sm font-black text-slate-900">{{ $details['counts']['D'] ?? $details['counts']['C_old'] ?? 0 }} Poin</span>
                </div>
            </div>

            <!-- Chart Canvas -->
            <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100 flex items-center justify-center">
                <div class="w-60 h-60">
                    <canvas id="discChart"></canvas>
                </div>
            </div>

        </div>

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

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('discChart').getContext('2d');
    
    new Chart(ctx, {
        type: 'radar',
        data: {
            labels: ['Melankolis (A)', 'Sanguinis (B)', 'Koleris (C)', 'Plegmatis (D)'],
            datasets: [{
                label: 'Skor Karakter',
                data: [
                    {{ $details['counts']['A'] ?? $details['counts']['D'] ?? 0 }},
                    {{ $details['counts']['B'] ?? $details['counts']['I'] ?? 0 }},
                    {{ $details['counts']['C'] ?? $details['counts']['S'] ?? 0 }},
                    {{ $details['counts']['D'] ?? $details['counts']['C_old'] ?? 0 }}
                ],
                backgroundColor: 'rgba(15, 82, 186, 0.25)',
                borderColor: '#0F52BA',
                borderWidth: 2.5,
                pointBackgroundColor: '#0F52BA',
                pointBorderColor: '#fff',
                pointHoverBackgroundColor: '#fff',
                pointHoverBorderColor: '#0F52BA'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                r: {
                    angleLines: { color: '#e2e8f0' },
                    grid: { color: '#e2e8f0' },
                    pointLabels: {
                        font: { family: 'Outfit', size: 11, weight: 'bold' },
                        color: '#334155'
                    },
                    ticks: { display: false, stepSize: 2 }
                }
            },
            plugins: {
                legend: { display: false }
            }
        }
    });
});
</script>
@endpush
