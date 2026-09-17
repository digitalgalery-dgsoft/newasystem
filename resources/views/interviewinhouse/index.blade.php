@extends('layouts.app')

@section('title', 'Kandidat Inhouse - ASystem Support System')

@section('content')
<div class="space-y-6">
    <!-- PAGE HEADER -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-primary mb-1">
                <i class="fa-solid fa-house-user"></i>
                <span>Fitur & Layanan &bull; Interview Inhouse</span>
            </div>
            <h1 class="text-2xl font-black text-slate-800 tracking-tight">Data Kandidat Inhouse</h1>
            <p class="text-xs text-slate-500 mt-0.5">Daftar kandidat inhouse penempatan internal ESA Groups (Arina, Alva, Anugrah, Abadi Berkat Odelia).</p>
        </div>

        <div class="flex flex-wrap items-center gap-2.5">
            <a href="{{ route('interview.export') }}" class="btn-att-secondary text-xs">
                <i class="fa-solid fa-file-excel text-emerald-600"></i>
                <span>Export Data</span>
            </a>
            <a href="{{ route('interview.index') }}" class="btn-att-primary text-xs">
                <i class="fa-solid fa-clipboard-user"></i>
                <span>Kandidat Interview</span>
            </a>
        </div>
    </div>

    <!-- METRIC CARDS -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- 1. Total Inhouse -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm flex items-center gap-4 hover:shadow-md transition-all">
            <div class="w-12 h-12 rounded-xl bg-blue-50 border border-blue-100 text-primary flex items-center justify-center text-xl shrink-0">
                <i class="fa-solid fa-house-user"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Inhouse</p>
                <p class="text-2xl font-black text-slate-800">{{ $totalInhouse }}</p>
                <span class="text-[11px] font-semibold text-blue-600">Aktif Internal</span>
            </div>
        </div>

        <!-- 2. Kandidat Baru -->
        <a href="{{ route('interviewinhouse.index', ['status_replace' => 'Baru']) }}" 
           class="bg-white p-4 rounded-2xl border {{ $statusReplace === 'Baru' ? 'border-emerald-500 ring-2 ring-emerald-100' : 'border-slate-200/80' }} shadow-sm flex items-center gap-4 hover:shadow-md transition-all">
            <div class="w-12 h-12 rounded-xl bg-emerald-50 border border-emerald-100 text-emerald-600 flex items-center justify-center text-xl shrink-0">
                <i class="fa-solid fa-user-plus"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Kandidat Baru</p>
                <p class="text-2xl font-black text-slate-800">{{ $countBaru }}</p>
                <span class="text-[11px] font-semibold text-emerald-600">Formasi Penambahan</span>
            </div>
        </a>

        <!-- 3. Kandidat Replace -->
        <a href="{{ route('interviewinhouse.index', ['status_replace' => 'Replace']) }}" 
           class="bg-white p-4 rounded-2xl border {{ $statusReplace === 'Replace' ? 'border-amber-500 ring-2 ring-amber-100' : 'border-slate-200/80' }} shadow-sm flex items-center gap-4 hover:shadow-md transition-all">
            <div class="w-12 h-12 rounded-xl bg-amber-50 border border-amber-100 text-amber-600 flex items-center justify-center text-xl shrink-0">
                <i class="fa-solid fa-user-gear"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Kandidat Replace</p>
                <p class="text-2xl font-black text-slate-800">{{ $countReplace }}</p>
                <span class="text-[11px] font-semibold text-amber-600">Pengganti Karyawan Resign</span>
            </div>
        </a>

        <!-- 4. Menunggu Approval -->
        <a href="{{ route('interviewinhouse.index', ['status_approval' => 'Pending']) }}" 
           class="bg-white p-4 rounded-2xl border {{ $statusApproval === 'Pending' ? 'border-indigo-500 ring-2 ring-indigo-100' : 'border-slate-200/80' }} shadow-sm flex items-center gap-4 hover:shadow-md transition-all">
            <div class="w-12 h-12 rounded-xl bg-indigo-50 border border-indigo-100 text-indigo-600 flex items-center justify-center text-xl shrink-0">
                <i class="fa-solid fa-clock-rotate-left"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Menunggu Approval</p>
                <p class="text-2xl font-black text-slate-800">{{ $countPending }}</p>
                <span class="text-[11px] font-semibold text-indigo-600">Review HRD / Head</span>
            </div>
        </a>
    </div>

    <!-- MAIN DATA TABLE CONTAINER -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <!-- FILTER & SEARCH BAR -->
        <div class="p-4 border-b border-slate-200/80 flex flex-col md:flex-row items-center justify-between gap-4 bg-slate-50/50">
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('interviewinhouse.index') }}" 
                   class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all {{ empty($statusReplace) && empty($statusApproval) ? 'bg-primary text-white shadow-sm' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
                    Semua Inhouse
                </a>
                <a href="{{ route('interviewinhouse.index', ['status_replace' => 'Baru']) }}" 
                   class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all {{ $statusReplace === 'Baru' ? 'bg-emerald-600 text-white shadow-sm' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
                    Baru ({{ $countBaru }})
                </a>
                <a href="{{ route('interviewinhouse.index', ['status_replace' => 'Replace']) }}" 
                   class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all {{ $statusReplace === 'Replace' ? 'bg-amber-600 text-white shadow-sm' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
                    Replace ({{ $countReplace }})
                </a>
                <a href="{{ route('interviewinhouse.index', ['status_approval' => 'Approve']) }}" 
                   class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all {{ $statusApproval === 'Approve' ? 'bg-blue-600 text-white shadow-sm' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
                    Approved ({{ $countApproved }})
                </a>
            </div>

            <!-- SEARCH FORM -->
            <form action="{{ route('interviewinhouse.index') }}" method="GET" class="flex items-center gap-2 w-full md:w-80">
                @if($statusReplace)
                    <input type="hidden" name="status_replace" value="{{ $statusReplace }}">
                @endif
                @if($statusApproval)
                    <input type="hidden" name="status_approval" value="{{ $statusApproval }}">
                @endif
                <div class="relative w-full">
                    <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                    <input type="text" name="search" value="{{ $search }}" 
                           placeholder="Cari NIK, nama, jabatan, user..." 
                           class="w-full pl-9 pr-8 py-2 text-xs rounded-xl border border-slate-300 focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary-100 bg-white">
                    @if($search)
                        <a href="{{ route('interviewinhouse.index', ['status_replace' => $statusReplace, 'status_approval' => $statusApproval]) }}" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                            <i class="fa-solid fa-xmark text-xs"></i>
                        </a>
                    @endif
                </div>
                <button type="submit" class="btn-att-primary text-xs px-3 py-2 shrink-0">
                    Cari
                </button>
            </form>
        </div>

        <!-- DATA TABLE -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-200 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                        <th class="py-3 px-3.5 text-center w-10">No</th>
                        <th class="py-3 px-3.5">No. KTP</th>
                        <th class="py-3 px-3.5">Nama Kandidat</th>
                        <th class="py-3 px-3.5">Tgl Lahir / Usia</th>
                        <th class="py-3 px-3.5">Pendidikan</th>
                        <th class="py-3 px-3.5">Prinsiple Inhouse</th>
                        <th class="py-3 px-3.5">Jabatan</th>
                        <th class="py-3 px-2.5 text-center" title="Tes Kepribadian (DISC)">Kepribadian</th>
                        <th class="py-3 px-2.5 text-center" title="Tes Matematika">Matematika</th>
                        <th class="py-3 px-2.5 text-center" title="Tes Komputer">Komputer</th>
                        <th class="py-3 px-3.5 text-center">Status</th>
                        <th class="py-3 px-3.5">User Request</th>
                        <th class="py-3 px-3.5 text-center w-28">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 text-xs">
                    @forelse($candidates as $index => $c)
                        @php
                            $isComplete = $c->is_profile_complete || (!empty($c->address_ktp) && !empty($c->education) && !empty($c->phone));
                            $hasPsikotes = $c->psikotes_score !== null || $c->testResults->where('test_type', 'psychology')->isNotEmpty();
                            $hasMath = $c->math_score !== null || $c->testResults->where('test_type', 'math')->isNotEmpty();
                            $hasKomputer = $c->computer_score !== null || $c->testResults->where('test_type', 'computer')->isNotEmpty();
                            $statusReplaceBadge = ($c->status_replace === 'Replace') 
                                ? 'bg-amber-50 text-amber-700 border-amber-200'
                                : 'bg-emerald-50 text-emerald-700 border-emerald-200';
                            $statusApprovalBadge = match($c->status_approval) {
                                'Approve' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                'Tolak' => 'bg-rose-50 text-rose-700 border-rose-200',
                                'Review HRD' => 'bg-sky-50 text-sky-700 border-sky-200',
                                'Review Head' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                                default => 'bg-slate-100 text-slate-600 border-slate-200'
                            };
                        @endphp
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="py-3 px-3.5 text-center text-slate-400 font-medium">
                                {{ $candidates->firstItem() + $index }}
                            </td>
                            <td class="py-3 px-3.5 font-mono text-slate-600 font-medium">
                                {{ $c->nik }}
                            </td>
                            <td class="py-3 px-3.5">
                                <div class="flex items-center gap-2.5">
                                    <div class="relative">
                                        <img src="{{ $c->photo_path ?: 'https://ui-avatars.com/api/?name='.urlencode($c->full_name).'&background=0F52BA&color=fff' }}" 
                                             alt="{{ $c->full_name }}" 
                                             class="w-8 h-8 rounded-lg object-cover border border-slate-200">
                                        <span class="absolute -bottom-0.5 -right-0.5 w-2.5 h-2.5 rounded-full border-2 border-white {{ $isComplete ? 'bg-emerald-500' : 'bg-rose-500' }}"
                                              title="{{ $isComplete ? 'Profil Lengkap' : 'Profil Belum Lengkap' }}"></span>
                                    </div>
                                    <div>
                                        <a href="{{ route('interviewinhouse.show', $c->id) }}" class="font-bold text-slate-800 hover:text-primary transition-colors block">
                                            {{ $c->full_name }}
                                        </a>
                                        <span class="text-[11px] text-slate-400 font-mono">{{ $c->phone ?: $c->whatsapp }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3 px-3.5 whitespace-nowrap">
                                <div class="font-semibold text-slate-700">{{ $c->formatted_birth_date }}</div>
                                <div class="text-[11px] text-slate-400">{{ $c->age }} Tahun</div>
                            </td>
                            <td class="py-3 px-3.5 text-slate-700 font-medium">
                                {{ $c->education ?: '-' }}
                            </td>
                            <td class="py-3 px-3.5">
                                <span class="font-semibold text-slate-800 block text-xs">{{ $c->principle->name ?? 'Internal Inhouse' }}</span>
                                <span class="text-[11px] text-slate-400">{{ $c->area ?: 'JAKARTA' }}</span>
                            </td>
                            <td class="py-3 px-3.5">
                                <span class="font-medium text-slate-700 block text-xs">{{ $c->applied_job }}</span>
                            </td>
                            <!-- Tes Kepribadian -->
                            <td class="py-3 px-2.5 text-center">
                                @if($hasPsikotes)
                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-emerald-100 text-emerald-600 text-xs" title="Sudah Mengerjakan">
                                        <i class="fa-solid fa-check"></i>
                                    </span>
                                @else
                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-rose-100 text-rose-500 text-xs" title="Belum Mengerjakan">
                                        <i class="fa-solid fa-xmark"></i>
                                    </span>
                                @endif
                            </td>
                            <!-- Tes Matematika -->
                            <td class="py-3 px-2.5 text-center">
                                @if($hasMath)
                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-emerald-100 text-emerald-600 text-xs" title="Sudah Mengerjakan">
                                        <i class="fa-solid fa-check"></i>
                                    </span>
                                @else
                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-rose-100 text-rose-500 text-xs" title="Belum Mengerjakan">
                                        <i class="fa-solid fa-xmark"></i>
                                    </span>
                                @endif
                            </td>
                            <!-- Tes Komputer -->
                            <td class="py-3 px-2.5 text-center">
                                @if($hasKomputer)
                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-emerald-100 text-emerald-600 text-xs" title="Sudah Dinilai">
                                        <i class="fa-solid fa-check"></i>
                                    </span>
                                @else
                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-rose-100 text-rose-500 text-xs" title="Belum Dinilai">
                                        <i class="fa-solid fa-xmark"></i>
                                    </span>
                                @endif
                            </td>
                            <!-- Status Pengajuan -->
                            <td class="py-3 px-3.5 text-center whitespace-nowrap">
                                <span class="px-2 py-0.5 rounded text-[11px] font-bold border {{ $statusReplaceBadge }}">
                                    {{ $c->status_replace ?: 'Baru' }}
                                </span>
                                @if($c->status_approval)
                                    <span class="block mt-1 px-1.5 py-0.5 rounded text-[10px] font-semibold border {{ $statusApprovalBadge }}">
                                        {{ $c->status_approval }}
                                    </span>
                                @endif
                            </td>
                            <!-- User Request -->
                            <td class="py-3 px-3.5 text-slate-600 text-xs">
                                <span class="font-medium text-slate-800 block">{{ $c->user_request ?: ($c->useras ?: 'Admin HRD') }}</span>
                            </td>
                            <!-- Aksi -->
                            <td class="py-3 px-3.5 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <!-- WhatsApp Action -->
                                    <a href="{{ $c->wa_url }}" target="_blank" 
                                       class="w-7 h-7 rounded-lg bg-emerald-50 hover:bg-emerald-600 text-emerald-600 hover:text-white border border-emerald-200 transition-colors flex items-center justify-center text-xs"
                                       title="Kirim Akses Test via WhatsApp">
                                        <i class="fa-brands fa-whatsapp"></i>
                                    </a>
                                    <!-- Detail Action -->
                                    <a href="{{ route('interviewinhouse.show', $c->id) }}" 
                                       class="w-7 h-7 rounded-lg bg-primary-50 hover:bg-primary text-primary hover:text-white border border-primary-200 transition-colors flex items-center justify-center text-xs"
                                       title="Lihat Detail & Evaluasi Inhouse">
                                        <i class="fa-solid fa-check"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="13" class="py-12 text-center text-slate-400">
                                <div class="flex flex-col items-center justify-center">
                                    <i class="fa-solid fa-folder-open text-3xl mb-2 text-slate-300"></i>
                                    <p class="font-semibold text-slate-600 text-sm">Tidak ada data kandidat inhouse</p>
                                    <p class="text-xs text-slate-400 mt-1">Belum ada kandidat inhouse dengan kriteria yang dipilih.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- PAGINATION -->
        @if($candidates->hasPages())
            <div class="p-4 border-t border-slate-200 bg-slate-50/50">
                {{ $candidates->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
