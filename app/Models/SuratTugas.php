<?php
// File: app/Models/SuratTugas.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class SuratTugas extends Model
{
    use HasFactory;

    protected $table = 'surat_tugas';

    protected $fillable = [
        'nomor_surat',
        'perihal',
        'dasar_hukum',
        'maksud_tujuan',
        'tempat_tujuan',
        'tanggal_mulai',
        'tanggal_selesai',
        'waktu_mulai',
        'waktu_selesai',
        'kegiatan_detail',
        'transportasi',
        'anggaran_estimasi',
        'sumber_anggaran',
        'catatan',
        'status',
        'pembuat_surat',
        'penandatangan',
        'tanggal_surat',
        'tempat_pembuatan'
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'tanggal_surat' => 'date',
        'anggaran_estimasi' => 'decimal:2'
    ];

    // Relasi many-to-many dengan Pegawai
    public function pegawai()
    {
        return $this->belongsToMany(Pegawai::class, 'surat_tugas_pegawai')
                   ->withPivot('peran', 'tugas_khusus', 'honor')
                   ->withTimestamps();
    }

    // Relasi one-to-many dengan SPPD
    public function sppd()
    {
        return $this->hasMany(Sppd::class);
    }

    // Scope untuk filter berdasarkan status
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    // Scope untuk filter berdasarkan tahun
    public function scopeTahun($query, $tahun)
    {
        return $query->whereYear('tanggal_surat', $tahun);
    }

    // Accessor untuk durasi dalam hari
    public function getDurasiHariAttribute()
    {
        return $this->tanggal_mulai->diffInDays($this->tanggal_selesai) + 1;
    }

    // Accessor untuk format nomor surat
    public function getNomorSuratFormatAttribute()
    {
        return $this->nomor_surat;
    }

    // Accessor untuk status badge class
    public function getStatusBadgeClassAttribute()
    {
        return match($this->status) {
            'draft' => 'bg-secondary',
            'disetujui' => 'bg-primary',
            'dilaksanakan' => 'bg-warning',
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
            'dilaksanakan' => 'Dilaksanakan',
            'selesai' => 'Selesai',
            'dibatalkan' => 'Dibatalkan',
            default => 'Unknown'
        };
    }

    // Method untuk generate nomor surat otomatis
    public static function generateNomorSurat()
    {
        $year = date('Y');
        $month = date('m');
        
        // Format: 001/ST/Diskominfosanpersandi-BLG/VII/2025
        $lastNumber = self::whereYear('tanggal_surat', $year)
                         ->whereMonth('tanggal_surat', $month)
                         ->count() + 1;

        $romanMonth = self::getRomanMonth($month);
        
        return sprintf('%03d/ST/Diskominfosanpersandi-BLG/%s/%s', 
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
}