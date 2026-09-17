@extends('layouts.app')

@section('title', 'Master Data Karyawan - Attendance Portal')

@section('content')
<div class="space-y-6">

    <!-- Page Header Card -->
    <div class="page-header-card flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-13 h-13 rounded-2xl bg-gradient-to-br from-emerald-600 to-teal-700 text-white flex items-center justify-center text-2xl shadow-lg shadow-emerald-600/25 flex-shrink-0">
                <i class="fa-solid fa-users-gear"></i>
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight">Data Master Karyawan</h1>
                    <span class="badge-pill bg-emerald-50 text-emerald-700 border-emerald-200">
                        <i class="fa-solid fa-database text-[10px]"></i> MASTER DATA
                    </span>
                    <span class="badge-pill bg-blue-50 text-primary border-blue-200">
                        Inhouse & RateCard
                    </span>
                </div>
                <p class="text-xs text-slate-500 mt-1">
                    Database induk informasi seluruh pegawai aktif, verifikasi komponen gaji, peringatan masa kerja 5 tahun, dan status review.
                </p>
            </div>
        </div>

        <div class="flex items-center gap-2.5 flex-wrap">
            <button onclick="openModal('addEmployeeModal')" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-primary hover:bg-primary-700 text-white text-xs font-bold transition-all shadow-md shadow-primary-600/20">
                <i class="fa-solid fa-user-plus"></i>
                <span>Add Karyawan</span>
            </button>
            <a href="{{ route('master.karyawan.index', ['status' => 'Review']) }}" class="inline-flex items-center gap-2 px-3.5 py-2.5 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 text-xs font-semibold hover:bg-amber-100 transition-all">
                <i class="fa-solid fa-user-clock text-amber-600"></i>
                <span>New Review ({{ $stats['review'] }})</span>
            </a>
            <a href="{{ route('master.karyawan.index', ['tipe' => 'RateCard']) }}" class="inline-flex items-center gap-2 px-3.5 py-2.5 rounded-xl bg-slate-100 border border-slate-200 text-slate-700 text-xs font-semibold hover:bg-slate-200 transition-all">
                <i class="fa-solid fa-id-card"></i>
                <span>Distributor / RateCard</span>
            </a>
        </div>
    </div>

    <!-- Stats Metric Row -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
        <div class="stat-box">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Total Karyawan</div>
                    <div class="text-2xl font-black text-slate-900 mt-1">{{ $stats['total'] }}</div>
                    <div class="text-[10px] text-slate-500 font-medium mt-0.5">Seluruh Database</div>
                </div>
                <div class="stat-box-icon bg-slate-100 text-slate-700">
                    <i class="fa-solid fa-users"></i>
                </div>
            </div>
        </div>

        <div class="stat-box">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Karyawan Aktif</div>
                    <div class="text-2xl font-black text-emerald-600 mt-1">{{ $stats['aktif'] }}</div>
                    <div class="text-[10px] text-emerald-600 font-medium mt-0.5">Operasional Lapangan</div>
                </div>
                <div class="stat-box-icon bg-emerald-50 text-emerald-600">
                    <i class="fa-solid fa-user-check"></i>
                </div>
            </div>
        </div>

        <div class="stat-box">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Review Baru</div>
                    <div class="text-2xl font-black text-amber-600 mt-1">{{ $stats['review'] }}</div>
                    <div class="text-[10px] text-amber-600 font-medium mt-0.5">Perlu Approval HR</div>
                </div>
                <div class="stat-box-icon bg-amber-50 text-amber-600">
                    <i class="fa-solid fa-user-clock"></i>
                </div>
            </div>
        </div>

        <div class="stat-box">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Resign / Nonaktif</div>
                    <div class="text-2xl font-black text-rose-600 mt-1">{{ $stats['resign'] }}</div>
                    <div class="text-[10px] text-rose-500 font-medium mt-0.5">Status Pengunduran</div>
                </div>
                <div class="stat-box-icon bg-rose-50 text-rose-600">
                    <i class="fa-solid fa-user-xmark"></i>
                </div>
            </div>
        </div>

        <div class="stat-box">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Tipe Inhouse</div>
                    <div class="text-2xl font-black text-primary mt-1">{{ $stats['inhouse'] }}</div>
                    <div class="text-[10px] text-slate-500 font-medium mt-0.5">{{ $stats['ratecard'] }} RateCard</div>
                </div>
                <div class="stat-box-icon bg-blue-50 text-primary">
                    <i class="fa-solid fa-building-user"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter & Advance Search Card -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4">
        <form method="GET" action="{{ route('master.karyawan.index') }}" class="space-y-3">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3">
                <!-- Search Keyword -->
                <div class="lg:col-span-2">
                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Cari Karyawan</label>
                    <div class="relative">
                        <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama, NIK, Area, Pimpinan..." 
                               class="w-full pl-9 pr-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent bg-slate-50/50">
                    </div>
                </div>

                <!-- Prinsiple Filter -->
                <div>
                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Prinsiple</label>
                    <select name="prinsiple" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-primary bg-slate-50/50">
                        <option value="">Semua Prinsiple</option>
                        @foreach($distinctPrinciples as $prin)
                            <option value="{{ $prin->name }}" {{ request('prinsiple') == $prin->name ? 'selected' : '' }}>
                                {{ $prin->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Jabatan Filter -->
                <div>
                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Jabatan</label>
                    <select name="jabatan" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-primary bg-slate-50/50">
                        <option value="">Semua Jabatan</option>
                        @foreach($distinctJabatan as $jab)
                            <option value="{{ $jab }}" {{ request('jabatan') == $jab ? 'selected' : '' }}>
                                {{ strtoupper($jab) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Area Filter -->
                <div>
                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Area</label>
                    <select name="area" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-primary bg-slate-50/50">
                        <option value="">Semua Area</option>
                        @foreach($distinctArea as $ar)
                            <option value="{{ $ar }}" {{ request('area') == $ar ? 'selected' : '' }}>
                                {{ $ar }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Status Filter -->
                <div>
                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Status</label>
                    <select name="status" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-primary bg-slate-50/50">
                        <option value="">Semua Status</option>
                        <option value="Aktiv" {{ request('status') == 'Aktiv' ? 'selected' : '' }}>Aktiv (Aktif)</option>
                        <option value="Review" {{ request('status') == 'Review' ? 'selected' : '' }}>Review Baru</option>
                        <option value="Resign" {{ request('status') == 'Resign' ? 'selected' : '' }}>Resign</option>
                    </select>
                </div>
            </div>

            <!-- Action buttons -->
            <div class="flex items-center justify-between pt-2 border-t border-slate-100">
                <div class="flex items-center gap-2 text-xs text-slate-500">
                    <span class="inline-flex items-center gap-1.5">
                        <span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span>
                        <span class="text-rose-600 font-semibold">Teks Merah</span>: Belum ada komponen gaji (sesuai legacy)
                    </span>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('master.karyawan.index') }}" class="px-3 py-1.5 rounded-xl border border-slate-200 text-slate-600 text-xs font-semibold hover:bg-slate-100 transition-all">
                        <i class="fa-solid fa-rotate-left mr-1"></i> Reset
                    </a>
                    <button type="submit" class="px-4 py-1.5 rounded-xl bg-slate-900 text-white text-xs font-bold hover:bg-slate-800 transition-all shadow-sm">
                        <i class="fa-solid fa-filter mr-1"></i> Terapkan Filter
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Employee Custom Table Card -->
    <div class="table-card">
        <div class="px-6 py-4 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
            <div>
                <h2 class="text-sm font-bold text-slate-900">Daftar Karyawan Inhouse & RateCard</h2>
                <p class="text-[11px] text-slate-500">Menampilkan {{ $employees->firstItem() ?? 0 }} - {{ $employees->lastItem() ?? 0 }} dari {{ $employees->total() }} data karyawan</p>
            </div>
            <div class="flex items-center gap-2 text-xs font-semibold">
                <span class="text-slate-400">Tipe:</span>
                <a href="{{ route('master.karyawan.index') }}" class="px-2.5 py-1 rounded-lg {{ !request('tipe') ? 'bg-primary text-white' : 'bg-slate-100 text-slate-600' }}">Semua</a>
                <a href="{{ route('master.karyawan.index', ['tipe' => 'Inhouse']) }}" class="px-2.5 py-1 rounded-lg {{ request('tipe') == 'Inhouse' ? 'bg-primary text-white' : 'bg-slate-100 text-slate-600' }}">Inhouse</a>
                <a href="{{ route('master.karyawan.index', ['tipe' => 'RateCard']) }}" class="px-2.5 py-1 rounded-lg {{ request('tipe') == 'RateCard' ? 'bg-primary text-white' : 'bg-slate-100 text-slate-600' }}">RateCard</a>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left custom-table">
                <thead>
                    <tr>
                        <th class="w-12 text-center">NO</th>
                        <th>NIK (KTP)</th>
                        <th>NAMA KARYAWAN</th>
                        <th>JABATAN & AREA</th>
                        <th>PRINSIPLE</th>
                        <th>PIMPINAN</th>
                        <th>TGL. JOIN</th>
                        <th>5 TAHUN</th>
                        <th class="text-center">STATUS</th>
                        <th class="text-center min-w-[150px]">TOOLS</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($employees as $index => $emp)
                        @php
                            $badge = $emp->status_badge;
                        @endphp
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="text-center font-bold text-slate-400 text-xs">
                                {{ $employees->firstItem() + $index }}
                            </td>
                            <td>
                                <div class="font-mono text-xs font-semibold text-slate-700">{{ $emp->nik }}</div>
                                @if($emp->nip)
                                    <div class="text-[10px] text-slate-400 font-mono">{{ $emp->nip }}</div>
                                @endif
                            </td>
                            <td>
                                <!-- Red color if has_komponen is false (exact replica of datain.php line 141) -->
                                <div class="font-bold text-sm {{ $emp->has_komponen ? 'text-slate-900' : 'text-rose-600 font-extrabold' }}">
                                    {{ $emp->nama_karyawan }}
                                </div>
                                <div class="text-[11px] text-slate-500 flex items-center gap-2 mt-0.5">
                                    <span><i class="fa-regular fa-envelope text-[10px]"></i> {{ $emp->email ?? '-' }}</span>
                                    <span>•</span>
                                    <span><i class="fa-brands fa-whatsapp text-[10px] text-emerald-600"></i> {{ $emp->telepon ?? '-' }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="font-bold text-xs text-slate-800">{{ strtoupper($emp->jabatan) }}</div>
                                <div class="text-[11px] text-slate-500 flex items-center gap-1 mt-0.5">
                                    <i class="fa-solid fa-location-dot text-[10px] text-slate-400"></i>
                                    <span>{{ $emp->area }}</span>
                                    @if($emp->divisi)
                                        <span class="text-slate-300">•</span>
                                        <span class="text-[10px] uppercase text-slate-400">{{ $emp->divisi }}</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <span class="font-semibold text-xs text-slate-800 block">{{ $emp->prinsiple ?? '-' }}</span>
                                <span class="text-[10px] px-1.5 py-0.5 rounded bg-slate-100 text-slate-500 font-medium">
                                    {{ $emp->tipe_karyawan }}
                                </span>
                            </td>
                            <td>
                                <div class="font-semibold text-xs text-slate-700">{{ $emp->pimpinan ?? '-' }}</div>
                                @if($emp->jabatan_pimpinan)
                                    <div class="text-[10px] font-bold text-primary">{{ strtoupper($emp->jabatan_pimpinan) }}</div>
                                @endif
                            </td>
                            <td>
                                <div class="text-xs font-semibold text-slate-700">{{ $emp->formatted_join_date }}</div>
                                <div class="text-[10px] text-slate-400 font-medium mt-0.5">
                                    {{ $emp->years_of_service }}
                                </div>
                            </td>
                            <td>
                                <div class="text-xs font-bold text-slate-800">{{ $emp->five_years_date }}</div>
                                <div class="text-[10px] text-slate-400">Masa Evaluasi</div>
                            </td>
                            <td class="text-center">
                                <span class="badge-pill {{ $badge['bg'] }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $badge['dot'] }}"></span>
                                    <span>{{ $badge['label'] }}</span>
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="inline-flex items-center gap-1">
                                    <!-- Detail button -->
                                    <button onclick="viewEmployeeDetail({{ json_encode($emp) }})" 
                                            class="w-7 h-7 rounded-lg bg-blue-50 text-primary hover:bg-primary hover:text-white flex items-center justify-center text-xs transition-all shadow-sm" title="Detail Profil">
                                        <i class="bx bx-list-ul text-sm"></i>
                                    </button>

                                    <!-- Edit button -->
                                    <button onclick="editEmployee({{ json_encode($emp) }})" 
                                            class="w-7 h-7 rounded-lg bg-amber-50 text-amber-700 hover:bg-amber-600 hover:text-white flex items-center justify-center text-xs transition-all shadow-sm" title="Edit Data">
                                        <i class="bx bx-edit text-sm"></i>
                                    </button>

                                    <!-- Resign button with confirmation -->
                                    @if($emp->status !== 'Resign')
                                        <a href="{{ route('master.karyawan.resign', $emp->id) }}" 
                                           onclick="return confirm('Karyawan Benar Sudah Resign? Konfirmasi perubahan status menjadi Resign.');"
                                           class="w-7 h-7 rounded-lg bg-rose-50 text-rose-700 hover:bg-rose-600 hover:text-white flex items-center justify-center text-xs transition-all shadow-sm" title="Set Resign">
                                            <i class="ri-user-unfollow-line text-sm"></i>
                                        </a>
                                    @endif

                                    <!-- Switch User button -->
                                    <a href="{{ route('master.karyawan.switch', $emp->nik) }}" 
                                       class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-700 hover:bg-emerald-600 hover:text-white flex items-center justify-center text-xs transition-all shadow-sm" title="Switch User Akun">
                                        <i class="ri-user-shared-line text-sm"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center py-12">
                                <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3 text-lg">
                                    <i class="fa-solid fa-user-slash"></i>
                                </div>
                                <div class="text-sm font-bold text-slate-700">Tidak ada data karyawan ditemukan</div>
                                <div class="text-xs text-slate-400 mt-1">Coba sesuaikan kata kunci pencarian atau reset filter.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-slate-200 bg-slate-50/50 flex flex-col sm:flex-row items-center justify-between gap-3">
            <div class="text-xs text-slate-500">
                Menampilkan halaman <span class="font-bold text-slate-800">{{ $employees->currentPage() }}</span> dari <span class="font-bold text-slate-800">{{ $employees->lastPage() }}</span>
            </div>
            <div>
                {{ $employees->links() }}
            </div>
        </div>
    </div>

</div>

<!-- ========================================== -->
<!-- MODAL: ADD KARYAWAN                        -->
<!-- ========================================== -->
<div id="addEmployeeModal" class="fixed inset-0 z-50 hidden bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4 overflow-y-auto">
    <div class="bg-white rounded-2xl max-w-2xl w-full shadow-2xl border border-slate-200 overflow-hidden my-8">
        <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between bg-slate-50">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-primary text-white flex items-center justify-center text-sm shadow-sm">
                    <i class="fa-solid fa-user-plus"></i>
                </div>
                <div>
                    <h3 class="text-base font-bold text-slate-900">Add Karyawan Inhouse</h3>
                    <p class="text-[11px] text-slate-500">Isi formulir data pegawai baru untuk disimpan ke sistem.</p>
                </div>
            </div>
            <button onclick="closeModal('addEmployeeModal')" class="text-slate-400 hover:text-slate-700">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <form action="{{ route('master.karyawan.store') }}" method="POST" class="p-6 space-y-4">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Nomor KTP (NIK) <span class="text-rose-500">*</span></label>
                    <input type="number" name="nik" required placeholder="Contoh: 3171011504950001" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-primary focus:outline-none bg-slate-50/50 font-mono">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Nama Lengkap <span class="text-rose-500">*</span></label>
                    <input type="text" name="nama_karyawan" required placeholder="Nama Lengkap Karyawan" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-primary focus:outline-none bg-slate-50/50">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Email</label>
                    <input type="email" name="email" placeholder="karyawan@arina.co.id" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-primary focus:outline-none bg-slate-50/50">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Nomor HP / WhatsApp <span class="text-rose-500">*</span></label>
                    <input type="text" name="telepon" required placeholder="0812xxxxxxx" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-primary focus:outline-none bg-slate-50/50">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Tanggal Join <span class="text-rose-500">*</span></label>
                    <input type="date" name="tanggal_join" required value="{{ date('Y-m-d') }}" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-primary focus:outline-none bg-slate-50/50">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Area <span class="text-rose-500">*</span></label>
                    <select name="area" required class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-primary bg-slate-50/50">
                        <option value="" disabled selected>Pilih Area</option>
                        @foreach($distinctArea as $ar)
                            <option value="{{ $ar }}">{{ $ar }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Prinsiple <span class="text-rose-500">*</span></label>
                    <select name="prinsiple" required class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-primary bg-slate-50/50">
                        <option value="" disabled selected>Pilih Prinsiple</option>
                        @foreach($distinctPrinciples as $prin)
                            <option value="{{ $prin->name }}">{{ $prin->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Jabatan <span class="text-rose-500">*</span></label>
                    <select name="jabatan" required class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-primary bg-slate-50/50">
                        <option value="" disabled selected>Pilih Jabatan</option>
                        @foreach($distinctJabatan as $jab)
                            <option value="{{ $jab }}">{{ strtoupper($jab) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Divisi</label>
                    <select name="divisi" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-primary bg-slate-50/50">
                        <option value="OPERASIONAL" selected>OPERASIONAL</option>
                        <option value="SALES & MARKETING">SALES & MARKETING</option>
                        <option value="HRD">HRD</option>
                        <option value="FINANCE & GA">FINANCE & GA</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Pimpinan Langsung</label>
                    <select name="pimpinan" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-primary bg-slate-50/50">
                        <option value="" selected>Pilih Pimpinan</option>
                        @foreach($distinctPimpinan as $pim)
                            <option value="{{ $pim->nama_karyawan }}">{{ $pim->nama_karyawan }} - {{ $pim->jabatan }} ({{ $pim->area }})</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="pt-4 border-t border-slate-200 flex items-center justify-end gap-2">
                <button type="button" onclick="closeModal('addEmployeeModal')" class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 text-xs font-semibold hover:bg-slate-100">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-primary text-white text-xs font-bold hover:bg-primary-700 shadow-md shadow-primary-600/20">
                    <i class="bx bx-save mr-1"></i> Simpan Karyawan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================== -->
<!-- MODAL: DETAIL KARYAWAN                     -->
<!-- ========================================== -->
<div id="detailEmployeeModal" class="fixed inset-0 z-50 hidden bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-lg w-full shadow-2xl border border-slate-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between bg-slate-50">
            <h3 class="text-sm font-bold text-slate-900">Detail Lengkap Karyawan</h3>
            <button onclick="closeModal('detailEmployeeModal')" class="text-slate-400 hover:text-slate-700">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <div class="p-6 space-y-3 text-xs" id="detailModalBody">
            <!-- Dynamic JS injection -->
        </div>
        <div class="px-6 py-3 border-t border-slate-200 bg-slate-50 flex justify-end">
            <button onclick="closeModal('detailEmployeeModal')" class="px-4 py-1.5 rounded-xl bg-slate-200 text-slate-700 text-xs font-semibold hover:bg-slate-300">
                Tutup
            </button>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- MODAL: EDIT KARYAWAN                       -->
<!-- ========================================== -->
<div id="editEmployeeModal" class="fixed inset-0 z-50 hidden bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-2xl w-full shadow-2xl border border-slate-200 overflow-hidden my-8">
        <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between bg-slate-50">
            <h3 class="text-sm font-bold text-slate-900">Edit Data Karyawan</h3>
            <button onclick="closeModal('editEmployeeModal')" class="text-slate-400 hover:text-slate-700">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <form id="editEmployeeForm" method="POST" class="p-6 space-y-4">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Nama Karyawan</label>
                    <input type="text" id="edit_nama" name="nama_karyawan" required class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 bg-slate-50">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Jabatan</label>
                    <input type="text" id="edit_jabatan" name="jabatan" required class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 bg-slate-50">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Email</label>
                    <input type="email" id="edit_email" name="email" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 bg-slate-50">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Telepon / WA</label>
                    <input type="text" id="edit_telepon" name="telepon" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 bg-slate-50">
                </div>
            </div>
            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Area</label>
                    <input type="text" id="edit_area" name="area" required class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 bg-slate-50">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Prinsiple</label>
                    <input type="text" id="edit_prinsiple" name="prinsiple" required class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 bg-slate-50">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Status</label>
                    <select id="edit_status" name="status" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 bg-slate-50">
                        <option value="Aktiv">Aktiv</option>
                        <option value="Review">Review</option>
                        <option value="Resign">Resign</option>
                    </select>
                </div>
            </div>
            <input type="hidden" id="edit_tanggal_join" name="tanggal_join">
            <input type="hidden" id="edit_tipe_karyawan" name="tipe_karyawan" value="Inhouse">
            <div class="pt-4 border-t border-slate-200 flex justify-end gap-2">
                <button type="button" onclick="closeModal('editEmployeeModal')" class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 text-xs font-semibold">Batal</button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-primary text-white text-xs font-bold">Update Data</button>
            </div>
        </form>
    </div>
</div>

@endsection

@section('scripts')
<script>
    function openModal(id) {
        document.getElementById(id).classList.remove('hidden');
    }
    function closeModal(id) {
        document.getElementById(id).classList.add('hidden');
    }

    function viewEmployeeDetail(emp) {
        const body = document.getElementById('detailModalBody');
        body.innerHTML = `
            <div class="flex items-center gap-3 p-3 bg-slate-50 rounded-xl mb-3">
                <div class="w-10 h-10 rounded-xl bg-primary text-white flex items-center justify-center font-bold text-base">
                    ${emp.nama_karyawan.charAt(0)}
                </div>
                <div>
                    <div class="text-sm font-bold text-slate-900">${emp.nama_karyawan}</div>
                    <div class="text-slate-500 font-mono text-[11px]">${emp.nik} • ${emp.nip || 'N/A'}</div>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div><span class="text-slate-400 block">Jabatan:</span> <strong class="text-slate-800">${emp.jabatan}</strong></div>
                <div><span class="text-slate-400 block">Area:</span> <strong class="text-slate-800">${emp.area}</strong></div>
                <div><span class="text-slate-400 block">Prinsiple:</span> <strong class="text-slate-800">${emp.prinsiple || '-'}</strong></div>
                <div><span class="text-slate-400 block">Pimpinan:</span> <strong class="text-slate-800">${emp.pimpinan || '-'}</strong></div>
                <div><span class="text-slate-400 block">Tanggal Join:</span> <strong class="text-slate-800">${emp.tanggal_join || '-'}</strong></div>
                <div><span class="text-slate-400 block">Status:</span> <span class="badge-pill bg-blue-50 text-primary border-blue-200 font-bold">${emp.status}</span></div>
                <div><span class="text-slate-400 block">Email:</span> <strong class="text-slate-800">${emp.email || '-'}</strong></div>
                <div><span class="text-slate-400 block">WhatsApp:</span> <strong class="text-emerald-700">${emp.telepon || '-'}</strong></div>
            </div>
        `;
        openModal('detailEmployeeModal');
    }

    function editEmployee(emp) {
        const form = document.getElementById('editEmployeeForm');
        form.action = `/master/karyawan/${emp.id}`;
        document.getElementById('edit_nama').value = emp.nama_karyawan;
        document.getElementById('edit_jabatan').value = emp.jabatan;
        document.getElementById('edit_email').value = emp.email || '';
        document.getElementById('edit_telepon').value = emp.telepon || '';
        document.getElementById('edit_area').value = emp.area;
        document.getElementById('edit_prinsiple').value = emp.prinsiple || '';
        document.getElementById('edit_status').value = emp.status;
        document.getElementById('edit_tanggal_join').value = emp.tanggal_join;
        openModal('editEmployeeModal');
    }
</script>
@endsection