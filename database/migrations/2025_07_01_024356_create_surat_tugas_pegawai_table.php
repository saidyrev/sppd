<?php
// File: database/migrations/xxxx_xx_xx_create_surat_tugas_pegawai_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('surat_tugas_pegawai', function (Blueprint $table) {
            $table->id();
            $table->foreignId('surat_tugas_id')->constrained()->onDelete('cascade');
            $table->foreignId('pegawai_id')->constrained()->onDelete('cascade');
            $table->enum('peran', ['ketua', 'anggota', 'sekretaris', 'bendahara'])->default('anggota');
            $table->text('tugas_khusus')->nullable();
            $table->decimal('honor', 15, 2)->nullable();
            $table->timestamps();

            // Unique constraint agar pegawai tidak duplikat dalam satu surat tugas
            $table->unique(['surat_tugas_id', 'pegawai_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('surat_tugas_pegawai');
    }
};