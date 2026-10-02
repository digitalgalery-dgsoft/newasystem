@extends('layouts.app')

@section('title', 'Detail Pengajuan Veklaring #' . $paklaring->kode_validasi . ' - ESA Groups')

@section('content')
<div class="space-y-6" x-data="paklaringDetail()">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-2xl border border-slate-200 shadow-xs">
        <div class="space-y-1">
            <div class="flex items-center gap-2 flex-wrap">
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-primary/10 text-primary border border-primary/20">
                    Modul Veklaring
                </span>
                <span class="text-xs font-mono font-bold text-slate-700 bg-slate-100 px-2 py-0.5 rounded-md border border-slate-200">
                    ID: {{ $paklaring->kode_validasi }}
                </span>
                <span class="text-xs text-slate-400 font-medium">&bull; Diajukan {{ $paklaring->created_at?->format('d M Y, H:i') }} WIB</span>
            </div>
            <h1 class="text-xl font-black text-slate-800 tracking-tight flex items-center gap-2">
                <i class="fa-solid fa-file-contract text-primary text-lg"></i>
                <span>{{ $paklaring->nama_lengkap }}</span>
            </h1>
            <p class="text-xs text-slate-500">
                NIK: <strong class="text-slate-700 font-mono">{{ $paklaring->nik }}</strong> &bull; {{ $paklaring->jabatan }} &bull; {{ $paklaring->prinsiple }} &bull; {{ $paklaring->area }}
            </p>
        </div>

        <div class="flex items-center gap-2 shrink-0 flex-wrap">
            @if($paklaring->status === 'Selesai')
            <a href="{{ route('paklaring.print-pdf', $paklaring->id) }}" target="_blank" class="px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition-all shadow-md shadow-emerald-600/20 flex items-center gap-2">
                <i class="fa-solid fa-print text-xs"></i>
                <span>Cetak Surat Referensi (PDF)</span>
            </a>
            @endif

            <a href="{{ route('paklaring.index') }}" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition-all flex items-center gap-2">
                <i class="fa-solid fa-arrow-left text-xs"></i>
                <span>Daftar Pengajuan</span>
            </a>
        </div>
    </div>

    <!-- Alert Notifikasi -->
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

    @if(session('warning'))
    <div class="p-4 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 text-xs font-medium flex items-center justify-between shadow-xs">
        <div class="flex items-center gap-2">
            <i class="fa-solid fa-triangle-exclamation text-amber-600 text-base"></i>
            <span>{{ session('warning') }}</span>
        </div>
        <button type="button" onclick="this.parentElement.remove()" class="text-amber-500 hover:text-amber-700">
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

    <!-- Workflow Progression Stepper -->
    @php
        $currOrder = $paklaring->current_step_order ?: match ($paklaring->status_bagian) {
            'Area' => 1,
            'HRD' => 2,
            'DB' => 3,
            'BPJS' => 4,
            'Selesai' => 5,
            default => 1,
        };
        if ($paklaring->status === 'Selesai') $currOrder = 5;
    @endphp

    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-xs space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-3 border-b border-slate-100">
            <div class="text-xs font-black text-slate-800 flex items-center gap-2">
                <i class="fa-solid fa-timeline text-primary"></i>
                <span>Alur Verifikasi Bertingkat Veklaring</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-[11px] text-slate-400">Status Permohonan:</span>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold border {{ $paklaring->status_badge['bg'] }} flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full {{ $paklaring->status_badge['dot'] }}"></span>
                    <span>{{ $paklaring->status_badge['label'] }}</span>
                </span>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-4 gap-3">
            <!-- Step 1: Area -->
            <div class="p-3.5 rounded-xl border text-left space-y-1 {{ $currOrder > 1 ? 'bg-emerald-50/70 border-emerald-200' : ($currOrder === 1 && $paklaring->status !== 'Tolak' ? 'bg-blue-50 border-blue-300 ring-2 ring-blue-500/20' : 'bg-slate-50 border-slate-200') }}">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-black uppercase tracking-wider {{ $currOrder > 1 ? 'text-emerald-800' : ($currOrder === 1 ? 'text-primary' : 'text-slate-500') }}">
                        1. Review Area
                    </span>
                    @if($currOrder > 1)
                        <i class="fa-solid fa-circle-check text-emerald-600 text-sm"></i>
                    @elseif($currOrder === 1 && $paklaring->status !== 'Tolak')
                        <i class="fa-solid fa-spinner fa-spin text-primary text-sm"></i>
                    @else
                        <i class="fa-regular fa-circle text-slate-300 text-sm"></i>
                    @endif
                </div>
                <div class="text-xs font-bold text-slate-800">Tim AS / Admin Ops</div>
                <div class="text-[10px] text-slate-500 line-clamp-1">
                    {{ $stepsInfo[1]['label'] ?? 'Staf Operasional Area' }}
                </div>
            </div>

            <!-- Step 2: HRD -->
            <div class="p-3.5 rounded-xl border text-left space-y-1 {{ $currOrder > 2 ? 'bg-emerald-50/70 border-emerald-200' : ($currOrder === 2 && $paklaring->status !== 'Tolak' ? 'bg-blue-50 border-blue-300 ring-2 ring-blue-500/20' : 'bg-slate-50 border-slate-200') }}">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-black uppercase tracking-wider {{ $currOrder > 2 ? 'text-emerald-800' : ($currOrder === 2 ? 'text-primary' : 'text-slate-500') }}">
                        2. Review HRD
                    </span>
                    @if($currOrder > 2)
                        <i class="fa-solid fa-circle-check text-emerald-600 text-sm"></i>
                    @elseif($currOrder === 2 && $paklaring->status !== 'Tolak')
                        <i class="fa-solid fa-spinner fa-spin text-primary text-sm"></i>
                    @else
                        <i class="fa-regular fa-circle text-slate-300 text-sm"></i>
                    @endif
                </div>
                <div class="text-xs font-bold text-slate-800">Tim HRD Management</div>
                <div class="text-[10px] text-slate-500 line-clamp-1">
                    {{ $stepsInfo[2]['label'] ?? 'Verifikasi Keabsahan Kerja' }}
                </div>
            </div>

            <!-- Step 3: DB -->
            <div class="p-3.5 rounded-xl border text-left space-y-1 {{ $currOrder > 3 ? 'bg-emerald-50/70 border-emerald-200' : ($currOrder === 3 && $paklaring->status !== 'Tolak' ? 'bg-blue-50 border-blue-300 ring-2 ring-blue-500/20' : 'bg-slate-50 border-slate-200') }}">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-black uppercase tracking-wider {{ $currOrder > 3 ? 'text-emerald-800' : ($currOrder === 3 ? 'text-primary' : 'text-slate-500') }}">
                        3. Review DB
                    </span>
                    @if($currOrder > 3)
                        <i class="fa-solid fa-circle-check text-emerald-600 text-sm"></i>
                    @elseif($currOrder === 3 && $paklaring->status !== 'Tolak')
                        <i class="fa-solid fa-spinner fa-spin text-primary text-sm"></i>
                    @else
                        <i class="fa-regular fa-circle text-slate-300 text-sm"></i>
                    @endif
                </div>
                <div class="text-xs font-bold text-slate-800">Database Pusat</div>
                <div class="text-[10px] text-slate-500 line-clamp-1">
                    {{ $stepsInfo[3]['label'] ?? 'Pencocokan Data Database' }}
                </div>
            </div>

            <!-- Step 4: BPJS -->
            <div class="p-3.5 rounded-xl border text-left space-y-1 {{ $currOrder >= 5 ? 'bg-emerald-50/70 border-emerald-200' : ($currOrder === 4 && $paklaring->status !== 'Tolak' ? 'bg-blue-50 border-blue-300 ring-2 ring-blue-500/20' : 'bg-slate-50 border-slate-200') }}">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-black uppercase tracking-wider {{ $currOrder >= 5 ? 'text-emerald-800' : ($currOrder === 4 ? 'text-primary' : 'text-slate-500') }}">
                        4. Review BPJS
                    </span>
                    @if($currOrder >= 5)
                        <i class="fa-solid fa-circle-check text-emerald-600 text-sm"></i>
                    @elseif($currOrder === 4 && $paklaring->status !== 'Tolak')
                        <i class="fa-solid fa-spinner fa-spin text-primary text-sm"></i>
                    @else
                        <i class="fa-regular fa-circle text-slate-300 text-sm"></i>
                    @endif
                </div>
                <div class="text-xs font-bold text-slate-800">Penomoran Resmi</div>
                <div class="text-[10px] text-slate-500 line-clamp-1">
                    {{ $stepsInfo[4]['label'] ?? 'Penerbitan Surat Selesai' }}
                </div>
            </div>
        </div>
    </div>

    <!-- Banner Resmi Jika Selesai Terbit -->
    @if($paklaring->status === 'Selesai' && !empty($paklaring->nomor_ref))
    <div class="bg-gradient-to-r from-emerald-600 via-teal-600 to-emerald-700 rounded-2xl p-6 text-white shadow-lg shadow-emerald-500/10 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="space-y-1">
            <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-white/20 text-xs font-bold">
                <i class="fa-solid fa-certificate text-emerald-200"></i>
                <span>Surat Referensi Kerja Resmi Telah Diterbitkan</span>
            </div>
            <div class="text-xl sm:text-2xl font-black font-mono tracking-wider">{{ $paklaring->nomor_ref }}</div>
            <p class="text-xs text-emerald-100">
                Seluruh verifikasi Area, HRD, Tim DB, dan Tim BPJS telah selesai diproses.
            </p>
        </div>
        <div class="shrink-0">
            <a href="{{ route('paklaring.print-pdf', $paklaring->id) }}" target="_blank" class="px-5 py-3 rounded-xl bg-white text-emerald-800 hover:bg-emerald-50 text-xs font-black shadow-md transition-all flex items-center gap-2">
                <i class="fa-solid fa-print"></i>
                <span>Cetak / Simpan PDF</span>
            </a>
        </div>
    </div>
    @endif

    <!-- Content Columns -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Left Column: Data Detail Karyawan & Pekerjaan -->
        <div class="lg:col-span-1 space-y-6">

            <!-- Data Diri Card -->
            <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-xs space-y-4">
                <h3 class="text-xs font-black text-slate-800 uppercase tracking-wider flex items-center gap-2 pb-3 border-b border-slate-100">
                    <i class="fa-solid fa-user text-primary"></i>
                    <span>Informasi Karyawan</span>
                </h3>

                <div class="space-y-3 text-xs">
                    <div>
                        <div class="text-slate-400 text-[11px]">Nama Lengkap Sesuai KTP:</div>
                        <div class="font-bold text-slate-800 text-sm">{{ $paklaring->nama_lengkap }}</div>
                    </div>
                    <div>
                        <div class="text-slate-400 text-[11px]">Nomor KTP (NIK):</div>
                        <div class="font-mono font-bold text-slate-800">{{ $paklaring->nik }}</div>
                    </div>
                    <div>
                        <div class="text-slate-400 text-[11px]">Tempat, Tanggal Lahir:</div>
                        <div class="font-semibold text-slate-800">
                            {{ $paklaring->tempat_lahir ?: '-' }}, {{ $paklaring->tgl_lahir ? $paklaring->tgl_lahir->format('d M Y') : '-' }}
                        </div>
                    </div>
                    <div>
                        <div class="text-slate-400 text-[11px]">Jenis Kelamin:</div>
                        <div class="font-semibold text-slate-800">{{ $paklaring->jenis_kelamin ?: '-' }}</div>
                    </div>
                    <div>
                        <div class="text-slate-400 text-[11px]">Nomor WhatsApp / HP:</div>
                        <div class="font-semibold text-slate-800 flex items-center gap-1.5">
                            <span>{{ $paklaring->no_hp }}</span>
                            <a href="https://wa.me/{{ $paklaring->no_hp }}" target="_blank" class="text-emerald-600 hover:text-emerald-700" title="Chat WhatsApp">
                                <i class="fa-brands fa-whatsapp text-sm"></i>
                            </a>
                        </div>
                    </div>
                    <div>
                        <div class="text-slate-400 text-[11px]">Email Aktif:</div>
                        <div class="font-semibold text-slate-800">{{ $paklaring->email }}</div>
                    </div>
                    <div>
                        <div class="text-slate-400 text-[11px]">Alamat Lengkap KTP:</div>
                        <div class="text-slate-700 leading-relaxed">{{ $paklaring->alamat ?: '-' }}</div>
                    </div>
                </div>
            </div>

            <!-- Data Pekerjaan Card -->
            <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-xs space-y-4">
                <h3 class="text-xs font-black text-slate-800 uppercase tracking-wider flex items-center gap-2 pb-3 border-b border-slate-100">
                    <i class="fa-solid fa-briefcase text-indigo-600"></i>
                    <span>Riwayat Pekerjaan &amp; Pengiriman</span>
                </h3>

                <div class="space-y-3 text-xs">
                    <div>
                        <div class="text-slate-400 text-[11px]">Prinsiple Rekanan:</div>
                        <div class="font-bold text-slate-800">{{ $paklaring->prinsiple }}</div>
                    </div>
                    <div>
                        <div class="text-slate-400 text-[11px]">Entitas Kantor Inhouse:</div>
                        <div class="font-bold text-primary">{{ $paklaring->kantor ?: 'AMK' }}</div>
                    </div>
                    <div>
                        <div class="text-slate-400 text-[11px]">Cabang Area / Region:</div>
                        <div class="font-semibold text-slate-800">{{ $paklaring->area }} {{ $paklaring->region ? '('.$paklaring->region.')' : '' }}</div>
                    </div>
                    <div>
                        <div class="text-slate-400 text-[11px]">Jabatan Terakhir:</div>
                        <div class="font-bold text-slate-800">{{ $paklaring->jabatan }}</div>
                    </div>
                    <div>
                        <div class="text-slate-400 text-[11px]">Alasan Berhenti:</div>
                        <div class="font-semibold text-slate-800">{{ $paklaring->alasan }}</div>
                    </div>
                    <div class="pt-2 border-t border-slate-100 grid grid-cols-2 gap-2">
                        <div>
                            <div class="text-slate-400 text-[10px]">Tgl Masuk Kerja:</div>
                            <div class="font-bold text-slate-800">{{ $paklaring->tgl_masuk ? $paklaring->tgl_masuk->format('d M Y') : '-' }}</div>
                        </div>
                        <div>
                            <div class="text-slate-400 text-[10px]">Tgl Keluar Kerja:</div>
                            <div class="font-bold text-slate-800">{{ $paklaring->tgl_keluar ? $paklaring->tgl_keluar->format('d M Y') : '-' }}</div>
                        </div>
                    </div>
                    <div class="pt-2 border-t border-slate-100">
                        <div class="text-slate-400 text-[10px]">Ekspedisi &amp; Nomor Resi:</div>
                        <div class="font-semibold text-slate-800">
                            @if(!empty($paklaring->xpdc) || !empty($paklaring->noresi))
                                <span class="font-bold text-slate-900">{{ $paklaring->xpdc }}</span> &bull; Resi: <span class="font-mono font-bold text-primary">{{ $paklaring->noresi }}</span>
                                @if($paklaring->tgl_kirimsurat)
                                    <div class="text-[10px] text-slate-400">Tgl Kirim: {{ $paklaring->tgl_kirimsurat->format('d M Y') }}</div>
                                @endif
                            @else
                                <span class="text-slate-400 italic">Belum diinput tim Area</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Rekening & Deposit (Jika Ada) -->
            @if($paklaring->status_deposit === 'Ya' || !empty($paklaring->nomor_rekening) || !empty($paklaring->kebank) || !empty($paklaring->kerekening))
            <div class="bg-white rounded-2xl p-6 border border-emerald-200 shadow-xs space-y-4">
                <h3 class="text-xs font-black text-emerald-800 uppercase tracking-wider flex items-center gap-2 pb-3 border-b border-emerald-100">
                    <i class="fa-solid fa-piggy-bank text-emerald-600"></i>
                    <span>Pencairan Deposit Jaminan</span>
                </h3>
                
                <!-- Data Rekening Bank Pemohon (Untuk Pencairan Deposit) -->
                <div class="space-y-2 text-xs">
                    <div class="text-[11px] font-black text-slate-700 uppercase tracking-wider flex items-center gap-1.5 pb-1 border-b border-slate-100">
                        <i class="fa-solid fa-wallet text-emerald-600 text-xs"></i>
                        <span>Rekening Pemohon (Penerima)</span>
                    </div>
                    <div>
                        <div class="text-slate-400 text-[11px]">Bank:</div>
                        <div class="font-bold text-slate-800">{{ $paklaring->bank ?: '-' }}</div>
                    </div>
                    <div>
                        <div class="text-slate-400 text-[11px]">Nomor Rekening:</div>
                        <div class="font-mono font-bold text-slate-800">{{ $paklaring->nomor_rekening ?: '-' }}</div>
                    </div>
                    <div>
                        <div class="text-slate-400 text-[11px]">Pemilik Rekening:</div>
                        <div class="font-semibold text-slate-800">{{ $paklaring->nama_rekening ?: '-' }}</div>
                    </div>
                    <div>
                        <div class="text-slate-400 text-[11px]">Jumlah Deposit:</div>
                        <div class="font-bold text-emerald-700">Rp {{ number_format((float)$paklaring->jml_deposit, 0, ',', '.') }}</div>
                    </div>
                </div>

                <!-- Data Bank Deposit (Bank Tujuan Deposit Awal) -->
                @if($paklaring->kebank || $paklaring->kerekening || $paklaring->tanggaldeposit)
                <div class="pt-3 border-t border-emerald-100 space-y-2 text-xs">
                    <div class="text-[11px] font-black text-slate-700 uppercase tracking-wider flex items-center gap-1.5 pb-1 border-b border-slate-100">
                        <i class="fa-solid fa-building-columns text-cyan-600 text-xs"></i>
                        <span>Bank Tujuan Deposit Awal</span>
                    </div>
                    <div>
                        <div class="text-slate-400 text-[11px]">Transfer Ke (Bank):</div>
                        <div class="font-bold text-slate-800">{{ $paklaring->kebank ?: '-' }}</div>
                    </div>
                    <div>
                        <div class="text-slate-400 text-[11px]">Nomor Rekening / VA:</div>
                        <div class="font-mono font-bold text-slate-800">{{ $paklaring->kerekening ?: '-' }}</div>
                    </div>
                    <div>
                        <div class="text-slate-400 text-[11px]">Tanggal Deposit:</div>
                        <div class="font-semibold text-slate-800">{{ $paklaring->tanggaldeposit ? $paklaring->tanggaldeposit->format('d M Y') : '-' }}</div>
                    </div>
                </div>
                @endif
            </div>
            @endif

        </div>

        <!-- Right Column: Interactive Approval Box & Gallery & Logs -->
        <div class="lg:col-span-2 space-y-6">

            <!-- Kotak Aksi Approval (Hanya tampil jika user berhak approve) -->
            @if($canApprove && $paklaring->status !== 'Selesai' && $paklaring->status !== 'Tolak')
            <div class="bg-gradient-to-br from-white to-blue-50/40 rounded-2xl p-6 border-2 border-primary/30 shadow-md space-y-5">
                <div class="flex items-center justify-between pb-3 border-b border-primary/10">
                    <div class="flex items-center gap-2">
                        <span class="w-8 h-8 rounded-xl bg-primary text-white flex items-center justify-center font-black text-xs shadow-sm">
                            <i class="fa-solid fa-gavel text-xs"></i>
                        </span>
                        <div>
                            <h3 class="text-sm font-black text-slate-900">Panel Keputusan Persetujuan: {{ $paklaring->bagian_badge['label'] }}</h3>
                            <p class="text-[11px] text-slate-500">Anda memiliki hak wewenang untuk memproses tahap ini.</p>
                        </div>
                    </div>
                </div>

                <!-- FORM AKSI STEP 1: PERSATUJUAN AREA -->
                @if($currOrder === 1 || $paklaring->status_bagian === 'Area')
                <form action="{{ route('paklaring.approve-area', $paklaring->id) }}" method="POST" class="space-y-4" id="formApproveArea">
                    @csrf
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 text-xs">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Tanggal Masuk Kerja <span class="text-rose-500">*</span></label>
                            <input type="date" name="tgl_masuk" value="{{ old('tgl_masuk', $paklaring->tgl_masuk?->format('Y-m-d')) }}" required class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-semibold focus:ring-2 focus:ring-primary/20">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Tanggal Keluar Kerja <span class="text-rose-500">*</span></label>
                            <input type="date" name="tgl_keluar" value="{{ old('tgl_keluar', $paklaring->tgl_keluar?->format('Y-m-d')) }}" required class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-semibold focus:ring-2 focus:ring-primary/20">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Tanggal Kirim Berkas Fisik <span class="text-rose-500">*</span></label>
                            <input type="date" name="tgl_kirimsurat" value="{{ old('tgl_kirimsurat', $paklaring->tgl_kirimsurat?->format('Y-m-d') ?: date('Y-m-d')) }}" required class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-semibold focus:ring-2 focus:ring-primary/20">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Kurir / Ekspedisi Pengiriman <span class="text-rose-500">*</span></label>
                            <input type="text" name="xpdc" value="{{ old('xpdc', $paklaring->xpdc ?: 'JNE') }}" required placeholder="JNE / J&T / SiCepat / Lion Parcel" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-semibold focus:ring-2 focus:ring-primary/20 uppercase">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block font-bold text-slate-700 mb-1">Nomor Resi Pengiriman Fisik <span class="text-rose-500">*</span></label>
                            <input type="text" name="noresi" value="{{ old('noresi', $paklaring->noresi) }}" required placeholder="Masukkan nomor resi ekspedisi" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-semibold focus:ring-2 focus:ring-primary/20 font-mono">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block font-bold text-slate-700 mb-1">Catatan Tim Area (AS) <span class="text-rose-500">*</span></label>
                            <textarea name="catatan" rows="2" required placeholder="Catatan kelengkapan berkas fisik, serah terima seragam/ID card, exit interview..." class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-semibold focus:ring-2 focus:ring-primary/20">{{ old('catatan', $paklaring->catatan_aro) }}</textarea>
                        </div>
                    </div>

                    <!-- Tanda Tangan Digital Area (Signature Pad) -->
                    <div class="space-y-1.5 pt-2">
                        <div class="flex items-center justify-between">
                            <label class="block text-xs font-bold text-slate-700">Tanda Tangan Digital Petugas Penerima Area (Opsional):</label>
                            <button type="button" @click="clearSignature()" class="text-[11px] text-rose-600 hover:text-rose-800 font-bold">
                                Bersihkan Tanda Tangan
                            </button>
                        </div>
                        <div class="border-2 border-dashed border-slate-300 rounded-xl bg-white overflow-hidden">
                            <canvas id="signaturePad" width="500" height="130" class="w-full h-32 touch-none cursor-crosshair"></canvas>
                        </div>
                        <input type="hidden" name="tandatangan_base64" id="tandatangan_base64">
                    </div>

                    <div class="flex items-center justify-between gap-3 pt-3 border-t border-slate-200/80">
                        <div class="flex items-center gap-2">
                            <button type="button" @click="openHoldModal()" class="px-3.5 py-2 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-700 text-xs font-bold border border-amber-200">
                                <i class="fa-solid fa-pause text-xs mr-1"></i>
                                <span>Hold</span>
                            </button>
                            <button type="button" @click="openRejectModal()" class="px-3.5 py-2 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-bold border border-rose-200">
                                <i class="fa-solid fa-xmark text-xs mr-1"></i>
                                <span>Tolak</span>
                            </button>
                        </div>

                        <button type="submit" @click="prepareSignature()" class="px-5 py-2.5 rounded-xl bg-primary hover:bg-primary-700 text-white text-xs font-bold shadow-md shadow-primary/20 flex items-center gap-2 cursor-pointer">
                            <i class="fa-solid fa-check text-xs"></i>
                            <span>Setujui ke Tahap HRD</span>
                        </button>
                    </div>
                </form>
                @endif

                <!-- FORM AKSI STEP 2: PERSATUJUAN HRD -->
                @if($currOrder === 2 || $paklaring->status_bagian === 'HRD')
                <form action="{{ route('paklaring.approve-hrd', $paklaring->id) }}" method="POST" class="space-y-4">
                    @csrf
                    <div class="space-y-3 text-xs">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Catatan Verifikasi HRD <span class="text-rose-500">*</span></label>
                            <textarea name="catatan" rows="3" required placeholder="Catatan keabsahan data kerja, konfirmasi status karyawan, rujukan..." class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-semibold focus:ring-2 focus:ring-primary/20">{{ old('catatan', $paklaring->catatan_hrd) }}</textarea>
                        </div>

                        <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200 space-y-2">
                            <label class="block font-bold text-slate-800">Tujuan Tahap Selanjutnya:</label>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <label class="flex items-center gap-2.5 p-3 rounded-xl border border-slate-200 bg-white cursor-pointer hover:bg-slate-50">
                                    <input type="radio" name="action_to" value="DB" checked class="text-primary focus:ring-primary">
                                    <div>
                                        <div class="font-bold text-slate-800">Teruskan ke Tim DB (Rekomendasi)</div>
                                        <div class="text-[10px] text-slate-400">Pencocokan database riwayat sebelum BPJS</div>
                                    </div>
                                </label>
                                <label class="flex items-center gap-2.5 p-3 rounded-xl border border-slate-200 bg-white cursor-pointer hover:bg-slate-50">
                                    <input type="radio" name="action_to" value="BPJS" class="text-primary focus:ring-primary">
                                    <div>
                                        <div class="font-bold text-slate-800">Bypass Langsung ke BPJS</div>
                                        <div class="text-[10px] text-slate-400">Langsung ke tahap penomoran akhir</div>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between gap-3 pt-3 border-t border-slate-200/80">
                        <button type="button" @click="openRejectModal()" class="px-3.5 py-2 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-bold border border-rose-200">
                            <i class="fa-solid fa-xmark text-xs mr-1"></i>
                            <span>Tolak Pengajuan</span>
                        </button>

                        <button type="submit" class="px-6 py-2.5 rounded-xl bg-purple-600 hover:bg-purple-700 text-white text-xs font-bold shadow-md shadow-purple-600/20 flex items-center gap-2 cursor-pointer">
                            <i class="fa-solid fa-check text-xs"></i>
                            <span>Simpan Keputusan HRD</span>
                        </button>
                    </div>
                </form>
                @endif

                <!-- FORM AKSI STEP 3: PERSATUJUAN TIM DB -->
                @if($currOrder === 3 || $paklaring->status_bagian === 'DB')
                <form action="{{ route('paklaring.approve-db', $paklaring->id) }}" method="POST" class="space-y-4" x-data="{ dbAction: 'bpjs' }">
                    @csrf
                    
                    <!-- Pilihan Aksi Keputusan Tim DB -->
                    <div class="space-y-2 text-xs">
                        <label class="block font-bold text-slate-800">Pilih Tindakan / Keputusan Tim DB: <span class="text-rose-500">*</span></label>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <!-- Opsi 1: Lanjut ke BPJS -->
                            <label class="flex items-start gap-2.5 p-3 rounded-xl border cursor-pointer transition-all"
                                   :class="dbAction === 'bpjs' ? 'border-cyan-500 bg-cyan-50/50 ring-2 ring-cyan-500/20' : 'border-slate-200 bg-white hover:bg-slate-50'">
                                <input type="radio" name="action_type" value="bpjs" x-model="dbAction" class="mt-0.5 text-cyan-600 focus:ring-cyan-500">
                                <div>
                                    <div class="font-bold text-slate-800">Lanjut ke Step BPJS</div>
                                    <div class="text-[10px] text-slate-500 leading-tight mt-0.5">Teruskan berkas ke verifikasi Tim BPJS</div>
                                </div>
                            </label>

                            <!-- Opsi 2: Approve (Surat Rilis) -->
                            <label class="flex items-start gap-2.5 p-3 rounded-xl border cursor-pointer transition-all"
                                   :class="dbAction === 'selesai' ? 'border-emerald-500 bg-emerald-50/50 ring-2 ring-emerald-500/20' : 'border-slate-200 bg-white hover:bg-slate-50'">
                                <input type="radio" name="action_type" value="selesai" x-model="dbAction" class="mt-0.5 text-emerald-600 focus:ring-emerald-500">
                                <div>
                                    <div class="font-bold text-emerald-800">Approve (Surat Rilis)</div>
                                    <div class="text-[10px] text-slate-500 leading-tight mt-0.5">Terbitkan No. Ref &amp; rilis surat resmi langsung selesai</div>
                                </div>
                            </label>

                            <!-- Opsi 3: Kembalikan ke Area -->
                            <label class="flex items-start gap-2.5 p-3 rounded-xl border cursor-pointer transition-all"
                                   :class="dbAction === 'return_area' ? 'border-amber-500 bg-amber-50/50 ring-2 ring-amber-500/20' : 'border-slate-200 bg-white hover:bg-slate-50'">
                                <input type="radio" name="action_type" value="return_area" x-model="dbAction" class="mt-0.5 text-amber-600 focus:ring-amber-500">
                                <div>
                                    <div class="font-bold text-amber-800">Kembalikan ke Area</div>
                                    <div class="text-[10px] text-slate-500 leading-tight mt-0.5">Jika persyaratan belum lengkap</div>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Panel Penerbitan Nomor Referensi (Muncul saat Approve Surat Rilis atau opsional pada BPJS) -->
                    <div x-show="dbAction === 'selesai' || dbAction === 'bpjs'" x-transition class="p-4 rounded-xl border text-xs space-y-3"
                         :class="dbAction === 'selesai' ? 'bg-emerald-50/40 border-emerald-200' : 'bg-slate-50 border-slate-200'">
                        <div class="flex items-center justify-between pb-1.5 border-b" :class="dbAction === 'selesai' ? 'border-emerald-200/70' : 'border-slate-200'">
                            <span class="font-bold flex items-center gap-1.5" :class="dbAction === 'selesai' ? 'text-emerald-800' : 'text-slate-800'">
                                <i class="fa-solid fa-stamp"></i>
                                <span>Penerbitan Nomor Referensi Surat Kerja (No. Ref)</span>
                            </span>
                            <span class="text-[10px] px-2 py-0.5 rounded-full font-bold"
                                  :class="dbAction === 'selesai' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-200 text-slate-600'"
                                  x-text="dbAction === 'selesai' ? 'Wajib Diterbitkan' : 'Bisa Diisi di DB / BPJS'">
                            </span>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block font-bold text-slate-700 mb-1">Nomor Surat Referensi Kerja Resmi</label>
                                <input type="text" name="nomor_ref" value="{{ old('nomor_ref', $paklaring->nomor_ref) }}" placeholder="Otomatis digenerate sistem jika dikosongkan" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-semibold focus:ring-2 focus:ring-primary/20 font-mono">
                                <p class="text-[10px] text-slate-400 mt-0.5">Biarkan kosong untuk penomoran otomatis ESA Groups.</p>
                            </div>
                            <div>
                                <label class="block font-bold text-slate-700 mb-1">Nomor Urut Buku (Opsional)</label>
                                <input type="number" name="nomor_urutref" value="{{ old('nomor_urutref', $paklaring->nomor_urutref) }}" placeholder="Nomor urut register" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-semibold focus:ring-2 focus:ring-primary/20">
                                <p class="text-[10px] text-slate-400 mt-0.5">Biarkan kosong untuk nomor urut register berikutnya.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Koreksi Masa Kerja (Opsional) -->
                    <div x-show="dbAction !== 'return_area'" class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Koreksi Tgl Masuk (Opsional)</label>
                            <input type="date" name="tgl_masuk" value="{{ old('tgl_masuk', $paklaring->tgl_masuk?->format('Y-m-d')) }}" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-semibold focus:ring-2 focus:ring-primary/20">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Koreksi Tgl Keluar (Opsional)</label>
                            <input type="date" name="tgl_keluar" value="{{ old('tgl_keluar', $paklaring->tgl_keluar?->format('Y-m-d')) }}" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-semibold focus:ring-2 focus:ring-primary/20">
                        </div>
                    </div>

                    <!-- Catatan / Keterangan -->
                    <div class="space-y-1.5 text-xs">
                        <label class="block font-bold text-slate-700">
                            <span x-text="dbAction === 'return_area' ? 'Alasan Pengembalian ke Area (Persyaratan Belum Lengkap) *' : 'Catatan Tim Database (DB) *'"></span>
                        </label>
                        <textarea name="catatan" rows="3" required
                                  :placeholder="dbAction === 'return_area' ? 'Tuliskan secara rinci persyaratan/berkas yang belum lengkap sehingga Tim Area dapat melengkapinya...' : 'Catatan hasil verifikasi riwayat kerja di sistem database...'"
                                  class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-semibold focus:ring-2 focus:ring-primary/20">{{ old('catatan', $paklaring->catatan_db) }}</textarea>
                    </div>

                    <div class="flex items-center justify-between gap-3 pt-3 border-t border-slate-200/80">
                        <div class="flex items-center gap-2">
                            <button type="button" @click="openRejectModal()" class="px-3.5 py-2 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-bold border border-rose-200">
                                <i class="fa-solid fa-xmark text-xs mr-1"></i>
                                <span>Tolak</span>
                            </button>
                        </div>

                        <!-- Dynamic Submit Button based on choice -->
                        <div>
                            <button type="submit" 
                                    x-show="dbAction === 'bpjs'"
                                    class="px-6 py-2.5 rounded-xl bg-cyan-600 hover:bg-cyan-700 text-white text-xs font-bold shadow-md shadow-cyan-600/20 flex items-center gap-2 cursor-pointer">
                                <i class="fa-solid fa-paper-plane text-xs"></i>
                                <span>Lanjut ke Step BPJS</span>
                            </button>

                            <button type="submit" 
                                    x-show="dbAction === 'selesai'"
                                    class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-black shadow-md shadow-emerald-600/25 flex items-center gap-2 cursor-pointer">
                                <i class="fa-solid fa-certificate text-xs"></i>
                                <span>Approve (Surat Rilis Selesai)</span>
                            </button>

                            <button type="submit" 
                                    x-show="dbAction === 'return_area'"
                                    class="px-6 py-2.5 rounded-xl bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold shadow-md shadow-amber-600/25 flex items-center gap-2 cursor-pointer">
                                <i class="fa-solid fa-rotate-left text-xs"></i>
                                <span>Kembalikan ke Tim Area</span>
                            </button>
                        </div>
                    </div>
                </form>
                @endif

                <!-- FORM AKSI STEP 4: PERSATUJUAN TIM BPJS -->
                @if($currOrder === 4 || $paklaring->status_bagian === 'BPJS')
                <form action="{{ route('paklaring.approve-bpjs', $paklaring->id) }}" method="POST" class="space-y-4">
                    @csrf
                    <div class="space-y-3 text-xs">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                            <div>
                                <label class="block font-bold text-slate-700 mb-1">Nomor Surat Referensi Kerja Resmi</label>
                                <input type="text" name="nomor_ref" value="{{ old('nomor_ref', $paklaring->nomor_ref) }}" placeholder="Otomatis digenerate sistem jika dikosongkan" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-semibold focus:ring-2 focus:ring-primary/20 font-mono">
                                <p class="text-[10px] text-slate-400 mt-0.5">Biarkan kosong untuk penomoran otomatis ESA Groups.</p>
                            </div>
                            <div>
                                <label class="block font-bold text-slate-700 mb-1">Nomor Urut Buku (Opsional)</label>
                                <input type="number" name="nomor_urutref" value="{{ old('nomor_urutref', $paklaring->nomor_urutref) }}" placeholder="Nomor urut register" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-semibold focus:ring-2 focus:ring-primary/20">
                            </div>
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Catatan Tim BPJS</label>
                            <textarea name="catatan" rows="2" placeholder="Catatan nomor kepesertaan BPJS, tanggal nonaktif kepesertaan, dll." class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-semibold focus:ring-2 focus:ring-primary/20">{{ old('catatan', $paklaring->catatan_bpjs) }}</textarea>
                        </div>
                    </div>

                    <div class="flex items-center justify-between gap-3 pt-3 border-t border-slate-200/80">
                        <div class="flex items-center gap-2">
                            <button type="button" @click="openReturnModal('HRD')" class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold">
                                <i class="fa-solid fa-rotate-left text-xs mr-1"></i>
                                <span>Kembalikan ke HRD</span>
                            </button>
                            <button type="button" @click="openRejectModal()" class="px-3.5 py-2 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-bold border border-rose-200">
                                <i class="fa-solid fa-xmark text-xs mr-1"></i>
                                <span>Tolak</span>
                            </button>
                        </div>

                        <button type="submit" class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-black shadow-md shadow-emerald-600/20 flex items-center gap-2 cursor-pointer">
                            <i class="fa-solid fa-certificate text-xs"></i>
                            <span>Approve &amp; Terbitkan Surat Selesai</span>
                        </button>
                    </div>
                </form>
                @endif
            </div>
            @endif

            <!-- Galeri Dokumen Lampiran -->
            <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-xs space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <h3 class="text-xs font-black text-slate-800 uppercase tracking-wider flex items-center gap-2">
                        <i class="fa-solid fa-paperclip text-primary"></i>
                        <span>Berkas &amp; Dokumen Lampiran Pemohon</span>
                    </h3>
                    <span class="text-[11px] text-slate-400">Klik dokumen untuk melihat pratinjau</span>
                </div>

                @php
                    $docs = [
                        ['title' => 'Foto KTP Asli', 'path' => $paklaring->foto_ktp, 'icon' => 'fa-id-card', 'color' => 'text-blue-600'],
                        ['title' => 'Form Request Paklaring', 'path' => $paklaring->form_request, 'icon' => 'fa-file-pen', 'color' => 'text-indigo-600'],
                        ['title' => 'Exit Clearance', 'path' => $paklaring->exit_cl, 'icon' => 'fa-clipboard-check', 'color' => 'text-emerald-600'],
                        ['title' => 'Surat Pengunduran Diri', 'path' => $paklaring->pengunduran_diri, 'icon' => 'fa-file-signature', 'color' => 'text-purple-600'],
                        ['title' => 'Kartu BPJS Ketenagakerjaan', 'path' => $paklaring->kartu_bpjs, 'icon' => 'fa-shield-halved', 'color' => 'text-cyan-600'],
                        ['title' => 'Berita Acara Serah Terima Aset', 'path' => $paklaring->serah_terima, 'icon' => 'fa-handshake', 'color' => 'text-teal-600'],
                        ['title' => 'Surat Pernyataan (Khusus Loreal)', 'path' => $paklaring->surat_cl, 'icon' => 'fa-file-circle-check', 'color' => 'text-rose-600'],
                        ['title' => 'Buku Tabungan', 'path' => $paklaring->buku_tabungan, 'icon' => 'fa-book', 'color' => 'text-amber-600'],
                        ['title' => 'Bukti Setor Deposit', 'path' => $paklaring->bukti_deposit, 'icon' => 'fa-receipt', 'color' => 'text-emerald-600'],
                    ];
                @endphp

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                    @foreach($docs as $doc)
                        @if(!empty($doc['path']))
                            @php $fileUrl = $paklaring->getFileUrl($doc['path']); @endphp
                            <div class="p-3 rounded-xl border border-slate-200 hover:border-primary/40 bg-slate-50/50 hover:bg-slate-50 transition-all group flex flex-col justify-between space-y-2">
                                <div class="flex items-start gap-2.5">
                                    <div class="w-8 h-8 rounded-lg bg-white border border-slate-200 flex items-center justify-center shrink-0 {{ $doc['color'] }}">
                                        <i class="fa-solid {{ $doc['icon'] }} text-xs"></i>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <div class="text-xs font-bold text-slate-800 truncate" title="{{ $doc['title'] }}">{{ $doc['title'] }}</div>
                                        <div class="text-[10px] text-slate-400">Lampiran Resmi</div>
                                    </div>
                                </div>

                                <div class="flex items-center gap-1.5 pt-1">
                                    <button type="button" 
                                            @click="previewModal('{{ $doc['title'] }}', '{{ $fileUrl }}')" 
                                            class="flex-1 py-1 px-2 rounded-lg bg-white hover:bg-slate-100 text-slate-700 text-[11px] font-bold border border-slate-200 transition-all text-center">
                                        Pratinjau
                                    </button>
                                    <a href="{{ $fileUrl }}" target="_blank" download class="py-1 px-2 rounded-lg bg-white hover:bg-slate-100 text-slate-700 text-[11px] font-bold border border-slate-200 transition-all" title="Unduh File">
                                        <i class="fa-solid fa-download text-[10px]"></i>
                                    </a>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>

            <!-- Audit Trail & Log Riwayat Approval -->
            <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-xs space-y-4">
                <h3 class="text-xs font-black text-slate-800 uppercase tracking-wider flex items-center gap-2 pb-3 border-b border-slate-100">
                    <i class="fa-solid fa-clock-rotate-left text-primary"></i>
                    <span>Riwayat &amp; Catatan Keputusan (Audit Trail)</span>
                </h3>

                <div class="space-y-3">
                    @forelse($paklaring->approvals as $log)
                    <div class="p-3.5 rounded-xl border border-slate-100 bg-slate-50/60 text-xs space-y-1.5">
                        <div class="flex items-center justify-between text-slate-800">
                            <div class="flex items-center gap-2">
                                <span class="font-bold">{{ $log->step_name }}</span>
                                <span class="text-slate-400">&bull;</span>
                                <span class="font-medium text-slate-600">{{ $log->user_name }}</span>
                                @if($log->action_to)
                                    <span class="text-[10px] px-1.5 py-0.5 rounded bg-blue-100 text-blue-800 font-bold">&rarr; {{ $log->action_to }}</span>
                                @endif
                            </div>
                            <span class="text-[10px] text-slate-400 font-medium">{{ $log->created_at?->format('d M Y, H:i') }}</span>
                        </div>
                        <p class="text-slate-600 italic">"{{ $log->notes }}"</p>
                    </div>
                    @empty
                    <div class="text-center py-6 text-slate-400 text-xs">
                        Belum ada riwayat keputusan approval.
                    </div>
                    @endforelse
                </div>
            </div>

        </div>

    </div>

    <!-- MODAL PRATINJAU DOKUMEN -->
    <div x-show="showDocModal" 
         x-transition
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs" 
         style="display: none;">
        <div class="bg-white rounded-2xl max-w-3xl w-full p-6 shadow-2xl border border-slate-200 space-y-4 max-h-[90vh] flex flex-col">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 shrink-0">
                <h3 class="text-sm font-black text-slate-800" x-text="modalTitle"></h3>
                <button type="button" @click="showDocModal = false" class="text-slate-400 hover:text-slate-600 p-1">
                    <i class="fa-solid fa-xmark text-base"></i>
                </button>
            </div>
            <div class="flex-1 overflow-auto flex items-center justify-center bg-slate-100 rounded-xl p-2 min-h-[300px]">
                <template x-if="isPdf(modalUrl)">
                    <iframe :src="modalUrl" class="w-full h-[600px] rounded-lg border-0"></iframe>
                </template>
                <template x-if="!isPdf(modalUrl)">
                    <img :src="modalUrl" class="max-h-[600px] max-w-full rounded-lg object-contain shadow-xs">
                </template>
            </div>
            <div class="flex items-center justify-between pt-2 shrink-0">
                <a :href="modalUrl" target="_blank" download class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition-all flex items-center gap-1.5">
                    <i class="fa-solid fa-download text-xs"></i>
                    <span>Unduh Berkas</span>
                </a>
                <button type="button" @click="showDocModal = false" class="px-4 py-2 rounded-xl bg-slate-200 hover:bg-slate-300 text-slate-700 text-xs font-bold">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    <!-- MODAL HOLD -->
    <div x-show="showHoldModal" x-transition class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60" style="display: none;">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-200 space-y-4">
            <h3 class="text-sm font-black text-slate-800 flex items-center gap-2">
                <i class="fa-solid fa-pause text-amber-500"></i>
                <span>Hold Pengajuan Paklaring</span>
            </h3>
            <p class="text-xs text-slate-500">Masukkan alasan penahanan sementara permohonan ini:</p>
            <form action="{{ route('paklaring.hold', $paklaring->id) }}" method="POST" class="space-y-4">
                @csrf
                <textarea name="catatan_hold" rows="3" required placeholder="Contoh: Menunggu pelunasan pinjaman inventaris, menunggu konfirmasi atasan..." class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-semibold focus:ring-2 focus:ring-amber-500/20"></textarea>
                <div class="flex justify-end gap-2">
                    <button type="button" @click="showHoldModal = false" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold">Batal</button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold">Simpan Hold</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL TOLAK -->
    <div x-show="showRejectModal" x-transition class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60" style="display: none;">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-200 space-y-4">
            <h3 class="text-sm font-black text-slate-800 flex items-center gap-2">
                <i class="fa-solid fa-circle-xmark text-rose-600"></i>
                <span>Tolak Pengajuan Paklaring</span>
            </h3>
            <p class="text-xs text-slate-500">Apakah Anda yakin ingin menolak pengajuan ini? Masukkan alasan penolakan secara jelas:</p>
            <form action="{{ route('paklaring.reject', $paklaring->id) }}" method="POST" class="space-y-4">
                @csrf
                <textarea name="alasan_penolakan" rows="3" required placeholder="Contoh: Belum menyelesaikan serah terima aset, masa kerja belum memenuhi syarat..." class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-semibold focus:ring-2 focus:ring-rose-500/20"></textarea>
                <div class="flex justify-end gap-2">
                    <button type="button" @click="showRejectModal = false" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold">Batal</button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold">Ya, Tolak Permohonan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL KEMBALIKAN (RETURN) -->
    <div x-show="showReturnModal" x-transition class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60" style="display: none;">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-200 space-y-4">
            <h3 class="text-sm font-black text-slate-800 flex items-center gap-2">
                <i class="fa-solid fa-rotate-left text-cyan-600"></i>
                <span>Kembalikan Pengajuan</span>
            </h3>
            <p class="text-xs text-slate-500">Kembalikan pengajuan ke tahap <strong x-text="targetReturnBagian"></strong> dengan catatan perbaikan:</p>
            <form action="{{ route('paklaring.return-back', $paklaring->id) }}" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="target_bagian" :value="targetReturnBagian">
                <textarea name="catatan_kembali" rows="3" required placeholder="Jelaskan data atau berkas apa yang perlu diperbaiki kembali..." class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-semibold focus:ring-2 focus:ring-cyan-500/20"></textarea>
                <div class="flex justify-end gap-2">
                    <button type="button" @click="showReturnModal = false" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold">Batal</button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-cyan-600 hover:bg-cyan-700 text-white text-xs font-bold">Kembalikan</button>
                </div>
            </form>
        </div>
    </div>

</div>

<script>
function paklaringDetail() {
    return {
        showDocModal: false,
        modalTitle: '',
        modalUrl: '',
        showHoldModal: false,
        showRejectModal: false,
        showReturnModal: false,
        targetReturnBagian: 'Area',

        previewModal(title, url) {
            this.modalTitle = title;
            this.modalUrl = url;
            this.showDocModal = true;
        },

        isPdf(url) {
            return url ? url.toLowerCase().includes('.pdf') : false;
        },

        openHoldModal() {
            this.showHoldModal = true;
        },

        openRejectModal() {
            this.showRejectModal = true;
        },

        openReturnModal(target) {
            this.targetReturnBagian = target;
            this.showReturnModal = true;
        },

        clearSignature() {
            const canvas = document.getElementById('signaturePad');
            if (canvas) {
                const ctx = canvas.getContext('2d');
                ctx.clearRect(0, 0, canvas.width, canvas.height);
            }
        },

        prepareSignature() {
            const canvas = document.getElementById('signaturePad');
            if (canvas) {
                const hiddenInput = document.getElementById('tandatangan_base64');
                // Check if canvas is not blank
                const blankCanvas = document.createElement('canvas');
                blankCanvas.width = canvas.width;
                blankCanvas.height = canvas.height;
                if (canvas.toDataURL() !== blankCanvas.toDataURL()) {
                    hiddenInput.value = canvas.toDataURL('image/png');
                }
            }
        },

        init() {
            // Setup canvas drawing
            const canvas = document.getElementById('signaturePad');
            if (canvas) {
                const ctx = canvas.getContext('2d');
                let drawing = false;

                const getPos = (e) => {
                    const rect = canvas.getBoundingClientRect();
                    const clientX = e.touches ? e.touches[0].clientX : e.clientX;
                    const clientY = e.touches ? e.touches[0].clientY : e.clientY;
                    return {
                        x: (clientX - rect.left) * (canvas.width / rect.width),
                        y: (clientY - rect.top) * (canvas.height / rect.height)
                    };
                };

                const startDraw = (e) => {
                    drawing = true;
                    ctx.beginPath();
                    const pos = getPos(e);
                    ctx.moveTo(pos.x, pos.y);
                    ctx.lineWidth = 2.5;
                    ctx.lineCap = 'round';
                    ctx.strokeStyle = '#0F52BA';
                };

                const draw = (e) => {
                    if (!drawing) return;
                    e.preventDefault();
                    const pos = getPos(e);
                    ctx.lineTo(pos.x, pos.y);
                    ctx.stroke();
                };

                const stopDraw = () => {
                    drawing = false;
                };

                canvas.addEventListener('mousedown', startDraw);
                canvas.addEventListener('mousemove', draw);
                canvas.addEventListener('mouseup', stopDraw);
                canvas.addEventListener('mouseleave', stopDraw);

                canvas.addEventListener('touchstart', startDraw);
                canvas.addEventListener('touchmove', draw);
                canvas.addEventListener('touchend', stopDraw);
            }
        }
    }
}
</script>
@endsection
