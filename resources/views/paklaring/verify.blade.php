@extends('layouts.public')

@section('title', 'Verifikasi Keabsahan Dokumen Veklaring - ESA Groups')

@section('content')
<div class="min-h-screen py-8 sm:py-12 px-4 sm:px-6 lg:px-8 bg-slate-50/70">
    <div class="max-w-2xl mx-auto space-y-6">

        @if($paklaring)
            @php
                $isCompleted = ($paklaring->status === 'Selesai' || !empty($paklaring->nomor_ref));
                $tglMasuk = $formatTanggalIndo($paklaring->tgl_masuk);
                $tglKeluar = $formatTanggalIndo($paklaring->tgl_keluar);
                
                $bpjsApproval = $paklaring->approvals->firstWhere('step_order', 4);
                $tanggalTerbit = $formatTanggalIndo($bpjsApproval ? $bpjsApproval->created_at : ($paklaring->updated_at ?? now()));
            @endphp

            @if($isCompleted)
                <!-- ========================================== -->
                <!-- 1. KONDISI: DOKUMEN RESMI & TERVERIFIKASI  -->
                <!-- ========================================== -->

                <!-- Kartu Status Keabsahan Resmi -->
                <div class="bg-white rounded-3xl p-6 sm:p-8 border border-emerald-200/90 shadow-lg shadow-emerald-500/5 space-y-5 text-center relative overflow-hidden">
                    <div class="absolute top-0 left-0 right-0 h-2 bg-gradient-to-r from-emerald-500 via-teal-500 to-emerald-600"></div>

                    <!-- Badge Header -->
                    <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-emerald-50 border border-emerald-200 text-emerald-800 text-[11px] sm:text-xs font-bold tracking-wide">
                        <i class="fa-solid fa-certificate text-emerald-600"></i>
                        <span>ASystem Digital Document Authentication</span>
                    </div>

                    <!-- Icon Perisai Centang Terverifikasi -->
                    <div class="flex justify-center pt-1">
                        <div class="relative">
                            <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-3xl bg-emerald-500 text-white flex items-center justify-center text-4xl sm:text-5xl shadow-xl shadow-emerald-500/25 ring-8 ring-emerald-50">
                                <i class="fa-solid fa-shield-halved"></i>
                            </div>
                            <div class="absolute -bottom-1 -right-1 w-8 h-8 rounded-full bg-white text-emerald-600 flex items-center justify-center text-base shadow border border-emerald-100">
                                <i class="fa-solid fa-circle-check"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Judul & Subtitle -->
                    <div class="space-y-1.5">
                        <h1 class="text-xl sm:text-2xl font-black text-slate-800 tracking-tight">
                            Dokumen Resmi Terverifikasi
                        </h1>
                        <p class="text-xs sm:text-sm text-slate-500 max-w-md mx-auto leading-relaxed">
                            Surat Keterangan Kerja (Veklaring) ini diterbitkan secara sah dan tercatat secara valid pada Pangkalan Data Sistem Kepegawaian ESA Groups.
                        </p>
                    </div>

                    <!-- Nomor Surat & Kode Validasi Ribbon -->
                    <div class="p-4 rounded-2xl bg-emerald-50/70 border border-emerald-200/80 text-left space-y-3">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                            <div>
                                <span class="text-[10px] font-bold text-emerald-700 uppercase tracking-wider block">Nomor Surat Referensi Kerja:</span>
                                <span class="text-base sm:text-lg font-black font-mono text-emerald-950 tracking-wider">
                                    {{ $paklaring->nomor_ref ?: '-' }}
                                </span>
                            </div>
                            <div class="sm:text-right">
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Kode Unik Validasi:</span>
                                <span class="text-xs sm:text-sm font-bold font-mono text-slate-700 bg-white px-2.5 py-1 rounded-lg border border-slate-200 inline-block mt-0.5">
                                    {{ $paklaring->kode_validasi }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Kartu Detail Data Karyawan & Riwayat Pekerjaan -->
                <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-xs space-y-5">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <h2 class="text-xs sm:text-sm font-black text-slate-800 uppercase tracking-wider flex items-center gap-2">
                            <i class="fa-solid fa-id-badge text-primary text-base"></i>
                            <span>Rincian Data Karyawan Tertera</span>
                        </h2>
                        <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-full border border-emerald-100 flex items-center gap-1">
                            <i class="fa-solid fa-lock text-[9px]"></i>
                            <span>Sah &amp; Asli</span>
                        </span>
                    </div>

                    <!-- Grid Rincian -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                        <div class="p-3.5 rounded-xl bg-slate-50/80 border border-slate-100 space-y-1">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Nama Lengkap Karyawan:</span>
                            <span class="font-extrabold text-slate-800 text-sm block">{{ $paklaring->nama_lengkap }}</span>
                        </div>

                        <div class="p-3.5 rounded-xl bg-slate-50/80 border border-slate-100 space-y-1">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Nomor Induk Kependudukan (NIK):</span>
                            <span class="font-mono font-bold text-slate-700 text-sm block">
                                {{ substr($paklaring->nik, 0, 6) . '******' . substr($paklaring->nik, -4) }}
                            </span>
                        </div>

                        <div class="p-3.5 rounded-xl bg-slate-50/80 border border-slate-100 space-y-1">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Jabatan Terakhir:</span>
                            <span class="font-bold text-slate-800 block">{{ $paklaring->jabatan }}</span>
                        </div>

                        <div class="p-3.5 rounded-xl bg-slate-50/80 border border-slate-100 space-y-1">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Prinsiple / Mitra Rekanan:</span>
                            <span class="font-bold text-slate-800 block">{{ $paklaring->prinsiple }}</span>
                        </div>

                        <div class="p-3.5 rounded-xl bg-slate-50/80 border border-slate-100 space-y-1">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Area / Cabang Penempatan:</span>
                            <span class="font-bold text-slate-800 block">{{ $paklaring->area }}</span>
                        </div>

                        <div class="p-3.5 rounded-xl bg-slate-50/80 border border-slate-100 space-y-1">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Periode Masa Kerja:</span>
                            <span class="font-bold text-slate-800 block">
                                {{ $tglMasuk }} s/d {{ $tglKeluar }}
                            </span>
                        </div>

                        <div class="p-3.5 rounded-xl bg-slate-50/80 border border-slate-100 space-y-1 sm:col-span-2">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Perusahaan / Entitas Penerbit Surat:</span>
                            <span class="font-extrabold text-slate-800 text-sm block flex items-center gap-1.5">
                                <i class="fa-solid fa-building text-primary"></i>
                                <span>{{ $entityUpper }} ({{ $entityTitle }})</span>
                            </span>
                        </div>

                        <div class="p-3.5 rounded-xl bg-slate-50/80 border border-slate-100 space-y-1">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Penandatangan Resmi:</span>
                            <span class="font-bold text-slate-800 block">Nurul Yuliastuti (Head HRD)</span>
                        </div>

                        <div class="p-3.5 rounded-xl bg-slate-50/80 border border-slate-100 space-y-1">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Tanggal Terbit Dokumen:</span>
                            <span class="font-bold text-slate-800 block">{{ $tanggalTerbit }}</span>
                        </div>
                    </div>

                    <!-- Tahapan Verifikasi Approval -->
                    <div class="pt-3 border-t border-slate-100 space-y-2.5">
                        <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block">
                            Audit Trail Pengesahan Bertingkat:
                        </span>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                            <div class="p-2 rounded-xl bg-emerald-50/60 border border-emerald-100 text-center">
                                <i class="fa-solid fa-circle-check text-emerald-600 text-xs"></i>
                                <div class="text-[11px] font-bold text-slate-800 mt-0.5">1. Area</div>
                                <div class="text-[9px] text-emerald-700 font-semibold">Disetujui</div>
                            </div>
                            <div class="p-2 rounded-xl bg-emerald-50/60 border border-emerald-100 text-center">
                                <i class="fa-solid fa-circle-check text-emerald-600 text-xs"></i>
                                <div class="text-[11px] font-bold text-slate-800 mt-0.5">2. HRD</div>
                                <div class="text-[9px] text-emerald-700 font-semibold">Disetujui</div>
                            </div>
                            <div class="p-2 rounded-xl bg-emerald-50/60 border border-emerald-100 text-center">
                                <i class="fa-solid fa-circle-check text-emerald-600 text-xs"></i>
                                <div class="text-[11px] font-bold text-slate-800 mt-0.5">3. Database</div>
                                <div class="text-[9px] text-emerald-700 font-semibold">Disetujui</div>
                            </div>
                            <div class="p-2 rounded-xl bg-emerald-50/60 border border-emerald-100 text-center">
                                <i class="fa-solid fa-circle-check text-emerald-600 text-xs"></i>
                                <div class="text-[11px] font-bold text-slate-800 mt-0.5">4. BPJS</div>
                                <div class="text-[9px] text-emerald-700 font-semibold">Selesai Terbit</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tombol Aksi Dokumen (Mobile-Friendly Touch-Targets) -->
                <div class="space-y-3">
                    <a href="{{ route('paklaring.public.download', $paklaring->kode_validasi) }}" 
                       target="_blank"
                       class="w-full min-h-[48px] px-6 py-3.5 rounded-2xl bg-emerald-600 hover:bg-emerald-700 active:scale-[0.99] text-white text-xs sm:text-sm font-black tracking-wide shadow-lg shadow-emerald-600/25 transition-all flex items-center justify-center gap-2 select-none cursor-pointer">
                        <i class="fa-solid fa-file-arrow-down text-base"></i>
                        <span>Unduh Dokumen Surat Resmi (PDF)</span>
                    </a>

                    <a href="{{ route('paklaring.public.check') }}" 
                       class="w-full min-h-[44px] px-5 py-2.5 rounded-2xl bg-white hover:bg-slate-100 border border-slate-200 text-slate-700 text-xs font-bold transition-all flex items-center justify-center gap-2 select-none text-center shadow-2xs">
                        <i class="fa-solid fa-magnifying-glass text-xs text-slate-400"></i>
                        <span>Periksa Status Pengajuan Lain</span>
                    </a>
                </div>

                <!-- Catatan Hukum & Legalitas Keamanan -->
                <div class="p-4 rounded-2xl bg-slate-100/80 border border-slate-200/80 text-[11px] text-slate-500 leading-relaxed text-center space-y-1">
                    <div class="font-bold text-slate-700 flex items-center justify-center gap-1.5">
                        <i class="fa-solid fa-shield-halved text-emerald-600"></i>
                        <span>Keaslian Dokumen Elektronik Terlindungi</span>
                    </div>
                    <p>
                        Dokumen Surat Keterangan Kerja (Veklaring) ini diterbitkan secara sah oleh sistem informasi manajemen kepegawaian <strong>ASystem ESA Groups</strong> dan memiliki kekuatan hukum dokumen resmi perusahaan yang sah tanpa memerlukan tanda tangan basah fisik.
                    </p>
                </div>

            @else
                <!-- ========================================== -->
                <!-- 2. KONDISI: PERMOHONAN DALAM PROSES        -->
                <!-- ========================================== -->
                <div class="bg-white rounded-3xl p-6 sm:p-8 border border-blue-200 shadow-sm text-center space-y-4">
                    <div class="w-16 h-16 mx-auto rounded-3xl bg-blue-500 text-white flex items-center justify-center text-3xl shadow-lg shadow-blue-500/20">
                        <i class="fa-solid fa-hourglass-half fa-spin"></i>
                    </div>

                    <div class="space-y-1.5">
                        <span class="inline-block px-3 py-1 rounded-full bg-blue-50 text-blue-700 font-bold text-xs border border-blue-100">
                            Status: Sedang Dalam Proses Approval
                        </span>
                        <h1 class="text-xl sm:text-2xl font-black text-slate-800">
                            Dokumen Belum Selesai Diterbitkan
                        </h1>
                        <p class="text-xs sm:text-sm text-slate-500 max-w-md mx-auto">
                            Permohonan surat keterangan kerja untuk <strong>{{ $paklaring->nama_lengkap }}</strong> (Kode: <code class="font-mono">{{ $paklaring->kode_validasi }}</code>) saat ini sedang dalam tahapan <strong>{{ $paklaring->status_bagian_badge['label'] }}</strong>.
                        </p>
                    </div>

                    <div class="pt-2">
                        <a href="{{ route('paklaring.public.check', ['q' => $paklaring->kode_validasi]) }}" 
                           class="inline-flex items-center gap-2 px-6 py-3 rounded-2xl bg-primary text-white text-xs font-bold hover:bg-primary-700 transition-all shadow-md shadow-primary/20">
                            <i class="fa-solid fa-clock-rotate-left"></i>
                            <span>Pantau Progres di Halaman Cek Status</span>
                        </a>
                    </div>
                </div>
            @endif

        @else
            <!-- ========================================== -->
            <!-- 3. KONDISI: DOKUMEN TIDAK DITEMUKAN        -->
            <!-- ========================================== -->
            <div class="bg-white rounded-3xl p-8 sm:p-10 border border-rose-200 shadow-sm text-center space-y-4">
                <div class="w-16 h-16 mx-auto rounded-3xl bg-rose-500 text-white flex items-center justify-center text-3xl shadow-lg shadow-rose-500/20">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>

                <div class="space-y-1.5">
                    <span class="inline-block px-3 py-1 rounded-full bg-rose-50 text-rose-700 font-bold text-xs border border-rose-100">
                        Kode Dokumen Tidak Valid
                    </span>
                    <h1 class="text-xl sm:text-2xl font-black text-slate-800">
                        Data Dokumen Tidak Ditemukan
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-500 max-w-md mx-auto leading-relaxed">
                        Sistem tidak menemukan arsip surat keterangan kerja dengan kode atau nomor <strong>"{{ $kode }}"</strong>. Pastikan QR Code yang Anda pindai berasal dari dokumen asli yang diterbitkan oleh ESA Groups.
                    </p>
                </div>

                <div class="pt-3 flex flex-col sm:flex-row items-center justify-center gap-3">
                    <a href="{{ route('paklaring.public.check') }}" 
                       class="w-full sm:w-auto px-6 py-3 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition-all text-center">
                        <i class="fa-solid fa-magnifying-glass mr-1.5"></i>
                        <span>Cari dengan NIK</span>
                    </a>

                    <a href="{{ route('paklaring.public.create') }}" 
                       class="w-full sm:w-auto px-6 py-3 rounded-2xl bg-primary text-white text-xs font-bold hover:bg-primary-700 transition-all shadow-md shadow-primary/20 text-center">
                        <i class="fa-solid fa-plus mr-1.5"></i>
                        <span>Ajukan Surat Baru</span>
                    </a>
                </div>
            </div>
        @endif

    </div>
</div>
@endsection
