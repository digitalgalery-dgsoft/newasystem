@extends('layouts.app')

@section('title', 'Audit Trail & Log Aktivitas Sistem - ASystem')

@section('content')
<div class="space-y-6 pb-12" x-data="activityLogsApp()">

    <!-- PAGE HEADER CARD -->
    <div class="page-header-card flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
        <div class="flex items-center gap-3.5">
            <div class="w-12 h-12 rounded-2xl bg-indigo-50 border border-indigo-100 flex items-center justify-center text-indigo-600 text-2xl font-black shadow-inner">
                <i class="fa-solid fa-clock-rotate-left"></i>
            </div>
            <div>
                <div class="flex items-center gap-2 flex-wrap">
                    <h1 class="text-xl md:text-2xl font-extrabold text-slate-900 tracking-tight">Audit Trail & Log Aktivitas</h1>
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-indigo-50 text-indigo-700 border border-indigo-200">
                        <i class="fa-solid fa-shield-halved text-[9px]"></i> System Audit
                    </span>
                </div>
                <p class="text-xs md:text-sm text-slate-500 font-medium mt-0.5">
                    Pencatatan real-time seluruh interaksi pengguna, sesi autentikasi, perubahan master data, rekrutmen, hingga ekspor laporan.
                </p>
            </div>
        </div>

        <!-- QUICK ACTIONS -->
        <div class="flex items-center gap-2 flex-wrap sm:flex-nowrap">
            <a href="{{ route('activity-logs.export', request()->all()) }}" 
               class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 transition-all shadow-sm shadow-emerald-600/20"
               title="Download Rekap Log Format Microsoft Excel (.xlsx)">
                <i class="fa-solid fa-file-excel"></i>
                <span>Export Excel (.xlsx)</span>
            </a>
        </div>
    </div>

    <!-- 4 KARTU METRIK RINGKASAN -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- 1. Total Aktivitas -->
        <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-sm flex items-center gap-4 relative overflow-hidden group hover:border-indigo-300 transition-all">
            <div class="w-12 h-12 rounded-xl bg-indigo-50 border border-indigo-100 flex items-center justify-center text-indigo-600 text-xl font-bold flex-shrink-0 group-hover:scale-105 transition-transform">
                <i class="fa-solid fa-database"></i>
            </div>
            <div class="min-w-0 flex-1">
                <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total Seluruh Log</div>
                <div class="text-2xl font-black text-slate-900 leading-tight mt-0.5">{{ number_format($metrics['total']) }}</div>
                <div class="text-[11px] font-semibold text-indigo-600 flex items-center gap-1 mt-0.5">
                    <i class="fa-solid fa-list-check text-[9px]"></i> Seluruh rekam jejak
                </div>
            </div>
        </div>

        <!-- 2. Aktivitas Hari Ini -->
        <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-sm flex items-center gap-4 relative overflow-hidden group hover:border-emerald-300 transition-all">
            <div class="w-12 h-12 rounded-xl bg-emerald-50 border border-emerald-100 flex items-center justify-center text-emerald-600 text-xl font-bold flex-shrink-0 group-hover:scale-105 transition-transform">
                <i class="fa-solid fa-calendar-day"></i>
            </div>
            <div class="min-w-0 flex-1">
                <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Aktivitas Hari Ini</div>
                <div class="text-2xl font-black text-emerald-700 leading-tight mt-0.5">{{ number_format($metrics['today']) }}</div>
                <div class="text-[11px] font-semibold text-emerald-600 flex items-center gap-1 mt-0.5">
                    <i class="fa-solid fa-circle text-[6px]"></i> Log real-time hari ini
                </div>
            </div>
        </div>

        <!-- 3. Sesi Login -->
        <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-sm flex items-center gap-4 relative overflow-hidden group hover:border-cyan-300 transition-all">
            <div class="w-12 h-12 rounded-xl bg-cyan-50 border border-cyan-100 flex items-center justify-center text-cyan-600 text-xl font-bold flex-shrink-0 group-hover:scale-105 transition-transform">
                <i class="fa-solid fa-right-to-bracket"></i>
            </div>
            <div class="min-w-0 flex-1">
                <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total Sesi Login</div>
                <div class="text-2xl font-black text-cyan-700 leading-tight mt-0.5">{{ number_format($metrics['logins']) }}</div>
                <div class="text-[11px] font-semibold text-cyan-600 flex items-center gap-1 mt-0.5">
                    <i class="fa-solid fa-user-check text-[9px]"></i> Akses masuk user
                </div>
            </div>
        </div>

        <!-- 4. Perubahan Data -->
        <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-sm flex items-center gap-4 relative overflow-hidden group hover:border-amber-300 transition-all">
            <div class="w-12 h-12 rounded-xl bg-amber-50 border border-amber-100 flex items-center justify-center text-amber-600 text-xl font-bold flex-shrink-0 group-hover:scale-105 transition-transform">
                <i class="fa-solid fa-pen-to-square"></i>
            </div>
            <div class="min-w-0 flex-1">
                <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Perubahan Data (CRUD)</div>
                <div class="text-2xl font-black text-amber-700 leading-tight mt-0.5">{{ number_format($metrics['changes']) }}</div>
                <div class="text-[11px] font-semibold text-amber-600 flex items-center gap-1 mt-0.5">
                    <i class="fa-solid fa-arrows-rotate text-[9px]"></i> Create, Update, Delete
                </div>
            </div>
        </div>
    </div>

    <!-- FILTER BAR -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-5">
        <form method="GET" action="{{ route('activity-logs.index') }}" class="space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3 flex-wrap gap-2">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-sliders text-indigo-600"></i>
                    <span class="text-sm font-bold text-slate-800">Filter Data Log Aktivitas</span>
                </div>
                @if(request()->hasAny(['f_module', 'f_action', 'f_user', 'f_search', 'f_date_start', 'f_date_end']))
                    <span class="text-xs bg-amber-50 text-amber-700 font-bold px-2 py-0.5 rounded-md border border-amber-200">
                        Filter Aktif Diterapkan
                    </span>
                @endif
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3">
                <!-- 1. Rentang Tanggal Mulai -->
                <div>
                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Dari Tanggal</label>
                    <input type="date" name="f_date_start" value="{{ $fDateStart }}" 
                           class="w-full text-xs font-semibold text-slate-700 bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 focus:bg-white focus:border-indigo-600 focus:ring-1 focus:ring-indigo-600 transition-all">
                </div>

                <!-- 2. Rentang Tanggal Sampai -->
                <div>
                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Sampai Tanggal</label>
                    <input type="date" name="f_date_end" value="{{ $fDateEnd }}" 
                           class="w-full text-xs font-semibold text-slate-700 bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 focus:bg-white focus:border-indigo-600 focus:ring-1 focus:ring-indigo-600 transition-all">
                </div>

                <!-- 3. Filter Modul -->
                <div>
                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Modul</label>
                    <select name="f_module" class="w-full text-xs font-semibold text-slate-700 bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 focus:bg-white focus:border-indigo-600 focus:ring-1 focus:ring-indigo-600 transition-all">
                        <option value="">Semua Modul</option>
                        @foreach($listModules as $mod)
                            <option value="{{ $mod }}" {{ $fModule === $mod ? 'selected' : '' }}>{{ $mod }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- 4. Filter Aksi -->
                <div>
                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Jenis Aksi</label>
                    <select name="f_action" class="w-full text-xs font-semibold text-slate-700 bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 focus:bg-white focus:border-indigo-600 focus:ring-1 focus:ring-indigo-600 transition-all">
                        <option value="">Semua Aksi</option>
                        @foreach($listActions as $act)
                            <option value="{{ $act }}" {{ $fAction === $act ? 'selected' : '' }}>{{ $act }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- 5. Filter User -->
                <div>
                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Pengguna</label>
                    <select name="f_user" class="w-full text-xs font-semibold text-slate-700 bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 focus:bg-white focus:border-indigo-600 focus:ring-1 focus:ring-indigo-600 transition-all">
                        <option value="">Semua Pengguna</option>
                        @foreach($listUsers as $u)
                            <option value="{{ $u->id }}" {{ $fUser == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- 6. Pencarian Deskripsi / IP -->
                <div>
                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Cari Kata Kunci</label>
                    <div class="relative">
                        <input type="text" name="f_search" value="{{ $fSearch }}" placeholder="Deskripsi, IP, user..." 
                               class="w-full text-xs font-semibold text-slate-700 bg-slate-50 border border-slate-300 rounded-xl pl-8 pr-3 py-2 focus:bg-white focus:border-indigo-600 focus:ring-1 focus:ring-indigo-600 transition-all">
                        <i class="fa-solid fa-search text-xs text-slate-400 absolute left-2.5 top-2.5"></i>
                    </div>
                </div>
            </div>

            <!-- BUTTON ACTIONS -->
            <div class="flex items-center justify-between pt-2 border-t border-slate-100 flex-wrap gap-2">
                <div class="flex items-center gap-2">
                    <span class="text-xs text-slate-500 font-medium">Tampilkan per halaman:</span>
                    <select name="per_page" onchange="this.form.submit()" class="text-xs bg-slate-50 border border-slate-300 rounded-lg px-2 py-1 focus:outline-none">
                        <option value="15" {{ $perPage == 15 ? 'selected' : '' }}>15 Baris</option>
                        <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25 Baris</option>
                        <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50 Baris</option>
                        <option value="100" {{ $perPage == 100 ? 'selected' : '' }}>100 Baris</option>
                    </select>
                </div>

                <div class="flex items-center gap-2">
                    <a href="{{ route('activity-logs.index') }}" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 transition-all flex items-center gap-1.5">
                        <i class="fa-solid fa-rotate-left text-slate-400"></i>
                        <span>Reset Filter</span>
                    </a>
                    <button type="submit" class="px-5 py-2 rounded-xl text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 transition-all shadow-sm shadow-indigo-600/20 flex items-center gap-1.5">
                        <i class="fa-solid fa-filter"></i>
                        <span>Terapkan Filter</span>
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- TABEL LOG AKTIVITAS -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead class="bg-slate-100 text-slate-600 font-bold border-b border-slate-200">
                    <tr>
                        <th class="py-3 px-3.5 w-12 text-center">No</th>
                        <th class="py-3 px-3.5 w-40">Waktu</th>
                        <th class="py-3 px-3.5 w-56">Pengguna</th>
                        <th class="py-3 px-3.5 w-28 text-center">Aksi</th>
                        <th class="py-3 px-3.5 w-36">Modul</th>
                        <th class="py-3 px-3.5">Deskripsi Aktivitas</th>
                        <th class="py-3 px-3.5 w-44">IP & Device</th>
                        <th class="py-3 px-3.5 w-20 text-center">Detail</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                    @forelse($logs as $idx => $log)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <!-- No -->
                            <td class="py-3 px-3.5 text-center text-slate-400 font-mono">
                                {{ $logs->firstItem() + $idx }}
                            </td>

                            <!-- Waktu -->
                            <td class="py-3 px-3.5 whitespace-nowrap">
                                <div class="font-bold text-slate-800">{{ $log->created_at->translatedFormat('d M Y') }}</div>
                                <div class="text-[11px] text-slate-500 font-mono">{{ $log->created_at->format('H:i:s') }} WIB</div>
                                <div class="text-[10px] text-slate-400 italic mt-0.5">{{ $log->diff_time }}</div>
                            </td>

                            <!-- Pengguna -->
                            <td class="py-3 px-3.5">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-full bg-slate-100 border border-slate-200 flex items-center justify-center font-bold text-xs text-indigo-700 flex-shrink-0 shadow-inner">
                                        {{ strtoupper(substr($log->user_name, 0, 2)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <div class="font-bold text-slate-900 truncate" title="{{ $log->user_name }}">{{ $log->user_name }}</div>
                                        <div class="text-[10px] text-indigo-600 font-semibold truncate">{{ $log->user_jabatan ?? 'User' }}</div>
                                        @if($log->user_email)
                                            <div class="text-[10px] text-slate-400 font-mono truncate" title="{{ $log->user_email }}">{{ $log->user_email }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <!-- Aksi -->
                            <td class="py-3 px-3.5 text-center whitespace-nowrap">
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-extrabold border {{ $log->action_badge_class }}">
                                    <i class="{{ $log->action_icon }} text-[9px]"></i>
                                    <span>{{ $log->action }}</span>
                                </span>
                            </td>

                            <!-- Modul -->
                            <td class="py-3 px-3.5 whitespace-nowrap">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-xs font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                    {{ $log->module }}
                                </span>
                            </td>

                            <!-- Deskripsi -->
                            <td class="py-3 px-3.5">
                                <div class="text-xs text-slate-800 leading-relaxed font-semibold">
                                    {{ $log->description }}
                                </div>
                                @if($log->url)
                                    <div class="text-[10px] text-slate-400 font-mono truncate mt-0.5 max-w-md" title="{{ $log->url }}">
                                        <span class="text-slate-500 font-bold">{{ $log->method }}:</span> {{ $log->url }}
                                    </div>
                                @endif
                            </td>

                            <!-- IP & Device -->
                            <td class="py-3 px-3.5 whitespace-nowrap">
                                <div class="font-mono text-xs text-slate-700 font-bold flex items-center gap-1">
                                    <i class="fa-solid fa-network-wired text-[10px] text-slate-400"></i>
                                    <span>{{ $log->ip_address ?? '-' }}</span>
                                </div>
                                <div class="text-[10px] text-slate-400 flex items-center gap-1 mt-0.5" title="{{ $log->user_agent }}">
                                    <i class="fa-solid fa-laptop text-[9px]"></i>
                                    <span class="truncate max-w-[130px]">{{ $log->device }}</span>
                                </div>
                            </td>

                            <!-- Detail (Modal) -->
                            <td class="py-3 px-3.5 text-center">
                                @if(!empty($log->properties))
                                    <button @click="showDetail({{ $log->id }})" 
                                            class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-indigo-50 text-indigo-700 hover:bg-indigo-100 border border-indigo-200 transition-all"
                                            title="Lihat Detail Diff & Payload Perubahan">
                                        <i class="fa-solid fa-eye text-xs"></i>
                                    </button>
                                @else
                                    <span class="text-slate-300 text-xs">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-12 text-slate-400">
                                <div class="w-12 h-12 rounded-2xl bg-slate-50 text-slate-300 flex items-center justify-center text-2xl mx-auto mb-2">
                                    <i class="fa-solid fa-clock-rotate-left"></i>
                                </div>
                                <div class="font-bold text-sm text-slate-600">Belum ada riwayat aktivitas</div>
                                <div class="text-xs text-slate-400 mt-0.5">Tidak ada aktivitas yang sesuai dengan kriteria filter yang Anda pilih.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- FOOTER & PAGINASI -->
        <div class="p-4 bg-slate-50 border-t border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs text-slate-500 font-medium">
            <div>
                Menampilkan <strong>{{ $logs->firstItem() ?? 0 }}</strong> - <strong>{{ $logs->lastItem() ?? 0 }}</strong> dari total <strong>{{ number_format($logs->total()) }}</strong> aktivitas
            </div>
            <div>
                {{ $logs->links() }}
            </div>
        </div>
    </div>

    <!-- MODAL DETAIL PRATINJAU DIFF & PROPERTIES -->
    <div x-show="modalOpen" 
         x-cloak 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-sm transition-all"
         x-transition:enter="ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @keydown.escape.window="modalOpen = false">
        
        <div class="bg-white rounded-2xl border border-slate-200 shadow-2xl w-full max-w-2xl overflow-hidden max-h-[90vh] flex flex-col"
             @click.away="modalOpen = false">
            
            <!-- Modal Header -->
            <div class="px-5 py-4 border-b border-slate-200 flex items-center justify-between bg-slate-50">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-700 flex items-center justify-center font-bold text-sm border border-indigo-200">
                        <i class="fa-solid fa-code-compare"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">Rincian Data & Payload Aktivitas</h3>
                        <p class="text-[11px] text-slate-500" x-text="'Log ID #' + activeLog?.id + ' — ' + activeLog?.created_at"></p>
                    </div>
                </div>
                <button @click="modalOpen = false" class="text-slate-400 hover:text-slate-700 transition-colors text-base p-1">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="p-5 overflow-y-auto space-y-4 flex-1 text-xs">
                <!-- Deskripsi & Info -->
                <div class="p-3 bg-slate-50 rounded-xl border border-slate-200/80 space-y-1">
                    <div class="font-bold text-slate-800 text-xs" x-text="activeLog?.description"></div>
                    <div class="text-[11px] text-slate-500 flex items-center gap-2 flex-wrap pt-1">
                        <span><strong class="text-slate-700">Pelaku:</strong> <span x-text="activeLog?.user_name"></span> (<span x-text="activeLog?.user_jabatan"></span>)</span>
                        <span>•</span>
                        <span><strong class="text-slate-700">Modul:</strong> <span x-text="activeLog?.module"></span></span>
                        <span>•</span>
                        <span><strong class="text-slate-700">IP:</strong> <span x-text="activeLog?.ip_address"></span></span>
                    </div>
                </div>

                <!-- Snapshot Data Lama vs Baru jika ada -->
                <template x-if="activeLog?.properties?.old || activeLog?.properties?.new">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <!-- Old Values -->
                        <div>
                            <div class="text-[11px] font-bold text-rose-700 uppercase tracking-wider mb-1 flex items-center gap-1">
                                <i class="fa-solid fa-minus-circle"></i>
                                <span>Nilai Sebelum (Old Data)</span>
                            </div>
                            <pre class="p-3 rounded-xl bg-slate-900 text-rose-300 font-mono text-[11px] overflow-x-auto max-h-60" x-text="JSON.stringify(activeLog?.properties?.old, null, 2)"></pre>
                        </div>
                        <!-- New Values -->
                        <div>
                            <div class="text-[11px] font-bold text-emerald-700 uppercase tracking-wider mb-1 flex items-center gap-1">
                                <i class="fa-solid fa-plus-circle"></i>
                                <span>Nilai Sesudah (New Data)</span>
                            </div>
                            <pre class="p-3 rounded-xl bg-slate-900 text-emerald-300 font-mono text-[11px] overflow-x-auto max-h-60" x-text="JSON.stringify(activeLog?.properties?.new, null, 2)"></pre>
                        </div>
                    </div>
                </template>

                <!-- Full Raw JSON Payload -->
                <div>
                    <div class="text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1 flex items-center gap-1">
                        <i class="fa-solid fa-code"></i>
                        <span>Raw Payload JSON</span>
                    </div>
                    <pre class="p-3 rounded-xl bg-slate-900 text-indigo-200 font-mono text-[11px] overflow-x-auto max-h-72" x-text="JSON.stringify(activeLog?.properties, null, 2)"></pre>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="px-5 py-3 border-t border-slate-200 flex items-center justify-end bg-slate-50">
                <button @click="modalOpen = false" class="px-4 py-1.5 rounded-xl text-xs font-bold text-slate-700 bg-white border border-slate-200 hover:bg-slate-100 transition-all">
                    Tutup
                </button>
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script>
function activityLogsApp() {
    return {
        modalOpen: false,
        activeLog: null,
        loading: false,

        showDetail(id) {
            this.loading = true;
            fetch('{{ url("activity-logs") }}/' + id)
                .then(res => res.json())
                .then(data => {
                    this.activeLog = data;
                    this.modalOpen = true;
                })
                .catch(err => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal Memuat Detail',
                        text: 'Terjadi kendala saat mengambil rincian data log.',
                    });
                })
                .finally(() => {
                    this.loading = false;
                });
        }
    };
}
</script>
@endpush
@endsection
