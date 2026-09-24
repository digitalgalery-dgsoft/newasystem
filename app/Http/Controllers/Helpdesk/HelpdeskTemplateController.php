<?php

namespace App\Http\Controllers\Helpdesk;

use App\Http\Controllers\Controller;
use App\Models\HelpdeskDivision;
use App\Models\HelpdeskTicketTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class HelpdeskTemplateController extends Controller
{
    /**
     * Pastikan hanya Administrator yang dapat mengelola Master Template Laporan
     */
    protected function authorizeAdmin()
    {
        $user = Auth::user();
        if (!$user || !$user->isHelpdeskAdmin()) {
            abort(403, 'Akses ditolak! Menu Master Template Laporan hanya dapat dikelola oleh Administrator.');
        }
    }

    /**
     * Daftar Master Template Laporan Kendala
     */
    public function index(Request $request)
    {
        $this->authorizeAdmin();

        $query = HelpdeskTicketTemplate::with('division');

        if ($search = $request->query('search')) {
            $search = trim($search);
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('subject', 'like', "%{$search}%")
                    ->orWhere('message', 'like', "%{$search}%");
            });
        }

        if ($divisionId = $request->query('division_id')) {
            if ($divisionId === 'general') {
                $query->whereNull('division_id');
            } else {
                $query->where('division_id', $divisionId);
            }
        }

        $templates = $query->orderBy('division_id', 'asc')
            ->orderBy('order_num', 'asc')
            ->orderBy('title', 'asc')
            ->get();

        $divisions = HelpdeskDivision::where('is_active', true)->orderBy('name', 'asc')->get();

        return view('helpdesk.templates.index', compact('templates', 'divisions'));
    }

    /**
     * Simpan Template Baru
     */
    public function store(Request $request)
    {
        $this->authorizeAdmin();

        $request->validate([
            'title' => 'required|string|max:255',
            'subject' => 'nullable|string|max:255',
            'message' => 'required|string',
            'division_id' => 'nullable|exists:helpdesk_divisions,id',
            'attachment' => 'nullable|file|max:10240|mimes:pdf,doc,docx,xls,xlsx,zip,png,jpg,jpeg',
            'order_num' => 'nullable|integer',
        ], [
            'title.required' => 'Nama / jenis template wajib diisi.',
            'message.required' => 'Format isi deskripsi / laporan kendala wajib diisi.',
            'attachment.max' => 'Ukuran berkas format lampiran maksimal 10MB.',
        ]);

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('helpdesk_templates', 'public');
        }

        HelpdeskTicketTemplate::create([
            'division_id' => $request->input('division_id') ?: null,
            'title' => trim($request->input('title')),
            'subject' => trim($request->input('subject')),
            'message' => trim($request->input('message')),
            'attachment' => $attachmentPath,
            'is_active' => true,
            'order_num' => (int)$request->input('order_num', 0),
        ]);

        return redirect()->route('helpdesk.templates.index')
            ->with('success', 'Template laporan kendala baru berhasil ditambahkan.');
    }

    /**
     * Perbarui Template
     */
    public function update(Request $request, $id)
    {
        $this->authorizeAdmin();

        $template = HelpdeskTicketTemplate::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'subject' => 'nullable|string|max:255',
            'message' => 'required|string',
            'division_id' => 'nullable|exists:helpdesk_divisions,id',
            'attachment' => 'nullable|file|max:10240|mimes:pdf,doc,docx,xls,xlsx,zip,png,jpg,jpeg',
            'order_num' => 'nullable|integer',
        ], [
            'title.required' => 'Nama / jenis template wajib diisi.',
            'message.required' => 'Format isi deskripsi / laporan kendala wajib diisi.',
            'attachment.max' => 'Ukuran berkas format lampiran maksimal 10MB.',
        ]);

        $attachmentPath = $template->attachment;
        if ($request->hasFile('attachment')) {
            if ($template->attachment && Storage::disk('public')->exists($template->attachment)) {
                Storage::disk('public')->delete($template->attachment);
            }
            $attachmentPath = $request->file('attachment')->store('helpdesk_templates', 'public');
        }

        $template->update([
            'division_id' => $request->input('division_id') ?: null,
            'title' => trim($request->input('title')),
            'subject' => trim($request->input('subject')),
            'message' => trim($request->input('message')),
            'attachment' => $attachmentPath,
            'is_active' => $request->has('is_active') ? (bool)$request->input('is_active') : $template->is_active,
            'order_num' => (int)$request->input('order_num', $template->order_num),
        ]);

        return redirect()->route('helpdesk.templates.index')
            ->with('success', "Template '{$template->title}' berhasil diperbarui.");
    }

    /**
     * Hapus Template
     */
    public function destroy($id)
    {
        $this->authorizeAdmin();

        $template = HelpdeskTicketTemplate::findOrFail($id);

        if ($template->attachment && Storage::disk('public')->exists($template->attachment)) {
            Storage::disk('public')->delete($template->attachment);
        }

        $title = $template->title;
        $template->delete();

        return redirect()->route('helpdesk.templates.index')
            ->with('success', "Template '{$title}' berhasil dihapus.");
    }

    /**
     * Hapus Berkas Lampiran Format Template Saja
     */
    public function removeAttachment($id)
    {
        $this->authorizeAdmin();

        $template = HelpdeskTicketTemplate::findOrFail($id);

        if ($template->attachment && Storage::disk('public')->exists($template->attachment)) {
            Storage::disk('public')->delete($template->attachment);
        }

        $template->update(['attachment' => null]);

        return redirect()->back()
            ->with('success', "Berkas lampiran format pada template '{$template->title}' berhasil dihapus.");
    }
}
