<?php
// File: database/migrations/xxxx_xx_xx_create_surat_tugas_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('surat_tugas', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_surat')->unique();
            $table->string('perihal');
            $table->text('dasar_hukum')->nullable();
            $table->text('maksud_tujuan');
            $table->string('tempat_tujuan');
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->time('waktu_mulai')->nullable();
            $table->time('waktu_selesai')->nullable();
            $table->text('kegiatan_detail')->nullable();
            $table->string('transportasi')->nullable();
            $table->decimal('anggaran_estimasi', 15, 2)->nullable();
            $table->string('sumber_anggaran')->nullable();
            $table->text('catatan')->nullable();
            $table->enum('status', ['draft', 'disetujui', 'dilaksanakan', 'selesai', 'dibatalkan'])->default('draft');
            $table->string('pembuat_surat');
            $table->string('penandatangan');
            $table->date('tanggal_surat');
            $table->string('tempat_pembuatan')->default('Paringin Selatan');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('surat_tugas');
    }
};