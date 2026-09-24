@extends('layouts.app')

@section('title', 'Buat Tiket Kendala Baru')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- BREADCRUMB & TITLE -->
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-slate-500 mb-1">
                <a href="{{ route('helpdesk.index') }}" class="hover:text-primary transition-colors">Helpdesk</a>
                <span>/</span>
                <a href="{{ route('helpdesk.tickets.index') }}" class="hover:text-primary transition-colors">Tiket</a>
                <span>/</span>
                <span class="text-slate-800">Buat Tiket Baru</span>
            </div>
            <h1 class="text-2xl font-black text-slate-800 tracking-tight">Ajukan Tiket Kendala & Permohonan</h1>
            <p class="text-xs text-slate-500 mt-0.5">Pilih divisi tujuan dan uraikan kendala yang Anda alami secara detail.</p>
        </div>
        <a href="{{ route('helpdesk.tickets.index') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 text-xs font-bold transition-all shadow-xs">
            <i class="fa-solid fa-arrow-left text-slate-400"></i>
            <span>Kembali ke Daftar</span>
        </a>
    </div>

    <!-- FORM CARD -->
    <div class="bg-white rounded-3xl border border-slate-200/90 shadow-sm p-6 md:p-8">
        <form method="POST" action="{{ route('helpdesk.tickets.store') }}" enctype="multipart/form-data" class="space-y-6" 
              x-data="{ 
                  selectedDivision: '{{ old('division_id') }}',
                  subjectValue: '{{ addslashes(old('subject', '')) }}',
                  descValue: `{{ addslashes(old('description', '')) }}`,
                  selectedTemplateId: '',
                  activeAttachmentUrl: '',
                  activeAttachmentName: '',
                  applyTemplate(e) {
                      const select = e.target;
                      const opt = select.options[select.selectedIndex];
                      if (!opt || !opt.value) {
                          this.selectedTemplateId = '';
                          this.activeAttachmentUrl = '';
                          this.activeAttachmentName = '';
                          return;
                      }
                      this.selectedTemplateId = opt.value;
                      if (opt.dataset.subject) this.subjectValue = opt.dataset.subject;
                      if (opt.dataset.desc) this.descValue = opt.dataset.desc;
                      if (opt.dataset.division) this.selectedDivision = opt.dataset.division;
                      if (opt.dataset.attachmentUrl) {
                          this.activeAttachmentUrl = opt.dataset.attachmentUrl;
                          this.activeAttachmentName = opt.dataset.attachmentName;
                      } else {
                          this.activeAttachmentUrl = '';
                          this.activeAttachmentName = '';
                      }
                  },
                  resetTemplate() {
                      this.selectedTemplateId = '';
                      this.subjectValue = '';
                      this.descValue = '';
                      this.activeAttachmentUrl = '';
                      this.activeAttachmentName = '';
                      const sel = document.getElementById('template_selector');
                      if (sel) sel.value = '';
                  }
              }">
            @csrf

            <!-- ⚡ JALAN PINTAS (TEMPLATE MASALAH & FORMAT LAPORAN) -->
            @if(!empty($templates) && $templates->isNotEmpty())
            <div class="p-5 rounded-2xl bg-gradient-to-br from-blue-50/90 via-indigo-50/60 to-sky-50/80 border border-blue-200/90 shadow-2xs space-y-3">
                <div class="flex items-center justify-between">
                    <label class="block text-xs font-bold text-blue-900 uppercase tracking-wider flex items-center gap-2">
                        <i class="fa-solid fa-bolt text-amber-500 text-sm"></i>
                        <span>Jalan Pintas (Template Masalah & Format Laporan)</span>
                    </label>
                    <template x-if="selectedTemplateId">
                        <button type="button" @click="resetTemplate()" class="text-[11px] text-rose-600 hover:underline font-bold flex items-center gap-1">
                            <i class="fa-solid fa-rotate-left text-[10px]"></i>
                            <span>Kosongkan Template</span>
                        </button>
                    </template>
                </div>

                <div class="relative">
                    <select id="template_selector" 
                            @change="applyTemplate($event)"
                            class="w-full px-4 py-2.5 bg-white border border-blue-200 rounded-xl text-xs text-slate-800 font-semibold focus:outline-none focus:border-primary shadow-xs">
                        <option value="">-- Pilih Format / Jenis Kendala (Otomatis Mengisi Form) --</option>
                        @foreach($templatesGrouped as $groupName => $tplList)
                        <optgroup label="📂 {{ strtoupper($groupName) }}">
                            @foreach($tplList as $tpl)
                            <option value="{{ $tpl->id }}"
                                    data-subject="{{ $tpl->subject }}"
                                    data-desc="{{ $tpl->message }}"
                                    data-division="{{ $tpl->division_id ?? '' }}"
                                    data-attachment-url="{{ $tpl->attachment_url }}"
                                    data-attachment-name="{{ $tpl->attachment_filename }}">
                                {{ $tpl->title }} {{ $tpl->division ? '(' . $tpl->division->name . ')' : '(Umum)' }}
                            </option>
                            @endforeach
                        </optgroup>
                        @endforeach
                    </select>
                </div>
                <p class="text-[11px] text-blue-700/80">
                    💡 Pilih masalah Anda di atas untuk mengisi divisi, judul, dan format deskripsi kendala secara otomatis seperti di sistem lama.
                </p>

                <!-- DOKUMEN FORMAT LAMPIRAN TEMPLATE (JIKA ADA) -->
                <div x-show="activeAttachmentUrl" x-cloak 
                     class="p-3.5 bg-white rounded-xl border border-blue-200 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center font-bold flex-shrink-0">
                            <i class="fa-solid fa-file-excel text-sm"></i>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-slate-800">Template ini menyertakan form dokumen format standar</p>
                            <p class="text-[11px] text-slate-500">Silakan unduh dokumen format di samping, lengkapi, lalu unggah kembali pada kolom lampiran di bawah.</p>
                        </div>
                    </div>
                    <a :href="activeAttachmentUrl" download 
                       class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-xs transition-all flex-shrink-0">
                        <i class="fa-solid fa-download"></i>
                        <span>Unduh Format (<span x-text="activeAttachmentName"></span>)</span>
                    </a>
                </div>
            </div>
            @endif

            <!-- 1. PILIH DIVISI TUJUAN -->
            <div>
                <label class="block text-xs font-bold text-slate-800 uppercase tracking-wider mb-2">
                    1. Pilih Divisi Tujuan <span class="text-rose-500">*</span>
                </label>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                    @foreach($divisions as $div)
                    <label class="relative flex flex-col p-4 rounded-2xl border-2 cursor-pointer transition-all hover:border-primary/60"
                           :class="selectedDivision == '{{ $div->id }}' ? 'border-primary bg-primary-50/30 ring-2 ring-primary/20' : 'border-slate-200 bg-slate-50/30'">
                        <input type="radio" name="division_id" value="{{ $div->id }}" x-model="selectedDivision" required class="sr-only">
                        <div class="flex items-center justify-between mb-2">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center text-lg {{ $div->color_badge }}">
                                <i class="{{ $div->icon }}"></i>
                            </div>
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-slate-200/70 text-slate-700">
                                SLA {{ $div->sla_hours }} Jam
                            </span>
                        </div>
                        <h4 class="text-xs font-bold text-slate-800 leading-snug">{{ $div->name }}</h4>
                        <p class="text-[11px] text-slate-500 mt-1 line-clamp-2 leading-relaxed">{{ $div->description }}</p>
                    </label>
                    @endforeach
                </div>
                @error('division_id')
                <p class="text-xs text-rose-500 mt-1.5 font-semibold">{{ $message }}</p>
                @enderror
            </div>

            <!-- 2. JUDUL KENDALA & PRIORITAS -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-slate-800 uppercase tracking-wider mb-1.5">
                        2. Judul Kendala / Permohonan <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="subject" x-model="subjectValue" required
                           placeholder="Contoh: Gangguan Login Aplikasi / Permohonan Akses Database"
                           class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:border-primary focus:bg-white transition-all">
                    @error('subject')
                    <p class="text-xs text-rose-500 mt-1 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-800 uppercase tracking-wider mb-1.5">
                        Tingkat Urgensi <span class="text-rose-500">*</span>
                    </label>
                    <select name="priority" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700 font-semibold focus:outline-none focus:border-primary focus:bg-white">
                        <option value="Medium" {{ old('priority', 'Medium') === 'Medium' ? 'selected' : '' }}>Medium (Standar)</option>
                        <option value="Low" {{ old('priority') === 'Low' ? 'selected' : '' }}>Low (Rendah / Tidak Mendesak)</option>
                        <option value="High" {{ old('priority') === 'High' ? 'selected' : '' }}>High (Tinggi / Membutuhkan Perhatian)</option>
                        <option value="Urgent" {{ old('priority') === 'Urgent' ? 'selected' : '' }}>Urgent (Darurat / Sistem Berhenti)</option>
                    </select>
                </div>
            </div>

            <!-- 3. DESKRIPSI RINCI -->
            <div>
                <label class="block text-xs font-bold text-slate-800 uppercase tracking-wider mb-1.5">
                    3. Deskripsi Rinci Kendala / Kebutuhan <span class="text-rose-500">*</span>
                </label>
                <textarea name="description" x-model="descValue" rows="6" required
                          placeholder="Jelaskan secara spesifik langkah-langkah yang dilakukan sebelum error terjadi, data karyawan terkait, kode error, atau rincian permohonan fasilitas..."
                          class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:border-primary focus:bg-white transition-all leading-relaxed font-mono"></textarea>
                @error('description')
                <p class="text-xs text-rose-500 mt-1 font-semibold">{{ $message }}</p>
                @enderror
            </div>

            <!-- 4. BERKAS LAMPIRAN / TANGKAPAN LAYAR -->
            <div>
                <label class="block text-xs font-bold text-slate-800 uppercase tracking-wider mb-1.5">
                    4. Berkas Pendukung / Tangkapan Layar (Opsional)
                </label>
                <div class="border-2 border-dashed border-slate-200 rounded-2xl p-6 text-center bg-slate-50/40 hover:bg-slate-50 transition-colors cursor-pointer relative"
                     onclick="document.getElementById('attachment_input').click()">
                    <input type="file" name="attachment" id="attachment_input" class="hidden"
                           accept="image/*,.pdf,.doc,.docx,.xls,.xlsx,.zip"
                           onchange="document.getElementById('file_preview_name').innerText = this.files[0] ? this.files[0].name : ''">
                    <div class="w-12 h-12 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center mx-auto mb-2 text-lg">
                        <i class="fa-solid fa-cloud-arrow-up"></i>
                    </div>
                    <p class="text-xs font-bold text-slate-700">Klik untuk memilih file atau screenshot kendala</p>
                    <p class="text-[11px] text-slate-400 mt-0.5">Format didukung: JPG, PNG, PDF, Excel, Word (Maks. 10MB)</p>
                    <p id="file_preview_name" class="text-xs font-bold text-primary mt-2"></p>
                </div>
            </div>

            <!-- NOTA INFORMASI OTOMASI WORK PLAN -->
            <div class="p-4 rounded-2xl bg-indigo-50/70 border border-indigo-100 flex items-start gap-3 text-xs text-indigo-900">
                <i class="fa-solid fa-circle-info text-indigo-600 text-sm mt-0.5 flex-shrink-0"></i>
                <div class="space-y-1">
                    <strong class="font-bold">Otomasi Integrasi Work Plan:</strong>
                    <p class="text-indigo-800 leading-relaxed text-[11px]">
                        Saat petugas/agen dari divisi tujuan merespon tiket Anda, tiket ini akan otomatis tercatat ke dalam sistem <strong>Work Plan (Step Progress)</strong> petugas tersebut, sehingga progres penyelesaian dapat dipantau secara transparan dan akuntabel.
                    </p>
                </div>
            </div>

            <!-- TOMBOL AKSI -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <a href="{{ route('helpdesk.tickets.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-100 font-bold text-xs transition-all">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-primary hover:bg-primary-600 text-white font-bold text-xs shadow-md shadow-primary/20 transition-all flex items-center gap-2">
                    <i class="fa-solid fa-paper-plane"></i>
                    <span>Kirim Tiket Sekarang</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
