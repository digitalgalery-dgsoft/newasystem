<!-- ============================================================================== -->
<!-- 1. MODAL TAMBAH TUGAS BARU                                                     -->
<!-- ============================================================================== -->
<div id="createTaskModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm hidden" x-cloak>
    <div class="bg-white rounded-2xl border border-slate-200 shadow-2xl w-full max-w-xl max-h-[90vh] flex flex-col overflow-hidden animate-in fade-in zoom-in-95 duration-200">
        
        <!-- Header Modal -->
        <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between bg-slate-50/50">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-primary-50 text-primary flex items-center justify-center text-base font-bold">
                    <i class="fa-solid fa-list-check"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-900">Tambah Tugas Baru</h3>
                    <p class="text-[11px] text-slate-500">Buat rencana kerja atau tugas baru untuk diri sendiri / tim.</p>
                </div>
            </div>
            <button type="button" @click="closeCreateModal()" class="w-8 h-8 rounded-lg hover:bg-slate-200 text-slate-400 hover:text-slate-600 flex items-center justify-center transition-all">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>

        <!-- Form Modal -->
        <form id="createTaskForm" method="POST" action="{{ route('workplan.store') }}" enctype="multipart/form-data" class="flex-1 overflow-y-auto p-6 space-y-4">
            @csrf
            <input type="hidden" name="status" id="createTaskStatus" value="todo">

            <!-- Judul Tugas -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Judul Tugas <span class="text-rose-500">*</span>
                </label>
                <input type="text" 
                       name="title" 
                       required 
                       placeholder="Contoh: Pengecekan Absensi SADATA Tim BA Area Jakarta"
                       class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary font-semibold text-slate-800 transition-all">
            </div>

            <!-- Deskripsi Tugas -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Deskripsi / Rincian Pekerjaan
                </label>
                <textarea name="description" 
                          rows="4" 
                          placeholder="Jelaskan detail instruksi, kriteria hasil, link referensi atau catatan penting..."
                          class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary text-slate-700 transition-all"></textarea>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Prioritas Tugas -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Prioritas Tugas
                    </label>
                    <select name="priority" class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary font-semibold text-slate-800 transition-all">
                        <option value="Medium">⚡ Medium (Standar)</option>
                        <option value="High">🔥 High (Prioritas Tinggi / Mendesak)</option>
                        <option value="Low">🌱 Low (Santai / Backlog)</option>
                    </select>
                </div>

                <!-- Target Selesai (Due Date) -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Target Deadline Selesai
                    </label>
                    <input type="date" 
                           name="due_date" 
                           value="{{ date('Y-m-d') }}"
                           class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary font-semibold text-slate-800 transition-all">
                </div>
            </div>

            <!-- Assignee / Penugasan -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Ditugaskan Kepada (Assignee)
                </label>
                <select name="assignee" class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary font-semibold text-slate-800 transition-all">
                    <option value="{{ $userName }}">👤 Tugaskan ke Saya Sendiri ({{ $userName }})</option>
                    @foreach($employeesGrouped as $areaName => $emps)
                        <optgroup label="WILAYAH: {{ $areaName }}">
                            @foreach($emps as $emp)
                                @if($emp->nama_karyawan !== $userName)
                                <option value="{{ $emp->nama_karyawan }}">{{ $emp->nama_karyawan }} ({{ $emp->jabatan_db ?: 'Staf' }})</option>
                                @endif
                            @endforeach
                        </optgroup>
                    @endforeach
                </select>
            </div>

            <!-- Lampiran Berkas -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Lampiran Berkas / Screenshot (Opsional)
                </label>
                <input type="file" 
                       name="attachment" 
                       accept="image/*,.pdf,.doc,.docx,.xls,.xlsx"
                       class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-primary-50 file:text-primary hover:file:bg-primary-100 border border-slate-200 rounded-xl bg-slate-50 cursor-pointer">
                <span class="text-[10px] text-slate-400 mt-1 block">Maksimal 10MB (Gambar, PDF, Word, Excel).</span>
            </div>

            <!-- Footer Buttons -->
            <div class="pt-4 border-t border-slate-200 flex items-center justify-end gap-2.5">
                <button type="button" @click="closeCreateModal()" class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-100 text-xs font-bold transition-all">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-primary-600 to-indigo-600 hover:from-primary-700 hover:to-indigo-700 text-white text-xs font-bold transition-all shadow-md shadow-primary-600/20">
                    <i class="fa-solid fa-floppy-disk mr-1"></i> Simpan Tugas
                </button>
            </div>
        </form>

    </div>
</div>

<!-- ============================================================================== -->
<!-- 2. SLIDE-OVER DRAWER / MODAL DETAIL TUGAS & KOLABORASI                          -->
<!-- ============================================================================== -->
<div id="taskDetailDrawer" class="fixed inset-0 z-50 flex justify-end bg-slate-900/50 backdrop-blur-xs hidden" x-cloak>
    <div class="bg-white w-full max-w-2xl h-full shadow-2xl border-l border-slate-200 flex flex-col overflow-hidden animate-in slide-in-from-right duration-200">
        
        <!-- Header Drawer -->
        <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between bg-slate-50/50">
            <div class="flex items-center gap-2 flex-wrap">
                <span class="text-xs font-extrabold text-slate-400">Tugas #<span x-text="detailTaskId"></span></span>
                <span class="w-1.5 h-1.5 rounded-full bg-slate-300"></span>
                <template x-if="currentTask">
                    <span class="inline-flex items-center gap-1 text-[10px] font-black px-2 py-0.5 rounded-full uppercase"
                          :class="{
                              'bg-rose-50 text-rose-700 border border-rose-200': currentTask.priority === 'High',
                              'bg-amber-50 text-amber-700 border border-amber-200': currentTask.priority === 'Medium',
                              'bg-emerald-50 text-emerald-700 border border-emerald-200': currentTask.priority === 'Low'
                          }">
                        <i class="fa-solid" :class="{
                            'fa-fire': currentTask.priority === 'High',
                            'fa-bolt': currentTask.priority === 'Medium',
                            'fa-feather': currentTask.priority === 'Low'
                        }"></i>
                        <span x-text="currentTask.priority"></span>
                    </span>
                </template>
                <template x-if="currentTask && currentTask.is_overdue">
                    <span class="text-[9px] font-extrabold px-1.5 py-0.2 rounded bg-rose-600 text-white uppercase tracking-wider animate-pulse">
                        Terlambat
                    </span>
                </template>
            </div>
            <button type="button" @click="closeTaskDetail()" class="w-8 h-8 rounded-lg hover:bg-slate-200 text-slate-400 hover:text-slate-600 flex items-center justify-center transition-all">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>

        <!-- Quick Status & Action Bar -->
        <div class="px-6 py-3 bg-slate-100/70 border-b border-slate-200 flex items-center justify-between gap-3 text-xs flex-wrap">
            <div class="flex items-center gap-2">
                <span class="font-bold text-slate-500 text-[11px]">Pindahkan:</span>
                <div class="flex items-center gap-1.5">
                    <button type="button" 
                            @click="moveTaskStatus(detailTaskId, 'todo')" 
                            class="px-2.5 py-1 rounded-lg text-[11px] font-bold transition-all shadow-xs"
                            :class="currentTask && currentTask.status === 'todo' ? 'bg-slate-800 text-white' : 'bg-white border border-slate-200 text-slate-700 hover:bg-slate-50'">
                        To Do
                    </button>
                    <button type="button" 
                            @click="moveTaskStatus(detailTaskId, 'inprogress')" 
                            class="px-2.5 py-1 rounded-lg text-[11px] font-bold transition-all shadow-xs"
                            :class="currentTask && currentTask.status === 'inprogress' ? 'bg-amber-600 text-white' : 'bg-white border border-amber-200 text-amber-700 hover:bg-amber-50'">
                        In Progress
                    </button>
                    <button type="button" 
                            @click="moveTaskStatus(detailTaskId, 'review')" 
                            class="px-2.5 py-1 rounded-lg text-[11px] font-bold transition-all shadow-xs"
                            :class="currentTask && currentTask.status === 'review' ? 'bg-indigo-600 text-white' : 'bg-white border border-indigo-200 text-indigo-700 hover:bg-indigo-50'">
                        Review
                    </button>
                    <button type="button" 
                            @click="moveTaskStatus(detailTaskId, 'done')" 
                            class="px-2.5 py-1 rounded-lg text-[11px] font-bold transition-all shadow-xs"
                            :class="currentTask && currentTask.status === 'done' ? 'bg-emerald-600 text-white' : 'bg-white border border-emerald-200 text-emerald-700 hover:bg-emerald-50'">
                        Done
                    </button>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <template x-if="canEdit && !isEditingTask">
                    <button type="button" @click="isEditingTask = true" class="text-xs font-bold text-primary hover:text-primary-700 flex items-center gap-1">
                        <i class="fa-solid fa-pen-to-square"></i>
                        <span>Edit Tugas</span>
                    </button>
                </template>
                <template x-if="isEditingTask">
                    <button type="button" @click="isEditingTask = false" class="text-xs font-bold text-slate-500 hover:text-slate-700">
                        Batal
                    </button>
                </template>
            </div>
        </div>

        <!-- Tabs Navigation -->
        <div class="px-6 border-b border-slate-200 flex items-center gap-6 text-xs font-bold">
            <button type="button" 
                    @click="activeTab = 'detail'" 
                    class="py-3 border-b-2 transition-all flex items-center gap-2"
                    :class="activeTab === 'detail' ? 'border-primary text-primary' : 'border-transparent text-slate-400 hover:text-slate-600'">
                <i class="fa-solid fa-list-check"></i>
                <span>Detail & Checklist</span>
                <span x-show="subtasks.length > 0" class="px-1.5 py-0.2 rounded-full bg-slate-100 text-slate-600 text-[10px]" x-text="subtasks.length"></span>
            </button>
            <button type="button" 
                    @click="activeTab = 'comments'" 
                    class="py-3 border-b-2 transition-all flex items-center gap-2"
                    :class="activeTab === 'comments' ? 'border-primary text-primary' : 'border-transparent text-slate-400 hover:text-slate-600'">
                <i class="fa-regular fa-comments"></i>
                <span>Komentar & Diskusi</span>
                <span x-show="comments.length > 0" class="px-1.5 py-0.2 rounded-full bg-indigo-100 text-indigo-700 text-[10px]" x-text="comments.length"></span>
            </button>
            <button type="button" 
                    @click="activeTab = 'activities'" 
                    class="py-3 border-b-2 transition-all flex items-center gap-2"
                    :class="activeTab === 'activities' ? 'border-primary text-primary' : 'border-transparent text-slate-400 hover:text-slate-600'">
                <i class="fa-solid fa-clock-rotate-left"></i>
                <span>Jejak Riwayat</span>
                <span x-show="activities.length > 0" class="px-1.5 py-0.2 rounded-full bg-slate-100 text-slate-500 text-[10px]" x-text="activities.length"></span>
            </button>
        </div>

        <!-- Tab Body Content -->
        <div class="flex-1 overflow-y-auto p-6">
            
            <!-- Loading Skeleton -->
            <div x-show="isLoadingDetail" class="space-y-4 py-8 text-center text-slate-400">
                <i class="fa-solid fa-spinner fa-spin text-2xl text-primary mb-2"></i>
                <div class="text-xs font-semibold">Memuat rincian tugas...</div>
            </div>

            <!-- TAB 1: DETAIL & CHECKLIST -->
            <div x-show="!isLoadingDetail && activeTab === 'detail'" class="space-y-6">
                
                <!-- 1.1 FORM EDIT MODE -->
                <div x-show="isEditingTask" class="p-4 rounded-xl bg-slate-50 border border-slate-200 space-y-3.5">
                    <h5 class="text-xs font-bold text-slate-800 uppercase tracking-wider flex items-center gap-1.5">
                        <i class="fa-solid fa-pen text-primary"></i> Edit Data Tugas
                    </h5>

                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 mb-1">Judul Tugas</label>
                        <input type="text" x-model="editForm.title" class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary/20">
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 mb-1">Deskripsi Pekerjaan</label>
                        <textarea rows="3" x-model="editForm.description" class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary/20"></textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 mb-1">Prioritas</label>
                            <select x-model="editForm.priority" class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl">
                                <option value="Low">Low</option>
                                <option value="Medium">Medium</option>
                                <option value="High">High</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 mb-1">Target Deadline</label>
                            <input type="date" x-model="editForm.due_date" class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl">
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-2">
                        <button type="button" @click="isEditingTask = false" class="px-3 py-1.5 rounded-lg border border-slate-200 text-xs font-semibold">
                            Batal
                        </button>
                        <button type="button" @click="saveTaskEdit()" class="px-4 py-1.5 rounded-lg bg-primary hover:bg-primary-700 text-white text-xs font-bold shadow-xs">
                            Simpan Perubahan
                        </button>
                    </div>
                </div>

                <!-- 1.2 VIEW DETAIL MODE -->
                <div x-show="!isEditingTask" class="space-y-4">
                    <!-- Judul Tugas -->
                    <div>
                        <h3 class="text-base font-extrabold text-slate-900 leading-snug" x-text="currentTask ? currentTask.title : ''"></h3>
                        <template x-if="currentTask && currentTask.description">
                            <p class="text-xs text-slate-600 mt-2 whitespace-pre-wrap leading-relaxed bg-slate-50/80 p-3.5 rounded-xl border border-slate-100" x-text="currentTask.description"></p>
                        </template>
                    </div>

                    <!-- Meta Information Box -->
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5 p-3.5 rounded-xl bg-slate-50/90 border border-slate-200/70 text-[11px]">
                        <div>
                            <span class="text-slate-400 font-bold block text-[10px] uppercase">Assignee</span>
                            <span class="font-bold text-slate-800 truncate block mt-0.5" x-text="currentTask ? currentTask.assignee : '-'"></span>
                        </div>
                        <div>
                            <span class="text-slate-400 font-bold block text-[10px] uppercase">Delegator</span>
                            <span class="font-bold text-slate-800 truncate block mt-0.5" x-text="currentTask ? currentTask.delegator : '-'"></span>
                        </div>
                        <div>
                            <span class="text-slate-400 font-bold block text-[10px] uppercase">Target Selesai</span>
                            <span class="font-bold text-slate-800 block mt-0.5" x-text="currentTask ? currentTask.due_date_formatted : '-'"></span>
                        </div>
                        <div>
                            <span class="text-slate-400 font-bold block text-[10px] uppercase">Dibuat Pada</span>
                            <span class="font-bold text-slate-800 block mt-0.5" x-text="currentTask ? currentTask.date_input : '-'"></span>
                        </div>
                    </div>

                    <!-- Lampiran File Utama -->
                    <template x-if="currentTask && currentTask.attachment_url">
                        <div class="flex items-center gap-2 p-2.5 rounded-xl bg-indigo-50/60 border border-indigo-100 text-xs">
                            <i class="fa-solid fa-paperclip text-indigo-600"></i>
                            <span class="text-slate-600">Lampiran Berkas:</span>
                            <a :href="currentTask.attachment_url" target="_blank" class="font-bold text-primary hover:underline truncate">
                                Buka Berkas Lampiran
                            </a>
                        </div>
                    </template>
                </div>

                <!-- 1.3 SUBTASKS / CHECKLIST SECTION -->
                <div class="pt-4 border-t border-slate-200">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-2">
                            <h4 class="text-xs font-bold text-slate-900 uppercase tracking-wider flex items-center gap-2">
                                <i class="fa-solid fa-list-check text-primary"></i> Checklist Sub-Tugas
                            </h4>
                            <template x-if="subtasks.length > 0">
                                <span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-700 text-[10px] font-extrabold"
                                      x-text="`${subtasks.filter(s => s.is_completed).length}/${subtasks.length}`">
                                </span>
                            </template>
                        </div>

                        <!-- Progress indicator -->
                        <template x-if="subtasks.length > 0">
                            <span class="text-[11px] font-bold"
                                  :class="subtasks.filter(s => s.is_completed).length === subtasks.length ? 'text-emerald-600' : 'text-slate-500'"
                                  x-text="`${Math.round((subtasks.filter(s => s.is_completed).length / subtasks.length) * 100)}% Selesai`">
                            </span>
                        </template>
                    </div>

                    <!-- Progress Bar -->
                    <template x-if="subtasks.length > 0">
                        <div class="w-full h-2 bg-slate-100 rounded-full overflow-hidden mb-3.5">
                            <div class="h-full bg-emerald-500 transition-all duration-300 rounded-full"
                                 :style="`width: ${(subtasks.filter(s => s.is_completed).length / subtasks.length) * 100}%`"></div>
                        </div>
                    </template>

                    <!-- Input Tambah Subtask Cepat -->
                    <div class="flex items-center gap-2 mb-3.5">
                        <input type="text" 
                               x-model="newSubtaskText" 
                               @keydown.enter.prevent="addSubtask()"
                               placeholder="+ Ketik sub-tugas checklist lalu tekan Enter..." 
                               class="flex-1 px-3.5 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all">
                        <button type="button" 
                                @click="addSubtask()" 
                                class="px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-900 text-white text-xs font-bold transition-all shadow-xs">
                            Tambah
                        </button>
                    </div>

                    <!-- Checklist Items List -->
                    <div class="space-y-2">
                        <template x-for="s in subtasks" :key="s.id">
                            <div class="flex items-center justify-between gap-3 p-2.5 rounded-xl border transition-all group"
                                 :class="s.is_completed ? 'bg-slate-50/80 border-slate-200/60' : 'bg-white border-slate-200 hover:border-slate-300'">
                                <label class="flex items-center gap-2.5 flex-1 cursor-pointer select-none">
                                    <input type="checkbox" 
                                           :checked="s.is_completed" 
                                           @change="toggleSubtaskItem(s.id)"
                                           class="w-4 h-4 rounded text-primary focus:ring-primary/20 border-slate-300 cursor-pointer">
                                    <span class="text-xs font-medium"
                                          :class="s.is_completed ? 'line-through text-slate-400' : 'text-slate-800'"
                                          x-text="s.subtask_text">
                                    </span>
                                </label>
                                
                                <button type="button" 
                                        @click="deleteSubtaskItem(s.id)"
                                        class="opacity-0 group-hover:opacity-100 text-slate-400 hover:text-rose-600 transition-all p-1"
                                        title="Hapus checklist ini">
                                    <i class="fa-regular fa-trash-can text-xs"></i>
                                </button>
                            </div>
                        </template>

                        <template x-if="subtasks.length === 0">
                            <div class="p-4 rounded-xl border border-dashed border-slate-200 text-center text-slate-400 text-xs">
                                Belum ada checklist sub-tugas. Tambahkan langkah pengerjaan di atas.
                            </div>
                        </template>
                    </div>
                </div>

            </div>

            <!-- TAB 2: KOMENTAR & DISKUSI -->
            <div x-show="!isLoadingDetail && activeTab === 'comments'" class="space-y-5">
                
                <!-- Comments List Stream -->
                <div class="space-y-3.5 max-h-[380px] overflow-y-auto pr-1">
                    <template x-for="c in comments" :key="c.id">
                        <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/80 space-y-1.5">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <img :src="c.avatar_url" class="w-6 h-6 rounded-full object-cover bg-slate-200 ring-1 ring-slate-200">
                                    <span class="text-xs font-bold text-slate-800" x-text="c.user_comment"></span>
                                </div>
                                <span class="text-[10px] text-slate-400" x-text="c.comment_date"></span>
                            </div>
                            <p class="text-xs text-slate-700 whitespace-pre-wrap pl-8 leading-relaxed" x-text="c.comment_text"></p>
                            <template x-if="c.attachment_url">
                                <div class="pl-8 pt-1">
                                    <a :href="c.attachment_url" target="_blank" class="inline-flex items-center gap-1.5 text-[11px] text-primary hover:underline font-semibold bg-white px-2.5 py-1 rounded-lg border border-slate-200">
                                        <i class="fa-solid fa-paperclip text-slate-400"></i> Lampiran Berkas
                                    </a>
                                </div>
                            </template>
                        </div>
                    </template>
                    <div x-show="comments.length === 0" class="text-center py-10 text-slate-400 text-xs">
                        <i class="fa-regular fa-comments text-2xl text-slate-300 block mb-1"></i>
                        Belum ada komentar pada tugas ini. Berikan catatan atau feedback pertama.
                    </div>
                </div>

                <!-- Input Tambah Komentar -->
                <div class="pt-4 border-t border-slate-200 space-y-2.5">
                    <textarea x-model="newCommentText" 
                              rows="3" 
                              placeholder="Tulis tanggapan, update hasil kerja, atau ketik @NamaKaryawan..."
                              class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary text-slate-700 transition-all"></textarea>
                    
                    <div class="flex items-center justify-between gap-2">
                        <input type="file" id="commentAttachmentInput" class="text-xs text-slate-500 file:mr-2 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-[11px] file:font-semibold file:bg-slate-100 hover:file:bg-slate-200 cursor-pointer">
                        <button type="button" 
                                @click="submitComment()" 
                                :disabled="submittingComment"
                                class="px-4 py-2 rounded-xl bg-primary hover:bg-primary-700 text-white text-xs font-bold transition-all disabled:opacity-50 flex items-center gap-1.5 shadow-sm">
                            <i class="fa-solid fa-paper-plane text-[10px]"></i>
                            <span>Kirim Komentar</span>
                        </button>
                    </div>
                </div>

            </div>

            <!-- TAB 3: JEJAK AKTIVITAS -->
            <div x-show="!isLoadingDetail && activeTab === 'activities'" class="space-y-4">
                <div class="relative pl-6 space-y-4 before:absolute before:left-2 before:top-2 before:bottom-2 before:w-0.5 before:bg-slate-200">
                    <template x-for="act in activities" :key="act.id">
                        <div class="relative">
                            <div class="absolute -left-6 top-1 w-2.5 h-2.5 rounded-full bg-primary ring-4 ring-white"></div>
                            <div class="text-xs">
                                <span class="font-bold text-slate-800" x-text="act.user_actor"></span>
                                <span class="text-slate-600" x-text="act.description"></span>
                            </div>
                            <div class="text-[10px] text-slate-400 mt-0.5" x-text="act.created_at"></div>
                        </div>
                    </template>
                    <div x-show="activities.length === 0" class="text-center py-8 text-slate-400 text-xs">
                        Belum ada riwayat aktivitas tercatat.
                    </div>
                </div>
            </div>

        </div>

        <!-- Drawer Footer -->
        <div class="px-6 py-3.5 bg-slate-50 border-t border-slate-200 flex items-center justify-between gap-2">
            <div>
                <template x-if="canDelete">
                    <button type="button" 
                            @click="deleteCurrentTask()" 
                            class="text-xs font-bold text-rose-600 hover:text-rose-700 flex items-center gap-1 px-2.5 py-1.5 rounded-lg hover:bg-rose-50 transition-all">
                        <i class="fa-solid fa-trash-can"></i>
                        <span>Hapus Tugas</span>
                    </button>
                </template>
            </div>
            <button type="button" @click="closeTaskDetail()" class="px-4 py-2 rounded-xl border border-slate-200 bg-white hover:bg-slate-100 text-slate-700 text-xs font-bold transition-all shadow-xs">
                Tutup
            </button>
        </div>

    </div>
</div>

<!-- ============================================================================== -->
<!-- 3. MODAL SALIN LAPORAN WHATSAPP (COPY REPORT)                                  -->
<!-- ============================================================================== -->
<div id="copyReportModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm hidden" x-cloak>
    <div class="bg-white rounded-2xl border border-slate-200 shadow-2xl w-full max-w-lg overflow-hidden animate-in fade-in zoom-in-95 duration-200">
        <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between bg-purple-50/50">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-purple-100 text-purple-700 flex items-center justify-center text-base font-bold">
                    <i class="fa-solid fa-clipboard-list"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-900">Salin Laporan Tugas Harian</h3>
                    <p class="text-[11px] text-slate-500">Format pesan rapi yang siap dibagikan ke WhatsApp tim.</p>
                </div>
            </div>
            <button type="button" @click="closeCopyReportModal()" class="w-8 h-8 rounded-lg hover:bg-slate-200 text-slate-400 hover:text-slate-600 flex items-center justify-center transition-all">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>
        <div class="p-6 space-y-4">
            <textarea id="copyReportTextarea" rows="12" readonly class="w-full p-3 font-mono text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none text-slate-700 leading-relaxed"></textarea>
            <div class="flex items-center justify-end gap-2.5">
                <button type="button" @click="closeCopyReportModal()" class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-100 text-xs font-bold transition-all">
                    Tutup
                </button>
                <button type="button" @click="copyReportToClipboard()" class="px-5 py-2.5 rounded-xl bg-purple-600 hover:bg-purple-700 text-white text-xs font-bold transition-all shadow-md shadow-purple-600/20 flex items-center gap-2">
                    <i class="fa-solid fa-copy"></i>
                    <span>Salin ke Clipboard</span>
                </button>
            </div>
        </div>
    </div>
</div>
