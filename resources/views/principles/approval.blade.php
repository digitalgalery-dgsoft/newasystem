<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Persetujuan Kandidat - {{ $approval->principle->name }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="p-4 md:p-8">
    <div class="max-w-2xl mx-auto space-y-6">
        <div class="text-center">
            <h1 class="text-2xl font-black text-slate-900">Konfirmasi Calon Kandidat</h1>
            <p class="text-xs text-slate-500 mt-1">Prinsiple: {{ $approval->principle->name }}</p>
        </div>

        <div class="p-6 rounded-3xl bg-white border border-slate-200 shadow-sm space-y-4">
            <div class="border-b pb-4">
                <span class="text-xs text-slate-400 uppercase font-bold">Nama Kandidat</span>
                <h2 class="text-xl font-bold text-slate-800">{{ $approval->candidate->full_name }}</h2>
                <p class="text-xs text-slate-500">Posisi: {{ $approval->candidate->applied_job }} ({{ $approval->candidate->area }})</p>
            </div>

            <form method="POST" action="{{ route('approval.process', $approval->approval_token) }}" class="space-y-4 pt-2">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-2">Keputusan Approval</label>
                    <select name="decision" required class="w-full px-4 py-2.5 rounded-xl border text-sm font-semibold">
                        <option value="approved">✅ SETUJU / LOLOS (Approve)</option>
                        <option value="rejected">❌ TOLAK (Reject)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-2">Catatan Prinsiple (Opsional)</label>
                    <textarea name="notes" rows="3" class="w-full px-4 py-2 rounded-xl border text-sm" placeholder="Catatan atau syarat penempatan..."></textarea>
                </div>

                <button type="submit" class="w-full py-3 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm shadow-md transition-all">
                    Kirim Konfirmasi Persetujuan
                </button>
            </form>
        </div>
    </div>
</body>
</html>