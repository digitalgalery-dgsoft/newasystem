<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('candidates', function (Blueprint $table) {
            $table->string('useras')->nullable()->index();
            $table->string('note_principle')->nullable()->index();
            $table->text('archive_reason')->nullable();
            $table->text('notes')->nullable();
            $table->integer('height')->nullable();
            $table->integer('weight')->nullable();
            $table->string('mother_name')->nullable();
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_phone')->nullable();
            $table->string('emergency_contact_relation')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('bank_account_number')->nullable();
            $table->string('bank_account_holder')->nullable();
            $table->string('npwp')->nullable();
            $table->string('status_approval')->nullable()->default('Proses');
        });
    }

    public function down(): void
    {
        Schema::table('candidates', function (Blueprint $table) {
            $table->dropColumn([
                'useras', 'note_principle', 'archive_reason', 'notes',
                'height', 'weight', 'mother_name',
                'emergency_contact_name', 'emergency_contact_phone', 'emergency_contact_relation',
                'bank_name', 'bank_account_number', 'bank_account_holder', 'npwp', 'status_approval'
            ]);
        });
    }
};