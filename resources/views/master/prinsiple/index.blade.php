@extends('layouts.app')

@section('title', 'Master Data Prinsiple - Attendance Portal')

@section('content')
<div class="space-y-6">

    <!-- Page Header Card -->
    <div class="page-header-card flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-13 h-13 rounded-2xl bg-gradient-to-br from-purple-600 to-indigo-700 text-white flex items-center justify-center text-2xl shadow-lg shadow-purple-600/25 flex-shrink-0">
                <i class="fa-solid fa-building-shield"></i>
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight">Data Master Prinsiple</h1>
                    <span class="badge-pill bg-purple-50 text-purple-700 border-purple-200">
                        <i class="fa-solid fa-database text-[10px]"></i> MASTER DATA
                    </span>
                    <span class="badge-pill bg-blue-50 text-primary border-blue-200">
                        Mitra & Klien
                    </span>
                </div>
                <p class="text-xs text-slate-500 mt-1">
                    Direktori lengkap rekanan kerja sama prinsiple dan pengelompokan afiliasi ke 5 entitas inhouse (AMK, AKP, ATK, ABO, ATB).
                </p>
            </div>
        </div>

        <div class="flex items-center gap-2.5">
            <button onclick="openModal('addPrincipleModal')" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-purple-600 hover:bg-purple-700 text-white text-xs font-bold transition-all shadow-md shadow-purple-600/20">
                <i class="fa-solid fa-plus"></i>
                <span>Add Prinsiple</span>
            </button>
        </div>
    </div>

    <!-- Stats Metric Row -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
        <div class="stat-box">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Total Prinsiple</div>
                    <div class="text-2xl font-black text-slate-900 mt-1">{{ $stats['total'] }}</div>
                    <div class="text-[10px] text-slate-500 font-medium mt-0.5">Mitra Terdaftar</div>
                </div>
                <div class="stat-box-icon bg-slate-100 text-slate-700">
                    <i class="fa-solid fa-building"></i>
                </div>
            </div>
        </div>

        <div class="stat-box">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Prinsiple Aktif</div>
                    <div class="text-2xl font-black text-purple-600 mt-1">{{ $stats['active'] }}</div>
                    <div class="text-[10px] text-purple-600 font-medium mt-0.5">Aktif Kerja Sama</div>
                </div>
                <div class="stat-box-icon bg-purple-50 text-purple-600">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
            </div>
        </div>

        <div class="stat-box">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Non-Aktif</div>
                    <div class="text-2xl font-black text-slate-500 mt-1">{{ $stats['inactive'] }}</div>
                    <div class="text-[10px] text-slate-400 font-medium mt-0.5">Berhenti / Arsip</div>
                </div>
                <div class="stat-box-icon bg-slate-100 text-slate-500">
                    <i class="fa-solid fa-ban"></i>
                </div>
            </div>
        </div>

        <div class="stat-box">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">5 Entitas Inhouse</div>
                    <div class="text-2xl font-black text-primary mt-1">{{ $stats['parents'] }}</div>
                    <div class="text-[10px] text-slate-500 font-medium mt-0.5">AMK, AKP, ATK, ABO, ATB</div>
                </div>
                <div class="stat-box-icon bg-blue-50 text-primary">
                    <i class="fa-solid fa-network-wired"></i>
                </div>
            </div>
        </div>

        <div class="stat-box">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Karyawan Terafiliasi</div>
                    <div class="text-2xl font-black text-emerald-600 mt-1">{{ $stats['total_employees'] }}</div>
                    <div class="text-[10px] text-emerald-600 font-medium mt-0.5">Staf & Promotor</div>
                </div>
                <div class="stat-box-icon bg-emerald-50 text-emerald-600">
                    <i class="fa-solid fa-users"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Entity Quick Filters -->
    <div class="flex flex-wrap items-center gap-2 pt-1 pb-1">
        <a href="{{ route('master.prinsiple.index') }}" 
           class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all border {{ !request('entity') ? 'bg-purple-600 text-white border-purple-600 shadow-sm' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50' }}">
            Semua ({{ $stats['total'] }})
        </a>
        <a href="{{ route('master.prinsiple.index', ['entity' => 'AMK']) }}" 
           class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all border {{ request('entity') === 'AMK' ? 'bg-blue-600 text-white border-blue-600 shadow-sm' : 'bg-blue-50 text-blue-700 border-blue-200 hover:bg-blue-100' }}">
            AMK - Arina ({{ $stats['by_entity']['AMK'] ?? 0 }})
        </a>
        <a href="{{ route('master.prinsiple.index', ['entity' => 'AKP']) }}" 
           class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all border {{ request('entity') === 'AKP' ? 'bg-amber-600 text-white border-amber-600 shadow-sm' : 'bg-amber-50 text-amber-700 border-amber-200 hover:bg-amber-100' }}">
            AKP - Alva ({{ $stats['by_entity']['AKP'] ?? 0 }})
        </a>
        <a href="{{ route('master.prinsiple.index', ['entity' => 'ATK']) }}" 
           class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all border {{ request('entity') === 'ATK' ? 'bg-emerald-600 text-white border-emerald-600 shadow-sm' : 'bg-emerald-50 text-emerald-700 border-emerald-200 hover:bg-emerald-100' }}">
            ATK - Anugrah Terpercaya ({{ $stats['by_entity']['ATK'] ?? 0 }})
        </a>
        <a href="{{ route('master.prinsiple.index', ['entity' => 'ABO']) }}" 
           class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all border {{ request('entity') === 'ABO' ? 'bg-purple-600 text-white border-purple-600 shadow-sm' : 'bg-purple-50 text-purple-700 border-purple-200 hover:bg-purple-100' }}">
            ABO - Abadi Berkat ({{ $stats['by_entity']['ABO'] ?? 0 }})
        </a>
        <a href="{{ route('master.prinsiple.index', ['entity' => 'ATB']) }}" 
           class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all border {{ request('entity') === 'ATB' ? 'bg-rose-600 text-white border-rose-600 shadow-sm' : 'bg-rose-50 text-rose-700 border-rose-200 hover:bg-rose-100' }}">
            ATB - Anugrah Talenta ({{ $stats['by_entity']['ATB'] ?? 0 }})
        </a>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4">
        <form method="GET" action="{{ route('master.prinsiple.index') }}" class="flex flex-col sm:flex-row items-center justify-between gap-3">
            <div class="grid grid-cols-1 sm:grid-cols-4 gap-3 w-full sm:w-auto flex-1">
                <!-- Search Keyword -->
                <div>
                    <div class="relative">
                        <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Kode, Nama Prinsiple..." 
                               class="w-full pl-9 pr-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-purple-600 bg-slate-50/50">
                    </div>
                </div>

                <!-- Entity Filter -->
                <div>
                    <select name="entity" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-purple-600 bg-slate-50/50">
                        <option value="">Semua Entitas</option>
                        @foreach($availableEntities as $ent)
                            <option value="{{ $ent }}" {{ request('entity') == $ent ? 'selected' : '' }}>
                                Entitas {{ $ent }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Induk Filter -->
                <div>
                    <select name="induk" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-purple-600 bg-slate-50/50">
                        <option value="">Semua Inhouse / Induk</option>
                        @foreach($distinctParents as $par)
                            <option value="{{ $par }}" {{ request('induk') == $par ? 'selected' : '' }}>
                                {{ $par }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Status Filter -->
                <div>
                    <select name="status" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-purple-600 bg-slate-50/50">
                        <option value="">Semua Status</option>
                        <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Aktif</option>
                        <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Non-Aktif</option>
                    </select>
                </div>
            </div>

            <div class="flex items-center gap-2 self-end sm:self-center">
                <a href="{{ route('master.prinsiple.index') }}" class="px-3 py-2 rounded-xl border border-slate-200 text-slate-600 text-xs font-semibold hover:bg-slate-100 transition-all">
                    <i class="fa-solid fa-rotate-left mr-1"></i> Reset
                </a>
                <button type="submit" class="px-4 py-2 rounded-xl bg-purple-600 text-white text-xs font-bold hover:bg-purple-700 transition-all shadow-sm">
                    <i class="fa-solid fa-filter mr-1"></i> Cari
                </button>
            </div>
        </form>
    </div>

    <!-- Principles Custom Table Card -->
    <div class="table-card">
        <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between">
            <div>
                <h2 class="text-sm font-bold text-slate-900">Daftar Mitra Prinsiple</h2>
                <p class="text-[11px] text-slate-500">Menampilkan {{ $principles->firstItem() ?? 0 }} - {{ $principles->lastItem() ?? 0 }} dari {{ $principles->total() }} mitra kerja</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left custom-table">
                <thead>
                    <tr>
                        <th class="w-12 text-center">NO</th>
                        <th class="w-32">KODE PRINSIPLE</th>
                        <th>NAMA PRINSIPLE</th>
                        <th>ENTITAS & INHOUSE</th>
                        <th>PIC & KONTAK</th>
                        <th class="text-center">KARYAWAN</th>
                        <th class="text-center">STATUS</th>
                        <th class="text-center w-28">TOOLS</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($principles as $index => $prin)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="text-center font-bold text-slate-400 text-xs">
                                {{ $principles->firstItem() + $index }}
                            </td>
                            <td>
                                <span class="font-mono text-xs font-bold px-2 py-1 rounded bg-slate-100 text-slate-700 border border-slate-200">
                                    {{ $prin->code ?? ('PRN-' . str_pad($prin->id, 3, '0', STR_PAD_LEFT)) }}
                                </span>
                            </td>
                            <td>
                                <div class="font-bold text-sm text-slate-900">{{ $prin->name }}</div>
                                <div class="text-[11px] text-slate-400">ID System: #{{ $prin->id }}</div>
                            </td>
                            <td>
                                <div class="flex flex-col gap-1 items-start">
                                    @php
                                        $colors = $prin->entity_color ?? ['bg' => 'bg-slate-100', 'text' => 'text-slate-700', 'border' => 'border-slate-200', 'dot' => 'bg-slate-400'];
                                    @endphp
                                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-md text-[10px] font-extrabold {{ $colors['bg'] }} {{ $colors['text'] }} border {{ $colors['border'] }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $colors['dot'] }}"></span>
                                        {{ $prin->entity ?? 'INHOUSE' }}
                                    </span>
                                    <span class="text-xs text-slate-700 font-medium">
                                        {{ $prin->parent_company ?? 'Inhouse' }}
                                    </span>
                                </div>
                            </td>
                            <td>
                                <div class="text-xs font-semibold text-slate-800">{{ $prin->pic_name ?? '-' }}</div>
                                <div class="text-[11px] text-slate-500 flex items-center gap-2 mt-0.5">
                                    <span>{{ $prin->pic_email ?? '-' }}</span>
                                    @if($prin->pic_phone)
                                        <span>•</span>
                                        <span class="text-emerald-600 font-medium">{{ $prin->pic_phone }}</span>
                                    @endif
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-blue-50 text-primary border border-blue-200">
                                    {{ $prin->employees_count }} Karyawan
                                </span>
                            </td>
                            <td class="text-center">
                                @if($prin->is_active)
                                    <a href="{{ route('master.prinsiple.toggle', $prin->id) }}" 
                                       title="Klik untuk non-aktifkan"
                                       class="badge-pill bg-emerald-50 text-emerald-700 border-emerald-200 hover:bg-emerald-100 transition-all">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        <span>Aktif</span>
                                    </a>
                                @else
                                    <a href="{{ route('master.prinsiple.toggle', $prin->id) }}" 
                                       title="Klik untuk aktifkan"
                                       class="badge-pill bg-slate-100 text-slate-600 border-slate-200 hover:bg-slate-200 transition-all">
                                        <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                        <span>Non-Aktif</span>
                                    </a>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="inline-flex items-center gap-1.5">
                                    <!-- Edit Button -->
                                    <button onclick="editPrinciple({{ json_encode($prin) }})" 
                                            class="w-7 h-7 rounded-lg bg-amber-50 text-amber-700 hover:bg-amber-600 hover:text-white flex items-center justify-center text-xs transition-all shadow-sm" title="Edit Data">
                                        <i class="bx bx-edit text-sm"></i>
                                    </button>

                                    <!-- Delete Button -->
                                    <form action="{{ route('master.prinsiple.destroy', $prin->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin mau hapus data prinsiple ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="w-7 h-7 rounded-lg bg-rose-50 text-rose-700 hover:bg-rose-600 hover:text-white flex items-center justify-center text-xs transition-all shadow-sm" title="Hapus">
                                            <i class="bx bxs-trash text-sm"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-12">
                                <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3 text-lg">
                                    <i class="fa-solid fa-building-circle-xmark"></i>
                                </div>
                                <div class="text-sm font-bold text-slate-700">Tidak ada data prinsiple ditemukan</div>
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
                Menampilkan halaman <span class="font-bold text-slate-800">{{ $principles->currentPage() }}</span> dari <span class="font-bold text-slate-800">{{ $principles->lastPage() }}</span>
            </div>
            <div>
                {{ $principles->links() }}
            </div>
        </div>
    </div>

</div>

<!-- ========================================== -->
<!-- MODAL: ADD PRINSIPLE                       -->
<!-- ========================================== -->
<div id="addPrincipleModal" class="fixed inset-0 z-50 hidden bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-lg w-full shadow-2xl border border-slate-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between bg-slate-50">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-purple-600 text-white flex items-center justify-center text-sm shadow-sm">
                    <i class="fa-solid fa-building"></i>
                </div>
                <div>
                    <h3 class="text-base font-bold text-slate-900">Add Prinsiple</h3>
                    <p class="text-[11px] text-slate-500">Tambah mitra prinsiple dan relasi entitas inhouse.</p>
                </div>
            </div>
            <button onclick="closeModal('addPrincipleModal')" class="text-slate-400 hover:text-slate-700">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <form action="{{ route('master.prinsiple.store') }}" method="POST" class="p-6 space-y-4">
            @csrf
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Kode Prinsiple <span class="text-rose-500">*</span></label>
                    <input type="text" name="code" required placeholder="Contoh: PRN-AMK-060" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-purple-600 bg-slate-50/50 font-mono">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Entitas Inhouse <span class="text-rose-500">*</span></label>
                    <select name="entity" id="add_entity" required onchange="syncInhouse('add')" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-purple-600 bg-slate-50/50">
                        <option value="" disabled selected>Pilih Entitas</option>
                        <option value="AMK">AMK - Arina</option>
                        <option value="AKP">AKP - Alva</option>
                        <option value="ATK">ATK - Anugrah Terpercaya</option>
                        <option value="ABO">ABO - Abadi Berkat</option>
                        <option value="ATB">ATB - Anugrah Talenta</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Nama Prinsiple <span class="text-rose-500">*</span></label>
                <input type="text" name="name" required placeholder="Contoh: PT Unilever Indonesia" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-purple-600 bg-slate-50/50">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Inhouse (Perusahaan Induk) <span class="text-rose-500">*</span></label>
                <select name="parent_company" id="add_parent_company" required class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-purple-600 bg-slate-50/50">
                    <option value="" disabled selected>Pilih Inhouse</option>
                    <option value="PT ARINA MULTI KARYA">PT ARINA MULTI KARYA (AMK)</option>
                    <option value="PT ALVA KARYA PERKASA">PT ALVA KARYA PERKASA (AKP)</option>
                    <option value="PT ANUGRAH TERPERCAYA KERJA">PT ANUGRAH TERPERCAYA KERJA (ATK)</option>
                    <option value="PT ABADI BERKAT ODELIA">PT ABADI BERKAT ODELIA (ABO)</option>
                    <option value="PT ANUGRAH TALENTA BERKARYA">PT ANUGRAH TALENTA BERKARYA (ATB)</option>
                </select>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Nama PIC</label>
                    <input type="text" name="pic_name" placeholder="Nama Contact Person" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-purple-600 bg-slate-50/50">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Nomor Telepon PIC</label>
                    <input type="text" name="pic_phone" placeholder="0812xxxxxxxx" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-purple-600 bg-slate-50/50">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Email PIC</label>
                <input type="email" name="pic_email" placeholder="pic@prinsiple.com" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-purple-600 bg-slate-50/50">
            </div>

            <div class="pt-4 border-t border-slate-200 flex items-center justify-end gap-2">
                <button type="button" onclick="closeModal('addPrincipleModal')" class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 text-xs font-semibold hover:bg-slate-100">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-purple-600 text-white text-xs font-bold hover:bg-purple-700 shadow-md shadow-purple-600/20">
                    <i class="bx bx-save mr-1"></i> Simpan Prinsiple
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================== -->
<!-- MODAL: EDIT PRINSIPLE                      -->
<!-- ========================================== -->
<div id="editPrincipleModal" class="fixed inset-0 z-50 hidden bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-lg w-full shadow-2xl border border-slate-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between bg-slate-50">
            <h3 class="text-base font-bold text-slate-900">Edit Data Prinsiple</h3>
            <button onclick="closeModal('editPrincipleModal')" class="text-slate-400 hover:text-slate-700">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <form id="editPrincipleForm" method="POST" class="p-6 space-y-4">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Kode Prinsiple</label>
                    <input type="text" id="edit_code" name="code" required class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 bg-slate-50 font-mono">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Entitas Inhouse <span class="text-rose-500">*</span></label>
                    <select name="entity" id="edit_entity" required onchange="syncInhouse('edit')" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 bg-slate-50">
                        <option value="AMK">AMK - Arina</option>
                        <option value="AKP">AKP - Alva</option>
                        <option value="ATK">ATK - Anugrah Terpercaya</option>
                        <option value="ABO">ABO - Abadi Berkat</option>
                        <option value="ATB">ATB - Anugrah Talenta</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Nama Prinsiple</label>
                <input type="text" id="edit_name" name="name" required class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 bg-slate-50">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Inhouse (Perusahaan Induk)</label>
                <select id="edit_parent_company" name="parent_company" required class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 bg-slate-50">
                    <option value="PT ARINA MULTI KARYA">PT ARINA MULTI KARYA (AMK)</option>
                    <option value="PT ALVA KARYA PERKASA">PT ALVA KARYA PERKASA (AKP)</option>
                    <option value="PT ANUGRAH TERPERCAYA KERJA">PT ANUGRAH TERPERCAYA KERJA (ATK)</option>
                    <option value="PT ABADI BERKAT ODELIA">PT ABADI BERKAT ODELIA (ABO)</option>
                    <option value="PT ANUGRAH TALENTA BERKARYA">PT ANUGRAH TALENTA BERKARYA (ATB)</option>
                </select>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Nama PIC</label>
                    <input type="text" id="edit_pic_name" name="pic_name" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 bg-slate-50">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Telepon PIC</label>
                    <input type="text" id="edit_pic_phone" name="pic_phone" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 bg-slate-50">
                </div>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Email PIC</label>
                <input type="email" id="edit_pic_email" name="pic_email" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 bg-slate-50">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Status Aktif</label>
                <select id="edit_is_active" name="is_active" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 bg-slate-50">
                    <option value="1">Aktif</option>
                    <option value="0">Non-Aktif</option>
                </select>
            </div>

            <div class="pt-4 border-t border-slate-200 flex justify-end gap-2">
                <button type="button" onclick="closeModal('editPrincipleModal')" class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 text-xs font-semibold">Batal</button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-purple-600 text-white text-xs font-bold">Update Prinsiple</button>
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

    const entityInhouseMap = {
        'AMK': 'PT ARINA MULTI KARYA',
        'AKP': 'PT ALVA KARYA PERKASA',
        'ATK': 'PT ANUGRAH TERPERCAYA KERJA',
        'ABO': 'PT ABADI BERKAT ODELIA',
        'ATB': 'PT ANUGRAH TALENTA BERKARYA'
    };

    function syncInhouse(mode) {
        const entitySel = document.getElementById(mode === 'add' ? 'add_entity' : 'edit_entity');
        const inhouseSel = document.getElementById(mode === 'add' ? 'add_parent_company' : 'edit_parent_company');
        const val = entitySel.value;
        if (entityInhouseMap[val]) {
            inhouseSel.value = entityInhouseMap[val];
        }
    }

    function editPrinciple(prin) {
        const form = document.getElementById('editPrincipleForm');
        form.action = `/master/prinsiple/${prin.id}`;
        document.getElementById('edit_code').value = prin.code || '';
        document.getElementById('edit_name').value = prin.name;
        document.getElementById('edit_entity').value = (prin.entity || 'AMK').toUpperCase();
        document.getElementById('edit_parent_company').value = prin.parent_company || (entityInhouseMap[prin.entity] || 'PT ARINA MULTI KARYA');
        document.getElementById('edit_pic_name').value = prin.pic_name || '';
        document.getElementById('edit_pic_phone').value = prin.pic_phone || '';
        document.getElementById('edit_pic_email').value = prin.pic_email || '';
        document.getElementById('edit_is_active').value = prin.is_active ? '1' : '0';
        openModal('editPrincipleModal');
    }
</script>
@endsection