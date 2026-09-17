@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-900">Walk Interview</h2>
            <p class="text-xs text-slate-500 mt-0.5">Daftar kandidat walk-in dengan filter rentang tanggal registrasi</p>
        </div>
        <a href="{{ route('interview.index') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-slate-900 text-white hover:bg-slate-800 transition">
            <i class="ri-arrow-left-line"></i>
            <span>Kembali ke Interview</span>
        </a>
    </div>

    <!-- Filter Form -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
        <form action="{{ route('interview.walk') }}" method="GET" class="flex flex-col md:flex-row md:items-end gap-3 text-xs">
            <div class="flex-1">
                <label class="block font-semibold text-slate-700 mb-1">Cari Data:</label>
                <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Nama kandidat atau NIK..." class="w-full rounded-lg border-slate-300 py-1.5 px-3 bg-white text-xs">
            </div>
            <div class="w-full md:w-44">
                <label class="block font-semibold text-slate-700 mb-1">Dari Tanggal:</label>
                <input type="date" name="start_date" value="{{ $startDate }}" class="w-full rounded-lg border-slate-300 py-1.5 px-3 bg-white text-xs">
            </div>
            <div class="w-full md:w-44">
                <label class="block font-semibold text-slate-700 mb-1">Sampai Tanggal:</label>
                <input type="date" name="end_date" value="{{ $endDate }}" class="w-full rounded-lg border-slate-300 py-1.5 px-3 bg-white text-xs">
            </div>
            <div class="flex items-center gap-2">
                <button type="submit" class="px-4 py-2 rounded-lg font-bold bg-amber-500 hover:bg-amber-400 text-slate-950 transition shadow-sm">
                    Filter
                </button>
                <a href="{{ route('interview.walk') }}" class="px-3 py-2 rounded-lg font-semibold bg-slate-200 hover:bg-slate-300 text-slate-800 transition">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead class="bg-slate-100/80 text-slate-700 font-bold border-b border-slate-200">
                    <tr>
                        <th class="py-3 px-3 w-12 text-center">No</th>
                        <th class="py-3 px-3 w-28">Tanggal</th>
                        <th class="py-3 px-3 w-36">No. KTP</th>
                        <th class="py-3 px-4">Nama Kandidat</th>
                        <th class="py-3 px-3 w-24">Tgl. Lahir</th>
                        <th class="py-3 px-3 w-20 text-center">Usia</th>
                        <th class="py-3 px-3">Pendidikan</th>
                        <th class="py-3 px-4">Posisi Dilamar</th>
                        <th class="py-3 px-4">Area</th>
                        <th class="py-3 px-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-800">
                    @forelse($candidates as $idx => $c)
                    <tr class="hover:bg-amber-50/30">
                        <td class="py-2.5 px-3 text-center text-slate-400">{{ $candidates->firstItem() + $idx }}</td>
                        <td class="py-2.5 px-3 text-slate-600 font-mono">{{ $c->created_at->format('d/m/Y') }}</td>
                        <td class="py-2.5 px-3 font-mono font-semibold">{{ $c->nik }}</td>
                        <td class="py-2.5 px-4 font-bold text-slate-950">{{ $c->full_name }}</td>
                        <td class="py-2.5 px-3">{{ $c->formatted_birth_date }}</td>
                        <td class="py-2.5 px-3 text-center">{{ $c->age }} Thn</td>
                        <td class="py-2.5 px-3">{{ $c->education ?? '-' }}</td>
                        <td class="py-2.5 px-4 font-medium">{{ $c->applied_job }}</td>
                        <td class="py-2.5 px-4">{{ $c->area }}</td>
                        <td class="py-2.5 px-3 text-center">
                            <a href="{{ route('interview.show', $c->id) }}" class="inline-flex items-center justify-center w-7 h-7 rounded bg-sky-600 hover:bg-sky-500 text-white font-bold transition">
                                <i class="bx bx-check text-base"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="py-8 text-center text-slate-400">Tidak ada data walk interview untuk rentang tanggal ini.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-100 bg-slate-50/50">
            {{ $candidates->appends(['search' => $search, 'start_date' => $startDate, 'end_date' => $endDate])->links() }}
        </div>
    </div>
</div>
@endsection
