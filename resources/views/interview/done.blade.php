@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-900">Kandidat Done (Sudah Dapat Prinsiple)</h2>
            <p class="text-xs text-slate-500 mt-0.5">Daftar kandidat yang telah selesai proses interview dan diterima oleh prinsiple</p>
        </div>
        <a href="{{ route('interview.index') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-slate-900 text-white hover:bg-slate-800 transition">
            <i class="ri-arrow-left-line"></i>
            <span>Kembali ke Interview</span>
        </a>
    </div>

    <!-- Search & Filter Box -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
        <form action="{{ route('interview.done') }}" method="GET" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
            <div class="relative flex-1">
                <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Cari berdasarkan nama atau NIK..." class="w-full pl-9 pr-4 py-2 text-xs rounded-lg border-slate-300 bg-white">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                    <i class="ri-search-line"></i>
                </div>
            </div>

            @if(isset($allRecruiters) && count($allRecruiters) > 0)
            <div class="w-full sm:w-72">
                <select name="filter_user" onchange="this.form.submit()" class="w-full text-xs rounded-lg border-slate-300 bg-white py-2 px-3 font-medium">
                    <option value="all" {{ ($filterUser === 'all' || empty($filterUser)) ? 'selected' : '' }}>-- Semua Rekruter (Nasional) --</option>
                    @foreach($allRecruiters as $rec)
                        <option value="{{ $rec->useras }}" {{ ($filterUser ?? '') === $rec->useras ? 'selected' : '' }}>
                            {{ $rec->display_name }} ({{ number_format($rec->total) }})
                        </option>
                    @endforeach
                </select>
            </div>
            @endif

            <div class="flex items-center gap-2">
                <button type="submit" class="px-4 py-2 rounded-lg text-xs font-bold bg-amber-500 hover:bg-amber-400 text-slate-950 transition">
                    Cari
                </button>
                @if(!empty($search) || (!empty($filterUser) && $filterUser !== 'all'))
                    <a href="{{ route('interview.done') }}" class="px-3 py-2 rounded-lg text-xs font-medium bg-slate-100 hover:bg-slate-200 text-slate-700 transition" title="Reset filter">
                        <i class="ri-refresh-line"></i>
                    </a>
                @endif
                <span class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 whitespace-nowrap">
                    <i class="ri-checkbox-circle-line"></i>
                    <span>{{ number_format($candidates->total()) }} Kandidat Selesai</span>
                </span>
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead class="bg-slate-100/80 text-slate-700 font-bold border-b border-slate-200">
                    <tr>
                        <th class="py-3 px-3 w-12 text-center">NO</th>
                        <th class="py-3 px-3 w-36">NO. KTP</th>
                        <th class="py-3 px-4">NAMA KANDIDAT</th>
                        <th class="py-3 px-3">JENIS KELAMIN</th>
                        <th class="py-3 px-3 w-28">TGL. LAHIR</th>
                        <th class="py-3 px-3 w-20 text-center">USIA</th>
                        <th class="py-3 px-3">PENDIDIKAN</th>
                        <th class="py-3 px-4">PRINSIPLE</th>
                        <th class="py-3 px-3">JABATAN</th>
                        <th class="py-3 px-4">NOTE PRINSIPLE</th>
                        <th class="py-3 px-3 text-center">AKSI</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-800">
                    @forelse($candidates as $idx => $c)
                    <tr class="hover:bg-amber-50/30">
                        <td class="py-2.5 px-3 text-center text-slate-400">{{ $candidates->firstItem() + $idx }}</td>
                        <td class="py-2.5 px-3 font-mono font-semibold">{{ $c->nik }}</td>
                        <td class="py-2.5 px-4 font-bold text-slate-950">{{ $c->full_name }}</td>
                        <td class="py-2.5 px-3">
                            @if(strtolower($c->gender ?? '') === 'perempuan')
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-bold bg-pink-50 text-pink-700 border border-pink-200">
                                    <i class="fa-solid fa-venus text-[10px]"></i> Perempuan
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                    <i class="fa-solid fa-mars text-[10px]"></i> Laki-laki
                                </span>
                            @endif
                        </td>
                        <td class="py-2.5 px-3">{{ $c->formatted_birth_date }}</td>
                        <td class="py-2.5 px-3 text-center">{{ $c->age }} Thn</td>
                        <td class="py-2.5 px-3">{{ $c->education ?? '-' }}</td>
                        <td class="py-2.5 px-4 font-medium text-blue-900">{{ $c->principle->name ?? '-' }}</td>
                        <td class="py-2.5 px-3">{{ $c->applied_job }}</td>
                        <td class="py-2.5 px-4 font-semibold text-emerald-700">{{ $c->note_principle ?? 'Accepted' }}</td>
                        <td class="py-2.5 px-3 text-center">
                            <a href="{{ route('interview.show', $c->id) }}" class="inline-flex items-center justify-center w-7 h-7 rounded bg-sky-600 hover:bg-sky-500 text-white font-bold transition">
                                <i class="bx bx-check text-base"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="11" class="py-8 text-center text-slate-400">Belum ada kandidat berstatus done.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-100 bg-slate-50/50">
            {{ $candidates->appends(['search' => $search, 'filter_user' => $filterUser])->links() }}
        </div>
    </div>
</div>
@endsection
