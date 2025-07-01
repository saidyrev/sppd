<?php
// File: app/Models/Pegawai.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pegawai extends Model
{
    use HasFactory;

    protected $table = 'pegawai';

    protected $fillable = [
        'nama',
        'nip',
        'pangkat',
        'golongan',
        'jabatan',
        'instansi',
        'tempat_bertugas',
        'email',
        'telepon',
        'alamat',
        'status'
    ];

    protected $casts = [
        'status' => 'string',
    ];

    // Scope untuk pegawai aktif
    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }

    // Accessor untuk format nama lengkap dengan pangkat
    public function getNamaLengkapAttribute()
    {
        return $this->nama . ', ' . $this->pangkat;
    }

    // Accessor untuk format NIP yang lebih rapi
    public function getNipFormatAttribute()
    {
        $nip = $this->nip;
        return substr($nip, 0, 8) . ' ' . substr($nip, 8, 6) . ' ' . substr($nip, 14, 1) . ' ' . substr($nip, 15, 3);
    }

    // Relasi many-to-many dengan Surat Tugas
    public function suratTugas()
    {
        return $this->belongsToMany(SuratTugas::class, 'surat_tugas_pegawai')
                   ->withPivot('peran', 'tugas_khusus', 'honor')
                   ->withTimestamps();
    }

    // Relasi one-to-many dengan SPPD
    public function sppd()
    {
        return $this->hasMany(Sppd::class);
    }

    // Method untuk hitung total surat tugas
    public function getTotalSuratTugasAttribute()
    {
        return $this->suratTugas()->count();
    }

    // Method untuk hitung total SPPD
    public function getTotalSppdAttribute()
    {
        return $this->sppd()->count();
    }
}