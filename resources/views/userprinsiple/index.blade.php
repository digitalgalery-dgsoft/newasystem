@extends('layouts.app')

@section('title', 'Master User Prinsiple - ASystem Support System')

@section('content')
<div class="space-y-6">
    <!-- PAGE HEADER -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
        <div class="flex items-center gap-4">
            <div class="w-13 h-13 rounded-2xl bg-gradient-to-br from-blue-600 to-indigo-700 text-white flex items-center justify-center text-2xl shadow-lg shadow-blue-600/20 flex-shrink-0">
                <i class="fa-solid fa-users-viewfinder"></i>
            </div>
            <div>
                <div class="flex flex-wrap items-center gap-2 mb-1">
                    <h1 class="text-2xl font-black text-slate-800 tracking-tight">Master User Prinsiple</h1>
                    @if(!$isAdmin)
                        <span class="badge-pill bg-indigo-50 text-indigo-700 border-indigo-200 font-bold">
                            <i class="fa-solid fa-user-check text-[10px]"></i> DATA SAYA ({{ strtoupper($currentUser->name) }})
                        </span>
                    @else
                        <span class="badge-pill bg-blue-50 text-primary border-blue-200">
                            <i class="fa-solid fa-shield-halved text-[10px]"></i> MODE ADMIN
                        </span>
                    @endif
                    <span class="badge-pill bg-emerald-50 text-emerald-700 border-emerald-200">
                        AKSES KLIEN PRINSIPLE
                    </span>
                </div>
                <p class="text-xs text-slate-500">
                    @if(!$isAdmin)
                        Kelola data kontak PIC prinsiple klien yang Anda tambahkan. Data Anda terisolasi aman dan tidak tercampur dengan user prinsiple lain.
                    @else
                        Kelola seluruh data akun PIC prinsiple klien untuk proses review pelamar dan evaluasi interview prinsiple di sistem.
                    @endif
                </p>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-2.5">
            <button onclick="openModal('addUserModal')" class="btn-att-primary text-xs px-4 py-2.5 shadow-md shadow-primary-600/20">
                <i class="fa-solid fa-user-plus"></i>
                <span>Add User Prinsiple</span>
            </button>
            <a href="{{ route('master.prinsiple.index') }}" class="btn-att-secondary text-xs">
                <i class="fa-solid fa-building-shield text-slate-500"></i>
                <span>Master Prinsiple</span>
            </a>
            <a href="{{ route('interview.index') }}" class="btn-att-secondary text-xs">
                <i class="fa-solid fa-clipboard-user text-slate-500"></i>
                <span>Kandidat Interview</span>
            </a>
        </div>
    </div>

    <!-- FLASH NOTIFICATIONS -->
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-2xl flex items-start gap-3 text-xs shadow-sm">
            <i class="fa-solid fa-circle-check text-emerald-600 text-base mt-0.5 shrink-0"></i>
            <div class="flex-1 font-medium leading-relaxed">{{ session('success') }}</div>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700"><i class="fa-solid fa-xmark"></i></button>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-2xl flex items-start gap-3 text-xs shadow-sm">
            <i class="fa-solid fa-circle-xmark text-rose-600 text-base mt-0.5 shrink-0"></i>
            <div class="flex-1 font-medium leading-relaxed">{{ session('error') }}</div>
            <button type="button" onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-700"><i class="fa-solid fa-xmark"></i></button>
        </div>
    @endif

    @if($errors->any())
        <div class="bg-rose-50 border-2 border-rose-300 text-rose-800 px-5 py-3.5 rounded-2xl text-xs shadow-sm space-y-1.5">
            <div class="flex items-center gap-2 font-bold text-rose-700">
                <i class="fa-solid fa-triangle-exclamation text-sm"></i>
                <span>Gagal Menyimpan Data Master User Prinsiple:</span>
            </div>
            <ul class="list-disc list-inside space-y-0.5 pl-1 font-medium text-rose-600">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- METRIC SUMMARY CARDS -->
    <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- 1. Total User Prinsiple -->
        <div class="stat-box">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">
                        {{ $isAdmin ? 'Total User Prinsiple' : 'User Prinsiple Saya' }}
                    </div>
                    <div class="text-2xl font-black text-slate-900 mt-1">{{ number_format($stats['total']) }}</div>
                    <div class="text-[10px] text-slate-500 font-medium mt-0.5">PIC Prinsiple Terdaftar</div>
                </div>
                <div class="stat-box-icon bg-blue-50 text-primary">
                    <i class="fa-solid fa-users-viewfinder"></i>
                </div>
            </div>
        </div>

        <!-- 2. User Aktif -->
        <div class="stat-box">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">User Aktif</div>
                    <div class="text-2xl font-black text-emerald-600 mt-1">{{ number_format($stats['aktif']) }}</div>
                    <div class="text-[10px] text-emerald-600 font-medium mt-0.5">Akses Login Aktif</div>
                </div>
                <div class="stat-box-icon bg-emerald-50 text-emerald-600">
                    <i class="fa-solid fa-user-shield"></i>
                </div>
            </div>
        </div>

        <!-- 3. Prinsiple Klien -->
        <div class="stat-box">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Prinsiple Terkait</div>
                    <div class="text-2xl font-black text-purple-600 mt-1">{{ $stats['distinct_prinsiple'] }}</div>
                    <div class="text-[10px] text-purple-600 font-medium mt-0.5">Brand / Mitra Klien</div>
                </div>
                <div class="stat-box-icon bg-purple-50 text-purple-600">
                    <i class="fa-solid fa-building-shield"></i>
                </div>
            </div>
        </div>

        <!-- 4. Area Jangkauan -->
        <div class="stat-box">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Cakupan Wilayah</div>
                    <div class="text-2xl font-black text-cyan-600 mt-1">{{ $stats['distinct_area'] }} Area</div>
                    <div class="text-[10px] text-cyan-600 font-medium mt-0.5">Nasional & Regional</div>
                </div>
                <div class="stat-box-icon bg-cyan-50 text-cyan-600">
                    <i class="fa-solid fa-map-location-dot"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- ADVANCE FILTER CARD -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-4">
        <form method="GET" action="{{ route('userprinsiple.index') }}" class="space-y-3">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 lg:grid-cols-12 gap-3">
                <!-- Search Input -->
                <div class="{{ $isAdmin ? 'lg:col-span-3' : 'lg:col-span-4' }}">
                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Pencarian Kata Kunci</label>
                    <div class="relative">
                        <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, email, jabatan, WA..." 
                               class="w-full pl-9 pr-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-primary bg-slate-50/50">
                    </div>
                </div>

                <!-- Prinsiple Filter -->
                <div class="{{ $isAdmin ? 'lg:col-span-2' : 'lg:col-span-3' }}">
                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Prinsiple</label>
                    <select name="prinsiple" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-primary bg-slate-50/50">
                        <option value="">Semua Prinsiple</option>
                        @foreach($principlesList as $p)
                            <option value="{{ $p->name }}" {{ request('prinsiple') == $p->name ? 'selected' : '' }}>
                                {{ $p->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Area Filter -->
                <div class="lg:col-span-2">
                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Area</label>
                    <select name="area" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-primary bg-slate-50/50">
                        <option value="">Semua Area</option>
                        @foreach($distinctAreas as $ar)
                            <option value="{{ $ar }}" {{ request('area') == $ar ? 'selected' : '' }}>
                                {{ $ar }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Status Filter -->
                <div class="lg:col-span-1">
                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Status</label>
                    <select name="status" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-primary bg-slate-50/50">
                        <option value="">Semua</option>
                        <option value="Aktiv" {{ request('status') == 'Aktiv' ? 'selected' : '' }}>Aktif</option>
                        <option value="Nonaktif" {{ request('status') == 'Nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                </div>

                <!-- Admin Creator Filter -->
                @if($isAdmin)
                <div class="lg:col-span-2">
                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Dibuat Oleh</label>
                    <select name="created_by" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-primary bg-slate-50/50">
                        <option value="">Semua User</option>
                        <option value="mine" {{ request('created_by') === 'mine' ? 'selected' : '' }}>Hanya Saya</option>
                        @foreach($allCreators as $cr)
                            <option value="{{ $cr->id }}" {{ request('created_by') == $cr->id ? 'selected' : '' }}>
                                {{ $cr->name }} ({{ $cr->area ?: 'All' }})
                            </option>
                        @endforeach
                    </select>
                </div>
                @endif

                <!-- Action Buttons -->
                <div class="lg:col-span-2 flex items-end gap-2">
                    <button type="submit" class="flex-1 py-2 px-3 rounded-xl bg-slate-900 text-white text-xs font-bold hover:bg-slate-800 transition-all shadow-sm flex items-center justify-center gap-1.5">
                        <i class="fa-solid fa-filter"></i>
                        <span>Filter</span>
                    </button>
                    <a href="{{ route('userprinsiple.index') }}" class="py-2 px-3 rounded-xl border border-slate-200 text-slate-600 text-xs font-semibold hover:bg-slate-100 transition-all" title="Reset Filter">
                        <i class="fa-solid fa-rotate-left"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- TABLE CARD -->
    <div class="table-card bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200/80 flex flex-col sm:flex-row sm:items-center justify-between gap-2 bg-slate-50/40">
            <div>
                <h2 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                    <span>{{ $isAdmin ? 'Daftar User Prinsiple Klien' : 'Daftar Master User Prinsiple Saya' }}</span>
                    <span class="text-[10px] font-extrabold px-2 py-0.5 rounded-full bg-blue-50 text-primary border border-blue-200">
                        {{ $users->total() }} USER
                    </span>
                </h2>
                <p class="text-[11px] text-slate-500">
                    Menampilkan {{ $users->firstItem() ?? 0 }} - {{ $users->lastItem() ?? 0 }} data master user prinsiple yang terdaftar.
                </p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left custom-table text-xs">
                <thead>
                    <tr>
                        <th class="w-12 text-center">NO</th>
                        <th>NAMA LENGKAP &amp; JABATAN</th>
                        <th>PRINSIPLE &amp; AREA</th>
                        <th>KONTAK RESMI</th>
                        <th>PIN AKSES / KATA KUNCI</th>
                        @if($isAdmin)
                            <th>DIBUAT OLEH</th>
                        @endif
                        <th class="text-center">STATUS</th>
                        <th class="text-center min-w-[140px]">AKSI</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($users as $index => $u)
                        @php
                            $badge = $u->status_badge;
                        @endphp
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="text-center font-bold text-slate-400 text-xs">
                                {{ $users->firstItem() + $index }}
                            </td>
                            <td>
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-lg bg-primary-50 text-primary flex items-center justify-center font-black text-xs shrink-0">
                                        {{ strtoupper(substr($u->nama_lengkap, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-slate-900 text-xs">{{ $u->nama_lengkap }}</div>
                                        <div class="text-[11px] text-slate-500 font-medium">{{ $u->jabatan }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="font-bold text-slate-800 text-xs">{{ $u->prinsiple }}</div>
                                <div class="text-[11px] text-slate-400 flex items-center gap-1 mt-0.5">
                                    <i class="fa-solid fa-location-dot text-[10px] text-rose-500"></i>
                                    <span>{{ $u->area ?: 'Nasional' }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="flex items-center gap-1.5 text-[11px] text-slate-700">
                                    <i class="fa-regular fa-envelope text-blue-500"></i>
                                    <a href="mailto:{{ $u->email }}" class="hover:underline truncate max-w-[180px]">{{ $u->email }}</a>
                                </div>
                                @if($u->no_wa)
                                    <div class="flex items-center gap-1.5 text-[11px] text-emerald-700 font-semibold mt-0.5">
                                        <i class="fa-brands fa-whatsapp text-emerald-600"></i>
                                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $u->no_wa) }}" target="_blank" class="hover:underline">{{ $u->no_wa }}</a>
                                    </div>
                                @endif
                            </td>
                            <td x-data="{ showPin: false }">
                                <div class="inline-flex items-center gap-2 px-2.5 py-1 rounded-lg bg-slate-100 border border-slate-200">
                                    <span class="font-mono text-xs font-bold text-slate-800" x-text="showPin ? '{{ $u->katakunci }}' : '••••••'"></span>
                                    <button type="button" @click="showPin = !showPin" class="text-slate-400 hover:text-slate-700 text-xs">
                                        <i :class="showPin ? 'fa-solid fa-eye-slash' : 'fa-solid fa-eye'"></i>
                                    </button>
                                </div>
                            </td>
                            @if($isAdmin)
                            <td>
                                @if($u->creator)
                                    <span class="inline-flex items-center gap-1 text-[10px] font-bold px-2 py-0.5 rounded-md bg-blue-50 text-blue-700 border border-blue-200">
                                        <i class="fa-solid fa-user-pen text-[9px]"></i>
                                        {{ $u->creator->name }}
                                    </span>
                                @else
                                    <span class="text-[10px] text-slate-400 italic">Sistem Legacy</span>
                                @endif
                            </td>
                            @endif
                            <td class="text-center">
                                <span class="inline-flex items-center gap-1 text-[10px] font-bold px-2 py-0.5 rounded-md border {{ $badge['bg'] }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $badge['dot'] }}"></span>
                                    <span>{{ $badge['label'] }}</span>
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="flex items-center justify-center gap-1">
                                    <!-- Kirim Akses Button -->
                                    <form action="{{ route('userprinsiple.send_access', $u->id) }}" method="POST" onsubmit="return confirm('Kirimkan ulang notifikasi akses dan PIN login ke {{ $u->email }}?')">
                                        @csrf
                                        <button type="submit" class="w-8 h-8 rounded-lg bg-amber-50 border border-amber-200 text-amber-600 hover:bg-amber-100 flex items-center justify-center text-xs transition-all shadow-sm" title="Kirim Notifikasi Akses">
                                            <i class="fa-solid fa-paper-plane"></i>
                                        </button>
                                    </form>

                                    <!-- Edit Button -->
                                    <button type="button" onclick="editUserPrinsiple({{ json_encode($u) }})" 
                                            class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center text-xs transition-all" title="Edit Data">
                                        <i class="fa-solid fa-pencil"></i>
                                    </button>

                                    <!-- Hapus Button -->
                                    <form action="{{ route('userprinsiple.destroy', $u->id) }}" method="POST" onsubmit="return confirm('Hapus user prinsiple {{ $u->nama_lengkap }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="w-8 h-8 rounded-lg bg-rose-50 border border-rose-200 text-rose-600 hover:bg-rose-100 flex items-center justify-center text-xs transition-all" title="Hapus User">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $isAdmin ? 8 : 7 }}" class="text-center py-12">
                                <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3 text-lg">
                                    <i class="fa-solid fa-user-shield"></i>
                                </div>
                                <div class="text-sm font-bold text-slate-700">Tidak ada data Master User Prinsiple ditemukan</div>
                                <div class="text-xs text-slate-400 mt-1">
                                    @if(!$isAdmin)
                                        Anda belum menambahkan Master User Prinsiple. Silakan klik tombol "Add User Prinsiple" di atas.
                                    @else
                                        Tidak ada data yang cocok dengan kriteria filter yang dipilih.
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- PAGINATION -->
        @if($users->hasPages())
            <div class="px-6 py-4 border-t border-slate-200 bg-slate-50/50 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-slate-500">
                <div>
                    Menampilkan <span class="font-bold text-slate-800">{{ $users->firstItem() }}</span> - <span class="font-bold text-slate-800">{{ $users->lastItem() }}</span> dari <span class="font-bold text-slate-800">{{ $users->total() }}</span> data
                </div>
                <div>{{ $users->links() }}</div>
            </div>
        @endif
    </div>
</div>

<!-- ========================================== -->
<!-- MODAL: ADD USER PRINSIPLE                  -->
<!-- ========================================== -->
<div id="addUserModal" class="fixed inset-0 z-50 hidden bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4 overflow-y-auto">
    <div class="bg-white rounded-2xl max-w-lg w-full shadow-2xl border border-slate-200 overflow-hidden my-8 animate-scale-up">
        <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between bg-slate-50">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-primary text-white flex items-center justify-center text-sm shadow-sm">
                    <i class="fa-solid fa-user-plus"></i>
                </div>
                <div>
                    <h3 class="text-base font-bold text-slate-900">Add User Prinsiple</h3>
                    <p class="text-[11px] text-slate-500">Daftarkan akun perwakilan prinsiple klien baru.</p>
                </div>
            </div>
            <button onclick="closeModal('addUserModal')" class="text-slate-400 hover:text-slate-700">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <form action="{{ route('userprinsiple.store') }}" method="POST" class="p-6 space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Nama Lengkap <span class="text-rose-500">*</span></label>
                <input type="text" name="nama_lengkap" value="{{ old('nama_lengkap') }}" required placeholder="Contoh: Budi Santoso, S.T." class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-primary focus:outline-none bg-slate-50/50">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Jabatan <span class="text-rose-500">*</span></label>
                    <input type="text" name="jabatan" value="{{ old('jabatan') }}" required placeholder="Contoh: Area Sales Manager" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-primary focus:outline-none bg-slate-50/50">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Area Penugasan <span class="text-rose-500">*</span></label>
                    <input type="text" name="area" value="{{ old('area') }}" required placeholder="Contoh: Surabaya / Nasional" list="areaList" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-primary focus:outline-none bg-slate-50/50">
                    <datalist id="areaList">
                        @foreach($distinctAreas as $ar)
                            <option value="{{ $ar }}">
                        @endforeach
                    </datalist>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Prinsiple Klien <span class="text-rose-500">*</span></label>
                <select name="prinsiple" required class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-primary focus:outline-none bg-slate-50/50">
                    <option value="" disabled {{ old('prinsiple') ? '' : 'selected' }}>-- Pilih Prinsiple --</option>
                    @foreach($principlesList as $p)
                        <option value="{{ $p->name }}" {{ old('prinsiple') == $p->name ? 'selected' : '' }}>{{ $p->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="block text-xs font-bold text-slate-700">Email Resmi <span class="text-rose-500">*</span></label>
                        <span class="text-[10px] text-amber-600 font-semibold">Bebas Duplikat</span>
                    </div>
                    <input type="email" name="email" value="{{ old('email') }}" required placeholder="budi@perusahaan.com" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-primary focus:outline-none bg-slate-50/50">
                </div>
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="block text-xs font-bold text-slate-700">No. WhatsApp / HP <span class="text-rose-500">*</span></label>
                        <span class="text-[10px] text-amber-600 font-semibold">Bebas Duplikat</span>
                    </div>
                    <input type="text" name="no_wa" value="{{ old('no_wa') }}" required placeholder="08123456789" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-primary focus:outline-none bg-slate-50/50 font-mono">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Kata Kunci / PIN Akses</label>
                <input type="text" name="katakunci" value="{{ old('katakunci') }}" placeholder="Kosongkan untuk generate otomatis 6 digit" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-primary focus:outline-none bg-slate-50/50 font-mono">
                <p class="text-[10px] text-slate-400 mt-1">Jika dikosongkan, sistem akan membuatkan PIN acak otomatis 6 digit.</p>
            </div>

            <div class="pt-4 border-t border-slate-200 flex justify-end gap-2">
                <button type="button" onclick="closeModal('addUserModal')" class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 text-xs font-semibold hover:bg-slate-100">Batal</button>
                <button type="submit" class="btn-att-primary text-xs px-5 py-2">Simpan User Prinsiple</button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================== -->
<!-- MODAL: EDIT USER PRINSIPLE                 -->
<!-- ========================================== -->
<div id="editUserModal" class="fixed inset-0 z-50 hidden bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4 overflow-y-auto">
    <div class="bg-white rounded-2xl max-w-lg w-full shadow-2xl border border-slate-200 overflow-hidden my-8 animate-scale-up">
        <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between bg-slate-50">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-indigo-600 text-white flex items-center justify-center text-sm shadow-sm">
                    <i class="fa-solid fa-user-pen"></i>
                </div>
                <div>
                    <h3 class="text-base font-bold text-slate-900">Edit User Prinsiple</h3>
                    <p class="text-[11px] text-slate-500">Perbarui informasi kredensial dan penugasan user.</p>
                </div>
            </div>
            <button onclick="closeModal('editUserModal')" class="text-slate-400 hover:text-slate-700">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <form id="editUserForm" method="POST" class="p-6 space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Nama Lengkap <span class="text-rose-500">*</span></label>
                <input type="text" id="edit_nama" name="nama_lengkap" required class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-primary focus:outline-none bg-slate-50/50">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Jabatan <span class="text-rose-500">*</span></label>
                    <input type="text" id="edit_jabatan" name="jabatan" required class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-primary focus:outline-none bg-slate-50/50">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Area Penugasan <span class="text-rose-500">*</span></label>
                    <input type="text" id="edit_area" name="area" required class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-primary focus:outline-none bg-slate-50/50">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Prinsiple Klien <span class="text-rose-500">*</span></label>
                <select id="edit_prinsiple" name="prinsiple" required class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-primary focus:outline-none bg-slate-50/50">
                    @foreach($principlesList as $p)
                        <option value="{{ $p->name }}">{{ $p->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="block text-xs font-bold text-slate-700">Email Resmi <span class="text-rose-500">*</span></label>
                        <span class="text-[10px] text-amber-600 font-semibold">Bebas Duplikat</span>
                    </div>
                    <input type="email" id="edit_email" name="email" required class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-primary focus:outline-none bg-slate-50/50">
                </div>
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="block text-xs font-bold text-slate-700">No. WhatsApp / HP <span class="text-rose-500">*</span></label>
                        <span class="text-[10px] text-amber-600 font-semibold">Bebas Duplikat</span>
                    </div>
                    <input type="text" id="edit_no_wa" name="no_wa" required class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-primary focus:outline-none bg-slate-50/50 font-mono">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Kata Kunci / PIN</label>
                    <input type="text" id="edit_katakunci" name="katakunci" placeholder="Biarkan kosong jika tidak diubah" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-primary focus:outline-none bg-slate-50/50 font-mono">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Status User <span class="text-rose-500">*</span></label>
                    <select id="edit_status" name="status" required class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-primary focus:outline-none bg-slate-50/50">
                        <option value="Aktiv">Aktiv</option>
                        <option value="Nonaktif">Nonaktif</option>
                    </select>
                </div>
            </div>

            <div class="pt-4 border-t border-slate-200 flex justify-end gap-2">
                <button type="button" onclick="closeModal('editUserModal')" class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 text-xs font-semibold hover:bg-slate-100">Batal</button>
                <button type="submit" class="btn-att-primary text-xs px-5 py-2">Update User Prinsiple</button>
            </div>
        </form>
    </div>
</div>

<script>
function openModal(id) {
    document.getElementById(id).classList.remove('hidden');
}
function closeModal(id) {
    document.getElementById(id).classList.add('hidden');
}

function editUserPrinsiple(user) {
    const form = document.getElementById('editUserForm');
    form.action = `{{ url('user-prinsiple') }}/${user.id}`;
    document.getElementById('edit_nama').value = user.nama_lengkap;
    document.getElementById('edit_jabatan').value = user.jabatan;
    document.getElementById('edit_area').value = user.area;
    document.getElementById('edit_prinsiple').value = user.prinsiple;
    document.getElementById('edit_email').value = user.email;
    document.getElementById('edit_no_wa').value = user.no_wa || '';
    document.getElementById('edit_katakunci').value = user.katakunci || '';
    document.getElementById('edit_status').value = user.status;
    openModal('editUserModal');
}

@if($errors->any())
document.addEventListener('DOMContentLoaded', function() {
    openModal('addUserModal');
});
@endif
</script>
@endsection
