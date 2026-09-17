@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-slate-900">{{ $pageTitle }}</h2>
            <p class="text-xs text-slate-500 mt-0.5">Modul sistem recruitment terpadu ASystem v4</p>
        </div>
        <a href="{{ route('interview.index') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-slate-900 text-white hover:bg-slate-800 transition">
            <i class="ri-arrow-left-line"></i>
            <span>Kembali ke Interview</span>
        </a>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 p-12 text-center shadow-sm">
        <div class="w-16 h-16 rounded-2xl bg-amber-100 text-amber-600 flex items-center justify-center mx-auto text-2xl font-bold mb-4 shadow-sm">
            <i class="ri-tools-line"></i>
        </div>
        <h3 class="text-base font-bold text-slate-900 mb-1">Modul {{ $pageTitle }} Sedang Disiapkan</h3>
        <p class="text-xs text-slate-500 max-w-md mx-auto mb-6">
            Fokus rebuild tahap 1 saat ini adalah modul <strong>Kandidat Interview</strong> (Dashboard, Walk Interview, Detail Form Hasil Evaluasi, Done, dan Arsip).
        </p>
        <a href="{{ route('interview.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-xs font-bold bg-amber-500 hover:bg-amber-400 text-slate-950 transition shadow-md">
            <i class="ri-store-3-line"></i>
            <span>Buka Dashboard Interview</span>
        </a>
    </div>
</div>
@endsection
