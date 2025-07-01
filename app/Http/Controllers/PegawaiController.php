<?php
namespace App\Http\Controllers;

use App\Models\Pegawai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class PegawaiController extends Controller
{
    public function index(Request $request)
    {
        $query = Pegawai::query();

        // Filter berdasarkan pencarian
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nip', 'like', "%{$search}%")
                  ->orWhere('jabatan', 'like', "%{$search}%");
            });
        }

        // Filter berdasarkan status
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        $pegawai = $query->orderBy('nama')->paginate(10);

        return view('pegawai.index', compact('pegawai'));
    }

    public function create()
    {
        return view('pegawai.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:255',
            'nip' => 'required|string|unique:pegawai,nip|size:18',
            'pangkat' => 'required|string|max:255',
            'golongan' => 'required|string|max:10',
            'jabatan' => 'required|string|max:255',
            'instansi' => 'required|string|max:255',
            'tempat_bertugas' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'telepon' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
            'status' => 'required|in:aktif,non_aktif'
        ], [
            'nip.size' => 'NIP harus terdiri dari 18 digit',
            'nip.unique' => 'NIP sudah terdaftar'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            Pegawai::create($request->all());
            
            return redirect()->route('pegawai.index')
                ->with('success', 'Data pegawai berhasil ditambahkan');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(Pegawai $pegawai)
    {
        return view('pegawai.show', compact('pegawai'));
    }

    public function edit(Pegawai $pegawai)
    {
        return view('pegawai.edit', compact('pegawai'));
    }

    public function update(Request $request, Pegawai $pegawai)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:255',
            'nip' => 'required|string|size:18|unique:pegawai,nip,' . $pegawai->id,
            'pangkat' => 'required|string|max:255',
            'golongan' => 'required|string|max:10',
            'jabatan' => 'required|string|max:255',
            'instansi' => 'required|string|max:255',
            'tempat_bertugas' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'telepon' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
            'status' => 'required|in:aktif,non_aktif'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $pegawai->update($request->all());
            
            return redirect()->route('pegawai.index')
                ->with('success', 'Data pegawai berhasil diupdate');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(Pegawai $pegawai)
    {
        try {
            // Cek apakah pegawai punya surat tugas aktif
            // $hasActiveTasks = $pegawai->suratTugas()->whereNull('tanggal_selesai')->count();
            // if ($hasActiveTasks > 0) {
            //     return redirect()->back()
            //         ->with('error', 'Tidak dapat menghapus pegawai yang masih memiliki tugas aktif');
            // }

            $pegawai->delete();
            
            return redirect()->route('pegawai.index')
                ->with('success', 'Data pegawai berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // Method untuk API atau Ajax
    public function apiIndex(Request $request)
    {
        $pegawai = Pegawai::aktif()
            ->select('id', 'nama', 'nip', 'pangkat', 'golongan', 'jabatan')
            ->get();

        return response()->json($pegawai);
    }
}