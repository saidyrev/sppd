<?php
// File: app/Http/Controllers/SuratTugasController.php

namespace App\Http\Controllers;

use App\Models\SuratTugas;
use App\Models\Pegawai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SuratTugasController extends Controller
{
    public function index(Request $request)
    {
        $query = SuratTugas::with(['pegawai']);

        // Filter berdasarkan pencarian
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nomor_surat', 'like', "%{$search}%")
                  ->orWhere('perihal', 'like', "%{$search}%")
                  ->orWhere('tempat_tujuan', 'like', "%{$search}%");
            });
        }

        // Filter berdasarkan status
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        // Filter berdasarkan tahun
        if ($request->has('tahun') && $request->tahun != '') {
            $query->whereYear('tanggal_surat', $request->tahun);
        }

        // Filter berdasarkan bulan
        if ($request->has('bulan') && $request->bulan != '') {
            $query->whereMonth('tanggal_surat', $request->bulan);
        }

        $suratTugas = $query->orderBy('tanggal_surat', 'desc')->paginate(10);

        // Data untuk filter
        $years = SuratTugas::selectRaw('YEAR(tanggal_surat) as year')
                          ->distinct()
                          ->orderBy('year', 'desc')
                          ->pluck('year');

        return view('surat-tugas.index', compact('suratTugas', 'years'));
    }

    public function create()
    {
        $pegawai = Pegawai::aktif()->orderBy('nama')->get();
        $nomorSurat = SuratTugas::generateNomorSurat();
        
        return view('surat-tugas.create', compact('pegawai', 'nomorSurat'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nomor_surat' => 'required|string|unique:surat_tugas,nomor_surat',
            'perihal' => 'required|string|max:255',
            'maksud_tujuan' => 'required|string',
            'tempat_tujuan' => 'required|string|max:255',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'tanggal_surat' => 'required|date',
            'pegawai_ids' => 'required|array|min:1',
            'pegawai_ids.*' => 'exists:pegawai,id',
            'peran.*' => 'required|in:ketua,anggota,sekretaris,bendahara',
            'pembuat_surat' => 'required|string|max:255',
            'penandatangan' => 'required|string|max:255',
            'waktu_mulai' => 'nullable|date_format:H:i',
            'waktu_selesai' => 'nullable|date_format:H:i',
            'anggaran_estimasi' => 'nullable|numeric|min:0',
            'sumber_anggaran' => 'nullable|string|max:255',
            'transportasi' => 'nullable|string|max:255',
            'tempat_pembuatan' => 'required|string|max:255'
        ], [
            'pegawai_ids.required' => 'Minimal harus memilih 1 pegawai',
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai tidak boleh lebih awal dari tanggal mulai'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            // Buat surat tugas
            $suratTugas = SuratTugas::create([
                'nomor_surat' => $request->nomor_surat,
                'perihal' => $request->perihal,
                'dasar_hukum' => $request->dasar_hukum,
                'maksud_tujuan' => $request->maksud_tujuan,
                'tempat_tujuan' => $request->tempat_tujuan,
                'tanggal_mulai' => $request->tanggal_mulai,
                'tanggal_selesai' => $request->tanggal_selesai,
                'waktu_mulai' => $request->waktu_mulai,
                'waktu_selesai' => $request->waktu_selesai,
                'kegiatan_detail' => $request->kegiatan_detail,
                'transportasi' => $request->transportasi,
                'anggaran_estimasi' => $request->anggaran_estimasi,
                'sumber_anggaran' => $request->sumber_anggaran,
                'catatan' => $request->catatan,
                'pembuat_surat' => $request->pembuat_surat,
                'penandatangan' => $request->penandatangan,
                'tanggal_surat' => $request->tanggal_surat,
                'tempat_pembuatan' => $request->tempat_pembuatan,
                'status' => 'draft'
            ]);

            // Attach pegawai dengan peran
            foreach ($request->pegawai_ids as $index => $pegawaiId) {
                $suratTugas->pegawai()->attach($pegawaiId, [
                    'peran' => $request->peran[$index],
                    'tugas_khusus' => $request->tugas_khusus[$index] ?? null,
                    'honor' => $request->honor[$index] ?? null
                ]);
            }

            DB::commit();

            return redirect()->route('surat-tugas.index')
                ->with('success', 'Surat tugas berhasil dibuat');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(SuratTugas $suratTugas)
    {
        $suratTugas->load(['pegawai', 'sppd']);
        return view('surat-tugas.show', compact('suratTugas'));
    }

    public function edit(SuratTugas $suratTugas)
    {
        if (!$suratTugas->canBeEdited()) {
            return redirect()->route('surat-tugas.show', $suratTugas)
                ->with('error', 'Surat tugas ini tidak dapat diedit');
        }

        $pegawai = Pegawai::aktif()->orderBy('nama')->get();
        $suratTugas->load('pegawai');
        
        return view('surat-tugas.edit', compact('suratTugas', 'pegawai'));
    }

    public function update(Request $request, SuratTugas $suratTugas)
    {
        if (!$suratTugas->canBeEdited()) {
            return redirect()->route('surat-tugas.show', $suratTugas)
                ->with('error', 'Surat tugas ini tidak dapat diedit');
        }

        $validator = Validator::make($request->all(), [
            'nomor_surat' => 'required|string|unique:surat_tugas,nomor_surat,' . $suratTugas->id,
            'perihal' => 'required|string|max:255',
            'maksud_tujuan' => 'required|string',
            'tempat_tujuan' => 'required|string|max:255',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'tanggal_surat' => 'required|date',
            'pegawai_ids' => 'required|array|min:1',
            'pegawai_ids.*' => 'exists:pegawai,id',
            'peran.*' => 'required|in:ketua,anggota,sekretaris,bendahara',
            'pembuat_surat' => 'required|string|max:255',
            'penandatangan' => 'required|string|max:255'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            // Update surat tugas
            $suratTugas->update($request->only([
                'nomor_surat', 'perihal', 'dasar_hukum', 'maksud_tujuan',
                'tempat_tujuan', 'tanggal_mulai', 'tanggal_selesai',
                'waktu_mulai', 'waktu_selesai', 'kegiatan_detail',
                'transportasi', 'anggaran_estimasi', 'sumber_anggaran',
                'catatan', 'pembuat_surat', 'penandatangan',
                'tanggal_surat', 'tempat_pembuatan'
            ]));

            // Sync pegawai dengan peran (hapus yang lama, tambah yang baru)
            $suratTugas->pegawai()->detach();
            
            foreach ($request->pegawai_ids as $index => $pegawaiId) {
                $suratTugas->pegawai()->attach($pegawaiId, [
                    'peran' => $request->peran[$index],
                    'tugas_khusus' => $request->tugas_khusus[$index] ?? null,
                    'honor' => $request->honor[$index] ?? null
                ]);
            }

            DB::commit();

            return redirect()->route('surat-tugas.show', $suratTugas)
                ->with('success', 'Surat tugas berhasil diupdate');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(SuratTugas $suratTugas)
    {
        if (!$suratTugas->canBeEdited()) {
            return redirect()->back()
                ->with('error', 'Surat tugas ini tidak dapat dihapus');
        }

        try {
            $suratTugas->delete();
            
            return redirect()->route('surat-tugas.index')
                ->with('success', 'Surat tugas berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // Method untuk update status
    public function updateStatus(Request $request, SuratTugas $suratTugas)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:draft,disetujui,dilaksanakan,selesai,dibatalkan'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        try {
            $suratTugas->update(['status' => $request->status]);
            
            return redirect()->back()
                ->with('success', 'Status surat tugas berhasil diupdate');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // Method untuk generate PDF (akan dibuat nanti)
    public function generatePdf(SuratTugas $suratTugas)
    {
        // TODO: Implement PDF generation
        return redirect()->back()
            ->with('info', 'Fitur PDF akan segera tersedia');
    }
}