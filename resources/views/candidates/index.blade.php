@extends('layouts.app')

@section('content')
<div class="space-y-6">
    
    <!-- STATS CARDS -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 sm:gap-4">
        <a href="{{ route('candidates.index') }}" class="p-4 rounded-2xl bg-white border border-slate-200/80 shadow-xs hover:border-indigo-300 transition-all">
            <span class="text-xs font-semibold text-slate-500">Semua Data</span>
            <div class="mt-1 text-2xl font-black text-slate-900">{{ number_format($statusCounts['total']) }}</div>
        </a>
        <a href="{{ route('candidates.index', ['status' => 'new']) }}" class="p-4 rounded-2xl bg-white border border-slate-200/80 shadow-xs hover:border-amber-300 transition-all">
            <span class="text-xs font-semibold text-amber-600">Baru Masuk</span>
            <div class="mt-1 text-2xl font-black text-amber-600">{{ number_format($statusCounts['new']) }}</div>
        </a>
        <a href="{{ route('candidates.index', ['status' => 'interview_process']) }}" class="p-4 rounded-2xl bg-white border border-slate-200/80 shadow-xs hover:border-blue-300 transition-all">
            <span class="text-xs font-semibold text-blue-600">Interview</span>
            <div class="mt-1 text-2xl font-black text-blue-600">{{ number_format($statusCounts['interview_process']) }}</div>
        </a>
        <a href="{{ route('candidates.index', ['status' => 'review_principle']) }}" class="p-4 rounded-2xl bg-white border border-slate-200/80 shadow-xs hover:border-purple-300 transition-all">
            <span class="text-xs font-semibold text-purple-600">Review Klien</span>
            <div class="mt-1 text-2xl font-black text-purple-600">{{ number_format($statusCounts['review_principle']) }}</div>
        </a>
        <a href="{{ route('candidates.index', ['status' => 'passed']) }}" class="p-4 rounded-2xl bg-white border border-slate-200/80 shadow-xs hover:border-emerald-300 transition-all">
            <span class="text-xs font-semibold text-emerald-600">Lolos</span>
            <div class="mt-1 text-2xl font-black text-emerald-600">{{ number_format($statusCounts['passed']) }}</div>
        </a>
        <a href="{{ route('candidates.index', ['status' => 'archived']) }}" class="p-4 rounded-2xl bg-white border border-slate-200/80 shadow-xs hover:border-rose-300 transition-all">
            <span class="text-xs font-semibold text-rose-600">Arsip / Tolak</span>
            <div class="mt-1 text-2xl font-black text-rose-600">{{ number_format($statusCounts['archived']) }}</div>
        </a>
    </div>

    <!-- FILTER & SEARCH BAR -->
    <div class="p-4 rounded-2xl bg-white border border-slate-200/80 shadow-xs">
        <form method="GET" action="{{ route('candidates.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            <input type="hidden" name="status" value="{{ request('status') }}">
            
            <div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, NIK, atau posisi..." class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
            </div>

            <div>
                <select name="area" class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                    <option value="">Semua Area</option>
                    @foreach($distinctAreas as $ar)
                        <option value="{{ $ar }}" {{ request('area') == $ar ? 'selected' : '' }}>{{ $ar }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <select name="principle_id" class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                    <option value="">Semua Prinsiple / Klien</option>
                    @foreach($principles as $p)
                        <option value="{{ $p->id }}" {{ request('principle_id') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex gap-2">
                <button type="submit" class="flex-1 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl transition-all shadow-xs">
                    Terapkan Filter
                </button>
                <a href="{{ route('candidates.index') }}" class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-sm font-medium transition-all" title="Reset Filter">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- CANDIDATES TABLE -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50/80 text-slate-500 text-xs font-bold uppercase tracking-wider border-b border-slate-200/80">
                    <tr>
                        <th class="px-6 py-4">Kandidat</th>
                        <th class="px-6 py-4">Posisi & Area</th>
                        <th class="px-6 py-4">Prinsiple</th>
                        <th class="px-6 py-4 text-center">Status Tes</th>
                        <th class="px-6 py-4 text-center">Status Pipeline</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200/70">
                    @forelse($candidates as $c)
                    <tr class="hover:bg-slate-50/60 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-slate-100 border border-slate-200 flex items-center justify-center font-bold text-slate-700 text-xs shrink-0 overflow-hidden">
                                    @if($c->photo_path)
                                        <img src="{{ asset('storage/' . $c->photo_path) }}" class="w-full h-full object-cover">
                                    @else
                                        {{ strtoupper(substr($c->full_name, 0, 2)) }}
                                    @endif
                                </div>
                                <div>
                                    <div class="font-bold text-slate-900 hover:text-indigo-600 transition-colors">
                                        <a href="{{ route('interview.show', $c) }}">{{ $c->full_name }}</a>
                                    </div>
                                    <div class="text-xs text-slate-400 font-mono">NIK: {{ $c->nik }} • Usia: {{ $c->age ?? '-' }} thn</div>
                                </div>
                            </div>
                        </td>

                        <td class="px-6 py-4">
                            <div class="font-semibold text-slate-800">{{ $c->applied_job }}</div>
                            <div class="text-xs text-slate-500">{{ $c->area }}</div>
                        </td>

                        <td class="px-6 py-4">
                            <span class="text-xs font-semibold px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700">
                                {{ $c->principle ? $c->principle->name : 'Publik / Inhouse' }}
                            </span>
                        </td>

                        <td class="px-6 py-4 text-center">
                            <div class="flex items-center justify-center gap-1.5" title="Kelengkapan Tes Online">
                                <span class="w-2.5 h-2.5 rounded-full {{ $c->testResults->contains('test_type', 'psychology') ? 'bg-emerald-500' : 'bg-slate-300' }}" title="Psikotes"></span>
                                <span class="w-2.5 h-2.5 rounded-full {{ $c->testResults->contains('test_type', 'math') ? 'bg-emerald-500' : 'bg-slate-300' }}" title="Matematika"></span>
                                <span class="w-2.5 h-2.5 rounded-full {{ $c->testResults->contains('test_type', 'computer') ? 'bg-emerald-500' : 'bg-slate-300' }}" title="Komputer"></span>
                            </div>
                        </td>

                        <td class="px-6 py-4 text-center">
                            @php
                                $statusBadges = [
                                    'new' => 'bg-amber-50 text-amber-700 border-amber-200',
                                    'interview_process' => 'bg-blue-50 text-blue-700 border-blue-200',
                                    'review_principle' => 'bg-purple-50 text-purple-700 border-purple-200',
                                    'passed' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                    'rejected' => 'bg-rose-50 text-rose-700 border-rose-200',
                                    'archived' => 'bg-slate-100 text-slate-600 border-slate-200',
                                ];
                            @endphp
                            <span class="inline-flex px-2.5 py-1 text-xs font-bold rounded-full border {{ $statusBadges[$c->status] ?? 'bg-slate-50 text-slate-700 border-slate-200' }}">
                                {{ strtoupper(str_replace('_', ' ', $c->status)) }}
                            </span>
                        </td>

                        <td class="px-6 py-4 text-right space-x-2">
                            <a href="{{ route('interview.show', $c) }}" class="inline-flex items-center px-3 py-1.5 rounded-lg bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-semibold text-xs transition-colors">
                                Evaluasi Interview
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                            Tidak ada data kandidat yang sesuai filter pencarian.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-slate-200/80 bg-slate-50/50">
            {{ $candidates->links() }}
        </div>
    </div>
</div>
@endsection