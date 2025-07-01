<?php
// File: database/migrations/xxxx_xx_xx_create_sppd_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('sppd', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_sppd')->unique();
            $table->foreignId('pegawai_id')->constrained()->onDelete('cascade');
            $table->foreignId('surat_tugas_id')->nullable()->constrained()->onDelete('set null');
            
            // Data Perjalanan
            $table->string('maksud_perjalanan');
            $table->string('tempat_berangkat')->default('Paringin Selatan');
            $table->string('tempat_tujuan');
            $table->date('tanggal_berangkat');
            $table->date('tanggal_kembali');
            $table->integer('lama_perjalanan'); // dalam hari
            
            // Tingkat Biaya dan Transport
            $table->enum('tingkat_biaya', ['A', 'B', 'C', 'D'])->default('C');
            $table->string('alat_angkut')->default('Angkutan Umum');
            $table->string('tempat_menginap')->nullable();
            
            // Anggaran
            $table->decimal('biaya_transport', 15, 2)->nullable();
            $table->decimal('biaya_penginapan', 15, 2)->nullable();
            $table->decimal('uang_harian', 15, 2)->nullable();
            $table->decimal('biaya_lain', 15, 2)->nullable();
            $table->decimal('total_biaya', 15, 2)->nullable();
            $table->string('sumber_anggaran')->default('APBD');
            $table->string('kode_rekening')->nullable();
            
            // Pengikut (jika ada)
            $table->json('pengikut')->nullable(); // Store sebagai JSON
            
            // Approval dan Status
            $table->enum('status', ['draft', 'disetujui', 'dalam_perjalanan', 'selesai', 'dibatalkan'])->default('draft');
            $table->string('pengguna_anggaran')->default('Kepala Dinas');
            $table->date('tanggal_sppd');
            $table->string('tempat_pembuatan')->default('Paringin Selatan');
            
            // Realisasi Perjalanan
            $table->date('tanggal_berangkat_real')->nullable();
            $table->date('tanggal_kembali_real')->nullable();
            $table->date('tanggal_tiba_tujuan')->nullable();
            $table->text('laporan_perjalanan')->nullable();
            $table->decimal('realisasi_biaya', 15, 2)->nullable();
            
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('sppd');
    }
};