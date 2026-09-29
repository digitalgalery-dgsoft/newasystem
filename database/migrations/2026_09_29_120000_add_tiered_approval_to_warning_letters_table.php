<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('warning_letters', function (Blueprint $table) {
            if (!Schema::hasColumn('warning_letters', 'pimpinan_pembuat')) {
                $table->string('pimpinan_pembuat', 150)->nullable()->after('created_by');
            }
            if (!Schema::hasColumn('warning_letters', 'head_approved_by')) {
                $table->foreignId('head_approved_by')->nullable()->after('pimpinan_pembuat')->constrained('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('warning_letters', 'head_approved_at')) {
                $table->dateTime('head_approved_at')->nullable()->after('head_approved_by');
            }
            if (!Schema::hasColumn('warning_letters', 'head_notes')) {
                $table->text('head_notes')->nullable()->after('head_approved_at');
            }
            if (!Schema::hasColumn('warning_letters', 'file_pdf_surat')) {
                $table->string('file_pdf_surat', 255)->nullable()->after('file_ttd_karyawan');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('warning_letters', function (Blueprint $table) {
            if (Schema::hasColumn('warning_letters', 'head_approved_by')) {
                $table->dropForeign(['head_approved_by']);
                $table->dropColumn('head_approved_by');
            }
            if (Schema::hasColumn('warning_letters', 'pimpinan_pembuat')) {
                $table->dropColumn('pimpinan_pembuat');
            }
            if (Schema::hasColumn('warning_letters', 'head_approved_at')) {
                $table->dropColumn('head_approved_at');
            }
            if (Schema::hasColumn('warning_letters', 'head_notes')) {
                $table->dropColumn('head_notes');
            }
            if (Schema::hasColumn('warning_letters', 'file_pdf_surat')) {
                $table->dropColumn('file_pdf_surat');
            }
        });
    }
};
