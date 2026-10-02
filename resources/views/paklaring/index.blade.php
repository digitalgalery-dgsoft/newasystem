@extends('layouts.app')

@section('title', 'Daftar Pengajuan Veklaring - Support System ESA Groups')

@section('content')
<div class="space-y-6">

    <!-- Header Section -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-xs">
        <div class="space-y-1">
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-primary/10 text-primary border border-primary/20">
                    Modul Veklaring
                </span>
                <span class="text-xs text-slate-400 font-medium">&bull; Approval Bertingkat (Area &rarr; HRD &rarr; DB &rarr; BPJS)</span>
            </div>
            <h1 class="text-xl font-black text-slate-800 tracking-tight flex items-center gap-2">
                <i class="fa-solid fa-file-contract text-primary text-lg"></i>
                <span>Daftar Pengajuan Surat Referensi Kerja (Veklaring)</span>
            </h1>
            <p class="text-xs text-slate-500">
                Kelola proses persetujuan bertingkat, verifikasi berkas pengunduran diri, serah terima aset, dan penerbitan nomor surat referensi kerja resmi.
            </p>
        </div>
    </div>

    @if(session('success'))
    <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-medium flex items-center justify-between shadow-xs">
        <div class="flex items-center gap-2">
            <i class="fa-solid fa-circle-check text-emerald-600 text-base"></i>
            <span>{{ session('success') }}</span>
        </div>
        <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">
            <i class="fa-solid fa-xmark text-sm"></i>
        </button>
    </div>
    @endif

    @if(session('error'))
    <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-medium flex items-center justify-between shadow-xs">
        <div class="flex items-center gap-2">
            <i class="fa-solid fa-circle-exclamation text-rose-600 text-base"></i>
            <span>{{ session('error') }}</span>
        </div>
        <button type="button" onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-700">
            <i class="fa-solid fa-xmark text-sm"></i>
        </button>
    </div>
    @endif

    <!-- Tab Navigation with Live Badges -->
    <div class="bg-white p-2 rounded-2xl border border-slate-200 shadow-xs overflow-x-auto">
        <div class="flex items-center gap-1.5 min-w-max" id="approvalTabContainer">
            <!-- All (Hanya Tampil untuk Administrator) -->
            @if(Auth::user() && (Auth::user()->isAdmin() || Auth::user()->role === 'admin'))
            <a href="{{ route('paklaring.index', array_merge(request()->query(), ['tab' => 'all'])) }}"
               @if($tab === 'all') id="activeApprovalTab" @endif
               class="px-3.5 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 {{ $tab === 'all' ? 'bg-primary text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                <span>Semua</span>
                <span class="px-1.5 py-0.5 rounded-md text-[10px] {{ $tab === 'all' ? 'bg-white/20 text-white' : 'bg-slate-200 text-slate-700' }}">{{ $counts['all'] }}</span>
            </a>
            @endif

            <!-- Area -->
            <a href="{{ route('paklaring.index', array_merge(request()->query(), ['tab' => 'area'])) }}"
               @if($tab === 'area') id="activeApprovalTab" @endif
               class="px-3.5 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 {{ $tab === 'area' ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                <i class="fa-solid fa-location-dot text-xs"></i>
                <span>Review Area (AS)</span>
                <span class="px-1.5 py-0.5 rounded-md text-[10px] {{ $tab === 'area' ? 'bg-white/20 text-white' : 'bg-indigo-100 text-indigo-800' }}">{{ $counts['area'] }}</span>
            </a>

            <!-- HRD -->
            <a href="{{ route('paklaring.index', array_merge(request()->query(), ['tab' => 'hrd'])) }}"
               @if($tab === 'hrd') id="activeApprovalTab" @endif
               class="px-3.5 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 {{ $tab === 'hrd' ? 'bg-purple-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                <i class="fa-solid fa-user-tie text-xs"></i>
                <span>Review HRD</span>
                <span class="px-1.5 py-0.5 rounded-md text-[10px] {{ $tab === 'hrd' ? 'bg-white/20 text-white' : 'bg-purple-100 text-purple-800' }}">{{ $counts['hrd'] }}</span>
            </a>

            <!-- DB -->
            <a href="{{ route('paklaring.index', array_merge(request()->query(), ['tab' => 'db'])) }}"
               @if($tab === 'db') id="activeApprovalTab" @endif
               class="px-3.5 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 {{ $tab === 'db' ? 'bg-cyan-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                <i class="fa-solid fa-database text-xs"></i>
                <span>Review DB</span>
                <span class="px-1.5 py-0.5 rounded-md text-[10px] {{ $tab === 'db' ? 'bg-white/20 text-white' : 'bg-cyan-100 text-cyan-800' }}">{{ $counts['db'] }}</span>
            </a>

            <!-- BPJS -->
            <a href="{{ route('paklaring.index', array_merge(request()->query(), ['tab' => 'bpjs'])) }}"
               @if($tab === 'bpjs') id="activeApprovalTab" @endif
               class="px-3.5 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 {{ $tab === 'bpjs' ? 'bg-emerald-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                <i class="fa-solid fa-shield-halved text-xs"></i>
                <span>Review BPJS</span>
                <span class="px-1.5 py-0.5 rounded-md text-[10px] {{ $tab === 'bpjs' ? 'bg-white/20 text-white' : 'bg-emerald-100 text-emerald-800' }}">{{ $counts['bpjs'] }}</span>
            </a>

            <!-- Selesai -->
            <a href="{{ route('paklaring.index', array_merge(request()->query(), ['tab' => 'selesai'])) }}"
               @if($tab === 'selesai') id="activeApprovalTab" @endif
               class="px-3.5 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 {{ $tab === 'selesai' ? 'bg-emerald-700 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                <i class="fa-solid fa-circle-check text-xs"></i>
                <span>Selesai / Terbit</span>
                <span class="px-1.5 py-0.5 rounded-md text-[10px] {{ $tab === 'selesai' ? 'bg-white/20 text-white' : 'bg-emerald-100 text-emerald-800' }}">{{ $counts['selesai'] }}</span>
            </a>

            <!-- Ditolak / Hold -->
            <a href="{{ route('paklaring.index', array_merge(request()->query(), ['tab' => 'tolak_hold'])) }}"
               @if($tab === 'tolak_hold') id="activeApprovalTab" @endif
               class="px-3.5 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 {{ $tab === 'tolak_hold' ? 'bg-rose-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                <i class="fa-solid fa-circle-xmark text-xs"></i>
                <span>Ditolak / Hold</span>
                <span class="px-1.5 py-0.5 rounded-md text-[10px] {{ $tab === 'tolak_hold' ? 'bg-white/20 text-white' : 'bg-rose-100 text-rose-800' }}">{{ $counts['tolak_hold'] }}</span>
            </a>
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs">
        <form action="{{ route('paklaring.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            <input type="hidden" name="tab" value="{{ $tab }}">

            <!-- Search Field -->
            <div class="relative lg:col-span-2">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                </span>
                <input type="text" 
                       name="search" 
                       value="{{ $search }}"
                       placeholder="Cari Nama, NIK, Kode Validasi, atau No. Surat..." 
                       class="w-full pl-9 pr-3.5 py-2 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 focus:ring-2 focus:ring-primary/20 focus:border-primary">
            </div>

            <!-- Filter Area -->
            <div>
                <select name="area" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 focus:ring-2 focus:ring-primary/20 focus:border-primary">
                    <option value="">-- Semua Area --</option>
                    @foreach($areas as $ar)
                        <option value="{{ $ar }}" {{ $areaFilter === $ar ? 'selected' : '' }}>{{ $ar }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Prinsiple & Submit -->
            <div class="flex items-center gap-2">
                <select name="prinsiple" class="flex-1 px-3 py-2 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 focus:ring-2 focus:ring-primary/20 focus:border-primary">
                    <option value="">-- Semua Prinsiple --</option>
                    @foreach($principles as $p)
                        <option value="{{ $p }}" {{ $prinsipleFilter === $p ? 'selected' : '' }}>{{ $p }}</option>
                    @endforeach
                </select>
                <button type="submit" class="px-4 py-2 rounded-xl bg-primary hover:bg-primary-700 text-white text-xs font-bold transition-all shadow-xs cursor-pointer shrink-0">
                    Filter
                </button>
                @if(!empty($search) || !empty($areaFilter) || !empty($prinsipleFilter))
                <a href="{{ route('paklaring.index', ['tab' => $tab]) }}" class="px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold transition-all" title="Reset Filter">
                    <i class="fa-solid fa-rotate-left"></i>
                </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Data Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50/80 text-slate-700 text-[11px] font-black uppercase tracking-wider border-b border-slate-200">
                    <tr>
                        <th class="py-3.5 px-4 w-12 text-center">No</th>
                        <th class="py-3.5 px-4">Permohonan</th>
                        <th class="py-3.5 px-4">Karyawan &amp; NIK</th>
                        <th class="py-3.5 px-4">Prinsiple &amp; Entitas</th>
                        <th class="py-3.5 px-4">Area &amp; Jabatan</th>
                        <th class="py-3.5 px-4">Durasi Proses</th>
                        <th class="py-3.5 px-4">Tahapan Approval</th>
                        <th class="py-3.5 px-4 text-center">Status</th>
                        <th class="py-3.5 px-4 text-center w-24">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($paklarings as $index => $row)
                    <tr class="hover:bg-slate-50/70 transition-colors">
                        <td class="py-3.5 px-4 text-center font-bold text-slate-400">
                            {{ $paklarings->firstItem() + $index }}
                        </td>
                        <td class="py-3.5 px-4">
                            <div class="font-mono font-bold text-primary">{{ $row->kode_validasi }}</div>
                            <div class="text-[10px] text-slate-400">{{ $row->created_at?->format('d M Y') }}</div>
                        </td>
                        <td class="py-3.5 px-4">
                            <div class="font-bold text-slate-900">{{ $row->nama_lengkap }}</div>
                            <div class="text-[10px] font-mono text-slate-400">{{ $row->nik }}</div>
                        </td>
                        <td class="py-3.5 px-4">
                            <div class="font-semibold text-slate-800">{{ $row->prinsiple }}</div>
                            <div class="text-[10px] text-slate-400 font-bold">Kantor: {{ $row->kantor ?: '-' }}</div>
                        </td>
                        <td class="py-3.5 px-4">
                            <div class="font-semibold text-slate-800">{{ $row->area }}</div>
                            <div class="text-[10px] text-slate-400">{{ $row->jabatan }}</div>
                        </td>
                        <td class="py-3.5 px-4">
                            <div class="text-xs font-semibold text-slate-700 flex items-center gap-1">
                                <i class="fa-regular fa-clock text-[10px] text-slate-400"></i>
                                <span>{{ $row->duration_string }}</span>
                            </div>
                        </td>
                        <td class="py-3.5 px-4">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[11px] font-bold border {{ $row->bagian_badge['bg'] }}">
                                <i class="fa-solid {{ $row->bagian_badge['icon'] }} text-[10px]"></i>
                                <span>{{ $row->bagian_badge['label'] }}</span>
                            </span>
                            @if(!empty($row->pengguna))
                                <div class="text-[10px] text-slate-400 mt-0.5">PIC: {{ $row->pengguna }}</div>
                            @endif
                        </td>
                        <td class="py-3.5 px-4 text-center">
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold border {{ $row->status_badge['bg'] }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $row->status_badge['dot'] }}"></span>
                                <span>{{ $row->status_badge['label'] }}</span>
                            </span>
                        </td>
                        <td class="py-3.5 px-4 text-center">
                            <a href="{{ route('paklaring.show', $row->id) }}" 
                               class="inline-flex items-center justify-center gap-1 px-3 py-1.5 rounded-xl bg-primary-50 hover:bg-primary-100 text-primary font-bold text-xs border border-primary-200 transition-all shadow-xs">
                                <i class="fa-solid fa-arrow-right text-[10px]"></i>
                                <span>Detail</span>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="py-12 text-center text-slate-400">
                            <div class="w-12 h-12 mx-auto rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center text-xl mb-3">
                                <i class="fa-solid fa-file-contract"></i>
                            </div>
                            <div class="text-xs font-bold text-slate-700">Belum Ada Data Pengajuan Veklaring</div>
                            <p class="text-[11px] text-slate-400 mt-1 max-w-sm mx-auto">
                                Tidak ada data yang cocok dengan kriteria filter tab saat ini.
                            </p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($paklarings->hasPages())
        <div class="p-4 border-t border-slate-100 bg-slate-50/50">
            {{ $paklarings->links() }}
        </div>
        @endif
    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const activeTab = document.getElementById('activeApprovalTab');
    if (activeTab) {
        // Otomatis arahkan fokus scroll tab bar ke tab aktif approver
        activeTab.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
    }
});
</script>
@endsection
