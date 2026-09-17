<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('principles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('parent_company')->nullable();
            $table->string('pic_name')->nullable();
            $table->string('pic_email')->nullable();
            $table->string('pic_phone')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('principles');
    }
};