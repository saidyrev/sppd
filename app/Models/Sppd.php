<?php
// File: app/Models/Sppd.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Sppd extends Model
{
    use HasFactory;

    protected $table = 'sppd';

    protected $fillable = [
        'nomor_sppd',
        'pegawai_id',
        'surat_tugas_id',
        'maksud_perjalanan',
        'tempat_berangkat',
        'tempat_tujuan',
        'tanggal_berangkat',
        'tanggal_kembali',
        'lama_perjalanan',
        'tingkat_biaya',
        'alat_angkut',
        'tempat_menginap',
        'biaya_transport',
        'biaya_penginapan',
        'uang_harian',
        'biaya_lain',
        'total_biaya',
        'sumber_anggaran',
        'kode_rekening',
        'pengikut',
        'status',
        'pengguna_anggaran',
        'tanggal_sppd',
        'tempat_pembuatan',
        'tanggal_berangkat_real',
        'tanggal_kembali_real',
        'tanggal_tiba_tujuan',
        'laporan_perjalanan',
        'realisasi_biaya',
        'keterangan'
    ];

    protected $casts = [
        'tanggal_berangkat' => 'date',
        'tanggal_kembali' => 'date',
        'tanggal_sppd' => 'date',
        'tanggal_berangkat_real' => 'date',
        'tanggal_kembali_real' => 'date',
        'tanggal_tiba_tujuan' => 'date',
        'biaya_transport' => 'decimal:2',
        'biaya_penginapan' => 'decimal:2',
        'uang_harian' => 'decimal:2',
        'biaya_lain' => 'decimal:2',
        'total_biaya' => 'decimal:2',
        'realisasi_biaya' => 'decimal:2',
        'pengikut' => 'array'
    ];

    // Relasi dengan Pegawai
    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class);
    }

    // Relasi dengan Surat Tugas
    public function suratTugas()
    {
        return $this->belongsTo(SuratTugas::class);
    }

    // Scope untuk filter berdasarkan status
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    // Scope untuk filter berdasarkan tahun
    public function scopeTahun($query, $tahun)
    {
        return $query->whereYear('tanggal_sppd', $tahun);
    }

    // Scope untuk filter berdasarkan pegawai
    public function scopePegawai($query, $pegawaiId)
    {
        return $query->where('pegawai_id', $pegawaiId);
    }

    // Accessor untuk status badge class
    public function getStatusBadgeClassAttribute()
    {
        return match($this->status) {
            'draft' => 'bg-secondary',
            'disetujui' => 'bg-primary',
            'dalam_perjalanan' => 'bg-warning',
            'selesai' => 'bg-success',
            'dibatalkan' => 'bg-danger',
            default => 'bg-secondary'
        };
    }

    // Accessor untuk status label
    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'draft' => 'Draft',
            'disetujui' => 'Disetujui',
            'dalam_perjalanan' => 'Dalam Perjalanan',
            'selesai' => 'Selesai',
            'dibatalkan' => 'Dibatalkan',
            default => 'Unknown'
        };
    }

    // Accessor untuk tingkat biaya label
    public function getTingkatBiayaLabelAttribute()
    {
        return match($this->tingkat_biaya) {
            'A' => 'Tingkat A (Eselon I)',
            'B' => 'Tingkat B (Eselon II)',
            'C' => 'Tingkat C (Eselon III)',
            'D' => 'Tingkat D (Eselon IV & Staff)',
            default => 'Tidak Ditentukan'
        };
    }

    // Accessor untuk format nomor SPPD
    public function getNomorSppdFormatAttribute()
    {
        return $this->nomor_sppd;
    }

    // Method untuk generate nomor SPPD otomatis
    public static function generateNomorSppd()
    {
        $year = date('Y');
        $month = date('m');
        
        // Format: 001/SPPD/Diskominfosanpersandi-BLG/VII/2025
        $lastNumber = self::whereYear('tanggal_sppd', $year)
                         ->whereMonth('tanggal_sppd', $month)
                         ->count() + 1;

        $romanMonth = self::getRomanMonth($month);
        
        return sprintf('%03d/SPPD/Diskominfosanpersandi-BLG/%s/%s', 
                      $lastNumber, $romanMonth, $year);
    }

    // Method untuk convert bulan ke romawi
    private static function getRomanMonth($month)
    {
        $romans = [
            1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI',
            7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'
        ];
        
        return $romans[$month];
    }

    // Method untuk kalkulasi total biaya otomatis
    public function calculateTotalBiaya()
    {
        $this->total_biaya = ($this->biaya_transport ?? 0) + 
                           ($this->biaya_penginapan ?? 0) + 
                           ($this->uang_harian ?? 0) + 
                           ($this->biaya_lain ?? 0);
        return $this->total_biaya;
    }

    // Method untuk kalkulasi lama perjalanan otomatis
    public function calculateLamaPerjalanan()
    {
        if ($this->tanggal_berangkat && $this->tanggal_kembali) {
            $this->lama_perjalanan = $this->tanggal_berangkat->diffInDays($this->tanggal_kembali) + 1;
        }
        return $this->lama_perjalanan;
    }

    // Method untuk cek apakah masih bisa diedit
    public function canBeEdited()
    {
        return in_array($this->status, ['draft']);
    }

    // Method untuk cek apakah bisa dibatalkan
    public function canBeCancelled()
    {
        return in_array($this->status, ['draft', 'disetujui']);
    }

    // Method untuk cek apakah perjalanan sudah dimulai
    public function isInProgress()
    {
        return $this->status === 'dalam_perjalanan';
    }

    // Method untuk cek apakah perjalanan sudah selesai
    public function isCompleted()
    {
        return $this->status === 'selesai';
    }

    // Method untuk format pengikut sebagai string
    public function getPengikutStringAttribute()
    {
        if (!$this->pengikut || !is_array($this->pengikut)) {
            return '-';
        }

        return collect($this->pengikut)->map(function($pengikut) {
            return $pengikut['nama'] . ' (' . $pengikut['hubungan'] . ')';
        })->join(', ');
    }
}