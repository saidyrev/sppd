<?php
// File: app/Http/Controllers/SppdController.php

namespace App\Http\Controllers;

use App\Models\Sppd;
use App\Models\Pegawai;
use App\Models\SuratTugas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class SppdController extends Controller
{
    public function index(Request $request)
    {
        $query = Sppd::with(['pegawai', 'suratTugas']);

        // Filter berdasarkan pencarian
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nomor_sppd', 'like', "%{$search}%")
                  ->orWhere('maksud_perjalanan', 'like', "%{$search}%")
                  ->orWhere('tempat_tujuan', 'like', "%{$search}%")
                  ->orWhereHas('pegawai', function($q2) use ($search) {
                      $q2->where('nama', 'like', "%{$search}%");
                  });
            });
        }

        // Filter berdasarkan status
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        // Filter berdasarkan pegawai
        if ($request->has('pegawai_id') && $request->pegawai_id != '') {
            $query->where('pegawai_id', $request->pegawai_id);
        }

        // Filter berdasarkan tahun
        if ($request->has('tahun') && $request->tahun != '') {
            $query->whereYear('tanggal_sppd', $request->tahun);
        }

        // Filter berdasarkan bulan
        if ($request->has('bulan') && $request->bulan != '') {
            $query->whereMonth('tanggal_sppd', $request->bulan);
        }

        $sppd = $query->orderBy('tanggal_sppd', 'desc')->paginate(10);

        // Data untuk filter
        $pegawai = Pegawai::aktif()->orderBy('nama')->get();
        $years = Sppd::selectRaw('YEAR(tanggal_sppd) as year')
                    ->distinct()
                    ->orderBy('year', 'desc')
                    ->pluck('year');

        return view('sppd.index', compact('sppd', 'pegawai', 'years'));
    }

    public function create(Request $request)
    {
        $pegawai = Pegawai::aktif()->orderBy('nama')->get();
        $suratTugas = SuratTugas::where('status', '!=', 'dibatalkan')
                                ->orderBy('tanggal_surat', 'desc')
                                ->get();
        $nomorSppd = Sppd::generateNomorSppd();
        
        // Jika dari surat tugas
        $selectedSuratTugas = null;
        if ($request->has('surat_tugas_id')) {
            $selectedSuratTugas = SuratTugas::find($request->surat_tugas_id);
        }
        
        return view('sppd.create', compact('pegawai', 'suratTugas', 'nomorSppd', 'selectedSuratTugas'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nomor_sppd' => 'required|string|unique:sppd,nomor_sppd',
            'pegawai_id' => 'required|exists:pegawai,id',
            'maksud_perjalanan' => 'required|string',
            'tempat_tujuan' => 'required|string|max:255',
            'tanggal_berangkat' => 'required|date',
            'tanggal_kembali' => 'required|date|after_or_equal:tanggal_berangkat',
            'tingkat_biaya' => 'required|in:A,B,C,D',
            'tanggal_sppd' => 'required|date',
            'pengguna_anggaran' => 'required|string|max:255',
            'alat_angkut' => 'required|string|max:255',
            'tempat_berangkat' => 'required|string|max:255',
            'surat_tugas_id' => 'nullable|exists:surat_tugas,id',
            'biaya_transport' => 'nullable|numeric|min:0',
            'biaya_penginapan' => 'nullable|numeric|min:0',
            'uang_harian' => 'nullable|numeric|min:0',
            'biaya_lain' => 'nullable|numeric|min:0',
            'sumber_anggaran' => 'required|string|max:255',
            'pengikut.*.nama' => 'nullable|string|max:255',
            'pengikut.*.hubungan' => 'nullable|string|max:100'
        ], [
            'tanggal_kembali.after_or_equal' => 'Tanggal kembali tidak boleh lebih awal dari tanggal berangkat'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // Prepare pengikut data
            $pengikut = [];
            if ($request->has('pengikut')) {
                foreach ($request->pengikut as $p) {
                    if (!empty($p['nama'])) {
                        $pengikut[] = [
                            'nama' => $p['nama'],
                            'hubungan' => $p['hubungan'] ?? 'Keluarga'
                        ];
                    }
                }
            }

            // Calculate dates
            $tanggalBerangkat = Carbon::parse($request->tanggal_berangkat);
            $tanggalKembali = Carbon::parse($request->tanggal_kembali);
            $lamaPerjalanan = $tanggalBerangkat->diffInDays($tanggalKembali) + 1;

            // Calculate total biaya
            $totalBiaya = ($request->biaya_transport ?? 0) + 
                         ($request->biaya_penginapan ?? 0) + 
                         ($request->uang_harian ?? 0) + 
                         ($request->biaya_lain ?? 0);

            $sppd = Sppd::create([
                'nomor_sppd' => $request->nomor_sppd,
                'pegawai_id' => $request->pegawai_id,
                'surat_tugas_id' => $request->surat_tugas_id,
                'maksud_perjalanan' => $request->maksud_perjalanan,
                'tempat_berangkat' => $request->tempat_berangkat,
                'tempat_tujuan' => $request->tempat_tujuan,
                'tanggal_berangkat' => $request->tanggal_berangkat,
                'tanggal_kembali' => $request->tanggal_kembali,
                'lama_perjalanan' => $lamaPerjalanan,
                'tingkat_biaya' => $request->tingkat_biaya,
                'alat_angkut' => $request->alat_angkut,
                'tempat_menginap' => $request->tempat_menginap,
                'biaya_transport' => $request->biaya_transport,
                'biaya_penginapan' => $request->biaya_penginapan,
                'uang_harian' => $request->uang_harian,
                'biaya_lain' => $request->biaya_lain,
                'total_biaya' => $totalBiaya,
                'sumber_anggaran' => $request->sumber_anggaran,
                'kode_rekening' => $request->kode_rekening,
                'pengikut' => $pengikut,
                'pengguna_anggaran' => $request->pengguna_anggaran,
                'tanggal_sppd' => $request->tanggal_sppd,
                'tempat_pembuatan' => $request->tempat_pembuatan ?? 'Paringin Selatan',
                'keterangan' => $request->keterangan,
                'status' => 'draft'
            ]);

            return redirect()->route('sppd.index')
                ->with('success', 'SPPD berhasil dibuat');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(Sppd $sppd)
    {
        $sppd->load(['pegawai', 'suratTugas']);
        return view('sppd.show', compact('sppd'));
    }

    public function edit(Sppd $sppd)
    {
        if (!$sppd->canBeEdited()) {
            return redirect()->route('sppd.show', $sppd)
                ->with('error', 'SPPD ini tidak dapat diedit');
        }

        $pegawai = Pegawai::aktif()->orderBy('nama')->get();
        $suratTugas = SuratTugas::where('status', '!=', 'dibatalkan')
                                ->orderBy('tanggal_surat', 'desc')
                                ->get();
        
        return view('sppd.edit', compact('sppd', 'pegawai', 'suratTugas'));
    }

    public function update(Request $request, Sppd $sppd)
    {
        if (!$sppd->canBeEdited()) {
            return redirect()->route('sppd.show', $sppd)
                ->with('error', 'SPPD ini tidak dapat diedit');
        }

        $validator = Validator::make($request->all(), [
            'nomor_sppd' => 'required|string|unique:sppd,nomor_sppd,' . $sppd->id,
            'pegawai_id' => 'required|exists:pegawai,id',
            'maksud_perjalanan' => 'required|string',
            'tempat_tujuan' => 'required|string|max:255',
            'tanggal_berangkat' => 'required|date',
            'tanggal_kembali' => 'required|date|after_or_equal:tanggal_berangkat',
            'tingkat_biaya' => 'required|in:A,B,C,D',
            'tanggal_sppd' => 'required|date',
            'pengguna_anggaran' => 'required|string|max:255',
            'alat_angkut' => 'required|string|max:255',
            'tempat_berangkat' => 'required|string|max:255'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // Prepare pengikut data
            $pengikut = [];
            if ($request->has('pengikut')) {
                foreach ($request->pengikut as $p) {
                    if (!empty($p['nama'])) {
                        $pengikut[] = [
                            'nama' => $p['nama'],
                            'hubungan' => $p['hubungan'] ?? 'Keluarga'
                        ];
                    }
                }
            }

            // Calculate dates and total
            $tanggalBerangkat = Carbon::parse($request->tanggal_berangkat);
            $tanggalKembali = Carbon::parse($request->tanggal_kembali);
            $lamaPerjalanan = $tanggalBerangkat->diffInDays($tanggalKembali) + 1;

            $totalBiaya = ($request->biaya_transport ?? 0) + 
                         ($request->biaya_penginapan ?? 0) + 
                         ($request->uang_harian ?? 0) + 
                         ($request->biaya_lain ?? 0);

            $sppd->update([
                'nomor_sppd' => $request->nomor_sppd,
                'pegawai_id' => $request->pegawai_id,
                'surat_tugas_id' => $request->surat_tugas_id,
                'maksud_perjalanan' => $request->maksud_perjalanan,
                'tempat_berangkat' => $request->tempat_berangkat,
                'tempat_tujuan' => $request->tempat_tujuan,
                'tanggal_berangkat' => $request->tanggal_berangkat,
                'tanggal_kembali' => $request->tanggal_kembali,
                'lama_perjalanan' => $lamaPerjalanan,
                'tingkat_biaya' => $request->tingkat_biaya,
                'alat_angkut' => $request->alat_angkut,
                'tempat_menginap' => $request->tempat_menginap,
                'biaya_transport' => $request->biaya_transport,
                'biaya_penginapan' => $request->biaya_penginapan,
                'uang_harian' => $request->uang_harian,
                'biaya_lain' => $request->biaya_lain,
                'total_biaya' => $totalBiaya,
                'sumber_anggaran' => $request->sumber_anggaran,
                'kode_rekening' => $request->kode_rekening,
                'pengikut' => $pengikut,
                'pengguna_anggaran' => $request->pengguna_anggaran,
                'tanggal_sppd' => $request->tanggal_sppd,
                'tempat_pembuatan' => $request->tempat_pembuatan ?? 'Paringin Selatan',
                'keterangan' => $request->keterangan
            ]);

            return redirect()->route('sppd.show', $sppd)
                ->with('success', 'SPPD berhasil diupdate');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(Sppd $sppd)
    {
        if (!$sppd->canBeEdited()) {
            return redirect()->back()
                ->with('error', 'SPPD ini tidak dapat dihapus');
        }

        try {
            $sppd->delete();
            
            return redirect()->route('sppd.index')
                ->with('success', 'SPPD berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // Method untuk update status
    public function updateStatus(Request $request, Sppd $sppd)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:draft,disetujui,dalam_perjalanan,selesai,dibatalkan'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        try {
            $updateData = ['status' => $request->status];

            // Auto-set tanggal berdasarkan status
            if ($request->status === 'dalam_perjalanan' && !$sppd->tanggal_berangkat_real) {
                $updateData['tanggal_berangkat_real'] = now()->toDateString();
            }

            if ($request->status === 'selesai' && !$sppd->tanggal_kembali_real) {
                $updateData['tanggal_kembali_real'] = now()->toDateString();
            }

            $sppd->update($updateData);
            
            return redirect()->back()
                ->with('success', 'Status SPPD berhasil diupdate');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // Method untuk input laporan perjalanan
    public function laporan(Request $request, Sppd $sppd)
    {
        if ($request->isMethod('post')) {
            $validator = Validator::make($request->all(), [
                'laporan_perjalanan' => 'required|string',
                'realisasi_biaya' => 'nullable|numeric|min:0',
                'tanggal_berangkat_real' => 'nullable|date',
                'tanggal_kembali_real' => 'nullable|date',
                'tanggal_tiba_tujuan' => 'nullable|date'
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator);
            }

            try {
                $sppd->update([
                    'laporan_perjalanan' => $request->laporan_perjalanan,
                    'realisasi_biaya' => $request->realisasi_biaya,
                    'tanggal_berangkat_real' => $request->tanggal_berangkat_real,
                    'tanggal_kembali_real' => $request->tanggal_kembali_real,
                    'tanggal_tiba_tujuan' => $request->tanggal_tiba_tujuan,
                    'status' => 'selesai'
                ]);

                return redirect()->route('sppd.show', $sppd)
                    ->with('success', 'Laporan perjalanan berhasil disimpan');
            } catch (\Exception $e) {
                return redirect()->back()
                    ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
            }
        }

        return view('sppd.laporan', compact('sppd'));
    }

    // Method untuk generate PDF (akan dibuat nanti)
    public function generatePdf(Sppd $sppd)
    {
        // TODO: Implement PDF generation
        return redirect()->back()
            ->with('info', 'Fitur PDF akan segera tersedia');
    }
}