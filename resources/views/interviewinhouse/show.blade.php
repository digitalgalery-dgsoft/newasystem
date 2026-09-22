@extends('layouts.app')

@section('title', 'Evaluasi Inhouse - ' . $candidate->full_name)
@section('page_title', 'APPROVAL KANDIDAT INHOUSE')
@section('breadcrumb_active', 'Detail Inhouse')

@section('content')
<div class="space-y-6">

    <!-- TOP NAVIGATION -->
    <div class="flex items-center justify-between">
        <a href="{{ route('interviewinhouse.index') }}" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-bold text-slate-700 bg-white hover:bg-slate-50 border border-slate-200 shadow-sm transition">
            <i class="fa-solid fa-arrow-left text-xs"></i>
            <span>Kembali ke Kandidat Inhouse</span>
        </a>

        <div class="flex items-center gap-2">
            <span class="text-xs text-slate-500 font-medium">Role Approver:</span>
            @if($isHrd)
                <span class="px-2.5 py-1 rounded-lg text-xs font-black bg-purple-50 text-purple-700 border border-purple-200">
                    <i class="fa-solid fa-user-shield mr-1"></i> HRD Pusat
                </span>
            @else
                <span class="px-2.5 py-1 rounded-lg text-xs font-black bg-blue-50 text-blue-700 border border-blue-200">
                    <i class="fa-solid fa-user-tie mr-1"></i> Head Approver
                </span>
            @endif
        </div>
    </div>

    <!-- PROFIL KANDIDAT CARD (Persis Gambar 3) -->
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6 space-y-4">
        <h2 class="text-sm font-bold text-slate-800 tracking-tight">Profil Kandidat</h2>

        <div class="space-y-2.5 text-xs">
            <div class="grid grid-cols-12 gap-2">
                <div class="col-span-12 sm:col-span-3 text-slate-600 font-bold">No. KTP</div>
                <div class="col-span-12 sm:col-span-9 text-slate-800 font-mono">: {{ $candidate->nik }}</div>
            </div>
            <div class="grid grid-cols-12 gap-2">
                <div class="col-span-12 sm:col-span-3 text-slate-600 font-bold">Nama Kandidat</div>
                <div class="col-span-12 sm:col-span-9 text-slate-900 font-bold">: {{ $candidate->full_name }}</div>
            </div>
            <div class="grid grid-cols-12 gap-2">
                <div class="col-span-12 sm:col-span-3 text-slate-600 font-bold">Alamat KTP</div>
                <div class="col-span-12 sm:col-span-9 text-slate-800 leading-relaxed">: {{ $candidate->address_ktp ?? '-' }}</div>
            </div>
            <div class="grid grid-cols-12 gap-2">
                <div class="col-span-12 sm:col-span-3 text-slate-600 font-bold">Usia</div>
                <div class="col-span-12 sm:col-span-9 text-slate-800">: {{ $candidate->age }} Tahun</div>
            </div>
            <div class="grid grid-cols-12 gap-2">
                <div class="col-span-12 sm:col-span-3 text-slate-600 font-bold">Pendidikan Terakhir</div>
                <div class="col-span-12 sm:col-span-9 text-slate-800">: {{ $candidate->education ?? '-' }}</div>
            </div>
            <div class="grid grid-cols-12 gap-2">
                <div class="col-span-12 sm:col-span-3 text-slate-600 font-bold">Mobile</div>
                <div class="col-span-12 sm:col-span-9 text-slate-800">
                    : <a href="https://api.whatsapp.com/send?phone={{ $candidate->clean_whatsapp }}" target="_blank" class="text-emerald-700 font-semibold hover:underline">
                        {{ $candidate->phone ?? '-' }}
                    </a>
                </div>
            </div>
            <div class="grid grid-cols-12 gap-2">
                <div class="col-span-12 sm:col-span-3 text-slate-600 font-bold">Prinsiple</div>
                <div class="col-span-12 sm:col-span-9 text-slate-900 font-semibold">: {{ $candidate->principle ? $candidate->principle->name : 'PT ARINA MULTI KARYA' }} - {{ $candidate->area ?? 'Jakarta' }}</div>
            </div>
            <div class="grid grid-cols-12 gap-2">
                <div class="col-span-12 sm:col-span-3 text-slate-600 font-bold">User Request</div>
                <div class="col-span-12 sm:col-span-9 text-slate-800 font-medium">
                    : <span class="bg-slate-100 px-2 py-0.5 rounded text-slate-700 font-semibold border border-slate-200/80">
                        {{ $candidate->user_request ?? ($candidate->recruiter?->name ?? $candidate->user_display_name) }}
                    </span>
                </div>
            </div>
            <div class="grid grid-cols-12 gap-2 items-center">
                <div class="col-span-12 sm:col-span-3 text-slate-600 font-bold">Status</div>
                <div class="col-span-12 sm:col-span-9 flex items-center gap-2">
                    : <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-500 text-white">
                        {{ $candidate->status_approval ?? $candidate->status_kandidat ?? 'Proses' }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- ACTION BUTTONS (Persis Gambar 3) -->
    <div class="space-y-2">
        <div class="flex flex-wrap items-center gap-2.5">
            <!-- 1. Tombol Dokument Interview & Test Online (Biru Tua) -->
            <a href="{{ route('interview.pdf', $candidate->id) }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold text-white bg-slate-800 hover:bg-slate-900 shadow-sm transition">
                <i class="fa-solid fa-file-pdf text-rose-400"></i>
                <span>Dokument Interview & Test Online</span>
            </a>

            <!-- 2. Tombol Berkas Lamaran -->
            @if(!empty($candidate->berkas_lamaran))
                <a href="{{ route('interviewinhouse.berkas', $candidate->id) }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold text-white bg-slate-700 hover:bg-slate-800 shadow-sm transition">
                    <i class="fa-solid fa-file-lines text-sky-300"></i>
                    <span>Berkas Lamaran</span>
                </a>
            @else
                <button type="button" disabled class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold bg-slate-200 text-slate-400 cursor-not-allowed border border-slate-300 shadow-none">
                    <i class="fa-solid fa-file-lines text-slate-400"></i>
                    <span>Berkas Lamaran</span>
                </button>
            @endif
        </div>

        <p class="text-[11px] text-slate-500 italic flex items-center gap-1.5 pt-0.5">
            <i class="fa-solid fa-circle-info text-slate-400 text-xs"></i>
            <span>Jika Tombol Berkas Lamaran Berwarna Abu-abu & Tidak Bisa di Klik, Artinya User Belum Melampirkan Berkas Lamaran Kandidat.</span>
        </p>
    </div>

    <!-- MAIN SECTION: FORM APPROVAL & LIST HEAD APPROVE (Persis Gambar 3) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        
        <!-- KOLOM KIRI: Form Keputusan & Tanda Tangan Digital -->
        <div class="lg:col-span-6 bg-white border border-slate-200 rounded-2xl p-6 shadow-sm space-y-4">
            <div class="pb-2 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                    <i class="fa-solid fa-signature text-primary"></i>
                    <span>Form Approval {{ $isHrd ? 'HRD Pusat' : 'Head Approver' }}</span>
                </h3>
                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ $isHrd ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                    {{ $isHrd ? 'Step HRD' : 'Step Head' }}
                </span>
            </div>

            <form action="{{ route('interviewinhouse.approval', $candidate->id) }}" method="POST" id="inhouseApprovalForm" class="space-y-4">
                @csrf
                <input type="hidden" name="submit_type" value="{{ $isHrd ? 'hrd' : 'head' }}">
                <input type="hidden" name="signature_data" id="signatureDataInput" value="">

                <!-- 1. Keputusan -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Keputusan</label>
                    <select name="approval" class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:ring-4 focus:ring-primary-100 focus:border-primary outline-none cursor-pointer" required>
                        <option value="" disabled selected>Hasil Keputusan</option>
                        <option value="Approve">Approve</option>
                        <option value="Tolak">Tolak</option>
                    </select>
                </div>

                <!-- 2. Catatan -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Catatan</label>
                    <textarea name="catatan" rows="3" class="w-full bg-white border border-slate-200 rounded-xl p-3 text-xs text-slate-800 focus:ring-4 focus:ring-primary-100 focus:border-primary outline-none" placeholder="Masukkan catatan hasil evaluasi dan rekomendasi..." required></textarea>
                </div>

                <!-- 3. Tanda Tangan Canvas -->
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="text-xs font-bold text-slate-700">Tanda Tangan</label>
                        <div class="flex items-center gap-2">
                            @if(!empty($user->signature_path))
                                <button type="button" onclick="pasteMySavedSig()" class="text-[11px] font-bold text-emerald-700 hover:text-emerald-800 underline flex items-center gap-1 cursor-pointer">
                                    <i class="fa-solid fa-stamp text-[10px]"></i> Tempel TTD Saya
                                </button>
                            @endif
                            <button type="button" onclick="clearSigCanvas()" class="text-[11px] font-bold text-rose-600 hover:text-rose-700 underline flex items-center gap-1 cursor-pointer">
                                <i class="fa-solid fa-rotate-left text-[10px]"></i> Bersihkan
                            </button>
                        </div>
                    </div>

                    <div class="border-2 border-dashed border-slate-300 rounded-2xl p-1 bg-slate-50 relative overflow-hidden">
                        <canvas id="sigCanvas" class="w-full h-44 bg-white rounded-xl touch-none cursor-crosshair border border-slate-100 shadow-inner"></canvas>
                        <div id="sigPlaceholder" class="absolute inset-0 flex items-center justify-center pointer-events-none text-slate-300 text-xs font-semibold">
                            <span>Gambar tanda tangan digital di sini</span>
                        </div>
                    </div>
                </div>

                <!-- Tombol Submit -->
                <div class="pt-2">
                    <button type="submit" onclick="syncSigDataBeforeSubmit()" class="w-full py-3 rounded-xl text-xs font-bold text-white bg-rose-600 hover:bg-rose-700 shadow-md shadow-rose-600/20 transition-all flex items-center justify-center gap-2 cursor-pointer">
                        <i class="fa-solid fa-check-double text-xs"></i>
                        <span>Simpan & Submit Keputusan {{ $isHrd ? 'HRD Pusat' : 'Head' }}</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- KOLOM KANAN: Kandidat Info & List Head Approve (Persis Gambar 3) -->
        <div class="lg:col-span-6 space-y-6">
            
            <!-- 1. Ringkasan Status Kandidat -->
            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm space-y-3">
                <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                    <i class="fa-solid fa-user-check text-primary"></i>
                    <span>Kandidat</span>
                </h3>

                <div class="bg-slate-50 border border-slate-200/80 rounded-xl p-3.5 space-y-2 text-xs">
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500 font-medium">Status Formasi:</span>
                        <span class="font-bold text-slate-800">
                            {{ $candidate->status_replace === 'Replace' ? 'Replace (Penggantian)' : 'New (Formasi Baru)' }}
                        </span>
                    </div>

                    @if($candidate->status_replace === 'Replace')
                        <div class="flex items-center justify-between border-t border-slate-200/60 pt-2">
                            <span class="text-slate-500 font-medium">Menggantikan:</span>
                            <span class="font-bold text-slate-900">{{ $candidate->menggantikan ?: '-' }}</span>
                        </div>
                        <div class="flex items-center justify-between border-t border-slate-200/60 pt-2">
                            <span class="text-slate-500 font-medium">Tanggal Resign:</span>
                            <span class="font-bold text-slate-800">
                                {{ $candidate->tgl_resign ? \Carbon\Carbon::parse($candidate->tgl_resign)->format('d/m/Y') : '-' }}
                            </span>
                        </div>
                        <div class="flex items-start justify-between border-t border-slate-200/60 pt-2">
                            <span class="text-slate-500 font-medium shrink-0 w-28">Alasan Resign:</span>
                            <span class="text-slate-700 font-medium text-right">{{ $candidate->alasan_resign ?: '-' }}</span>
                        </div>
                    @endif

                    <div class="flex items-center justify-between border-t border-slate-200/60 pt-2">
                        <span class="text-slate-500 font-medium">Pengaju (User Request):</span>
                        <span class="font-bold text-primary">{{ $candidate->user_request ?: ($candidate->user_display_name ?: '-') }}</span>
                    </div>
                </div>
            </div>

            <!-- 2. List Head Approve (Tabel Persis Gambar 3) -->
            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm space-y-3">
                <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                    <i class="fa-solid fa-list-check text-primary"></i>
                    <span>List Head Approve</span>
                </h3>

                <div class="border border-slate-200 rounded-xl overflow-hidden">
                    <table class="w-full text-xs text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 font-bold uppercase tracking-wider text-[10.5px]">
                                <th class="py-2.5 px-3">Nama</th>
                                <th class="py-2.5 px-3">Catatan</th>
                                <th class="py-2.5 px-2.5 text-center">Hasil Keputusan</th>
                                <th class="py-2.5 px-2.5 text-center">Tanda Tangan</th>
                                <th class="py-2.5 px-3 text-center">Waktu Submit</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @php
                                $headApprovals = $candidate->inhouseApprovals->filter(function($appr) {
                                    $job = strtolower($appr->jabatan_approver ?? '');
                                    return !str_contains($job, 'admin hrd') && !str_contains($job, 'hr lead');
                                });
                            @endphp
                            @forelse($headApprovals as $hAppr)
                                <tr class="hover:bg-slate-50/70 transition-colors">
                                    <td class="py-3 px-3 font-bold text-slate-800">
                                        <div>{{ $hAppr->nama_approver }}</div>
                                        <div class="text-[9.5px] text-slate-400 font-normal">{{ $hAppr->jabatan_approver ?: 'Head' }}</div>
                                    </td>
                                    <td class="py-3 px-3 text-slate-600 leading-relaxed max-w-[150px]">
                                        {{ $hAppr->catatan_approver ?: '-' }}
                                    </td>
                                    <td class="py-3 px-2.5 text-center">
                                        @if(in_array(strtolower($hAppr->status ?? ''), ['approve', 'yes']))
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-200">Approve</span>
                                        @else
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-rose-100 text-rose-800 border border-rose-200">Tolak</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-2.5 text-center">
                                        @if($hAppr->ttd_approver)
                                            @php
                                                $sigSrc = $hAppr->ttd_approver;
                                                if (!str_starts_with($sigSrc, 'data:image') && !str_starts_with($sigSrc, 'http')) {
                                                    $sigSrc = asset($sigSrc);
                                                }
                                            @endphp
                                            <img src="{{ $sigSrc }}" alt="TTD Head" class="h-8 max-w-[80px] mx-auto object-contain" onerror="this.src='/lampiran/{{ basename($hAppr->ttd_approver) }}';">
                                        @else
                                            <span class="text-[9.5px] text-slate-400 italic">Belum TTD</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-3 text-center text-[10.5px] text-slate-500 whitespace-nowrap">
                                        {{ $hAppr->time_approver ? \Carbon\Carbon::parse($hAppr->time_approver)->format('d/m/Y H:i') : '-' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-6 text-center text-xs text-slate-400 italic bg-slate-50/50">
                                        Belum ada approval dari Head
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- 3. List Approval HRD jika sudah diapprove HRD -->
            @php
                $hrdApprovals = $candidate->inhouseApprovals->filter(function($appr) {
                    $job = strtolower($appr->jabatan_approver ?? '');
                    return str_contains($job, 'admin hrd') || str_contains($job, 'hr lead') || str_contains($job, 'hrd');
                });
            @endphp
            @if($hrdApprovals->isNotEmpty())
            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm space-y-3">
                <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                    <i class="fa-solid fa-stamp text-emerald-600"></i>
                    <span>Approval HRD Pusat</span>
                </h3>

                <div class="border border-slate-200 rounded-xl overflow-hidden">
                    <table class="w-full text-xs text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 font-bold uppercase tracking-wider text-[10.5px]">
                                <th class="py-2.5 px-3">Nama</th>
                                <th class="py-2.5 px-3">Catatan</th>
                                <th class="py-2.5 px-2.5 text-center">Hasil</th>
                                <th class="py-2.5 px-2.5 text-center">Tanda Tangan</th>
                                <th class="py-2.5 px-3 text-center">Waktu</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($hrdApprovals as $hrdAppr)
                                <tr class="hover:bg-slate-50/70 transition-colors">
                                    <td class="py-3 px-3 font-bold text-slate-800">
                                        <div>{{ $hrdAppr->nama_approver }}</div>
                                        <div class="text-[9.5px] text-slate-400 font-normal">{{ $hrdAppr->jabatan_approver ?: 'ADMIN HRD' }}</div>
                                    </td>
                                    <td class="py-3 px-3 text-slate-600 leading-relaxed max-w-[150px]">
                                        {{ $hrdAppr->catatan_approver ?: '-' }}
                                    </td>
                                    <td class="py-3 px-2.5 text-center">
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-200">Approved</span>
                                    </td>
                                    <td class="py-3 px-2.5 text-center">
                                        @if($hrdAppr->ttd_approver)
                                            @php
                                                $sigSrcH = $hrdAppr->ttd_approver;
                                                if (!str_starts_with($sigSrcH, 'data:image') && !str_starts_with($sigSrcH, 'http')) {
                                                    $sigSrcH = asset($sigSrcH);
                                                }
                                            @endphp
                                            <img src="{{ $sigSrcH }}" alt="TTD HRD" class="h-8 max-w-[80px] mx-auto object-contain" onerror="this.src='/lampiran/{{ basename($hrdAppr->ttd_approver) }}';">
                                        @else
                                            <span class="text-[9.5px] text-slate-400 italic">Belum TTD</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-3 text-center text-[10.5px] text-slate-500 whitespace-nowrap">
                                        {{ $hrdAppr->time_approver ? \Carbon\Carbon::parse($hrdAppr->time_approver)->format('d/m/Y H:i') : '-' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

        </div>

    </div>

</div>

@push('scripts')
<script>
    let canvas, ctx, isDrawing = false, hasDrawn = false;
    const mySavedSig = @json(!empty($user->signature_path) ? $user->getSignatureBase64() : null);

    function initSigPad() {
        canvas = document.getElementById('sigCanvas');
        if (!canvas) return;

        ctx = canvas.getContext('2d');
        const rect = canvas.getBoundingClientRect();
        canvas.width = Math.round(rect.width) || 400;
        canvas.height = Math.round(rect.height) || 176;

        ctx.lineWidth = 2.5;
        ctx.lineCap = 'round';
        ctx.lineJoin = 'round';
        ctx.strokeStyle = '#0f172a';

        function getPos(e) {
            const r = canvas.getBoundingClientRect();
            const scaleX = canvas.width / (r.width || 1);
            const scaleY = canvas.height / (r.height || 1);
            let clientX, clientY;
            if (e.touches && e.touches.length > 0) {
                clientX = e.touches[0].clientX;
                clientY = e.touches[0].clientY;
            } else {
                clientX = e.clientX;
                clientY = e.clientY;
            }
            return {
                x: (clientX - r.left) * scaleX,
                y: (clientY - r.top) * scaleY
            };
        }

        function start(e) {
            isDrawing = true;
            hasDrawn = true;
            const ph = document.getElementById('sigPlaceholder');
            if (ph) ph.style.display = 'none';
            const pos = getPos(e);
            ctx.beginPath();
            ctx.moveTo(pos.x, pos.y);
            ctx.lineTo(pos.x, pos.y);
            ctx.stroke();
            if (e.cancelable && e.type.startsWith('touch')) e.preventDefault();
        }

        function move(e) {
            if (!isDrawing) return;
            const pos = getPos(e);
            ctx.lineTo(pos.x, pos.y);
            ctx.stroke();
            if (e.cancelable && e.type.startsWith('touch')) e.preventDefault();
        }

        function stop() {
            if (isDrawing) {
                isDrawing = false;
                ctx.closePath();
            }
        }

        if (window.PointerEvent) {
            canvas.addEventListener('pointerdown', function(e) {
                try { canvas.setPointerCapture(e.pointerId); } catch(_) {}
                start(e);
            });
            canvas.addEventListener('pointermove', move);
            canvas.addEventListener('pointerup', function(e) {
                stop();
                try { canvas.releasePointerCapture(e.pointerId); } catch(_) {}
            });
            canvas.addEventListener('pointercancel', function(e) {
                stop();
                try { canvas.releasePointerCapture(e.pointerId); } catch(_) {}
            });
        } else {
            canvas.addEventListener('mousedown', start);
            canvas.addEventListener('mousemove', move);
            window.addEventListener('mouseup', stop);
            canvas.addEventListener('touchstart', start, { passive: false });
            canvas.addEventListener('touchmove', move, { passive: false });
            window.addEventListener('touchend', stop);
        }
    }

    function clearSigCanvas() {
        if (!canvas || !ctx) return;
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        hasDrawn = false;
        const ph = document.getElementById('sigPlaceholder');
        if (ph) ph.style.display = 'flex';
        const input = document.getElementById('signatureDataInput');
        if (input) input.value = '';
    }

    function pasteMySavedSig() {
        if (!mySavedSig || !canvas || !ctx) {
            alert('Anda belum memiliki tanda tangan tersimpan di profil.');
            return;
        }
        const img = new Image();
        img.onload = function() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
            hasDrawn = true;
            const ph = document.getElementById('sigPlaceholder');
            if (ph) ph.style.display = 'none';
            const input = document.getElementById('signatureDataInput');
            if (input) input.value = mySavedSig;
        };
        img.src = mySavedSig;
    }

    function syncSigDataBeforeSubmit() {
        const input = document.getElementById('signatureDataInput');
        if (canvas && hasDrawn && input && !input.value) {
            try {
                input.value = canvas.toDataURL('image/png');
            } catch(e) {
                console.warn(e);
            }
        }
    }

    document.addEventListener('DOMContentLoaded', initSigPad);
</script>
@endpush
@endsection