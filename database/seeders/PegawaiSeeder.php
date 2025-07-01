<?php
// File: database/seeders/PegawaiSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pegawai;

class PegawaiSeeder extends Seeder
{
    public function run()
    {
        $pegawai_data = [
            [
                'nama' => 'Eddy Fahriannor, S.Sos',
                'nip' => '198112062010011017',
                'pangkat' => 'Penata Tk.I',
                'golongan' => 'III/d',
                'jabatan' => 'JF Sandiman Ahli Muda',
                'instansi' => 'Dinas Komunikasi, Informatika, Statistik dan Persandian',
                'tempat_bertugas' => 'Paringin Selatan',
                'email' => 'eddy.fahriannor@balangankab.go.id',
                'telepon' => '081234567001',
                'alamat' => 'Jl. Ahmad Yani No. 1, Paringin Selatan',
                'status' => 'aktif'
            ],
            [
                'nama' => 'Muhammad Saidi Yupini, S.Kom',
                'nip' => '200004222025041002',
                'pangkat' => 'Penata Muda',
                'golongan' => 'III/a',
                'jabatan' => 'JF Sandiman Ahli Pertama',
                'instansi' => 'Dinas Komunikasi, Informatika, Statistik dan Persandian',
                'tempat_bertugas' => 'Paringin Selatan',
                'email' => 'saidi.yupini@balangankab.go.id',
                'telepon' => '081234567002',
                'alamat' => 'Jl. Ahmad Yani No. 2, Paringin Selatan',
                'status' => 'aktif'
            ],
            [
                'nama' => 'Barkatullah Asfi, S.Kom',
                'nip' => '199807102024211001',
                'pangkat' => 'Pengatur Muda Tk.I',
                'golongan' => 'II/b',
                'jabatan' => 'Ahli Pertama Pranata Komputer',
                'instansi' => 'Dinas Komunikasi, Informatika, Statistik dan Persandian',
                'tempat_bertugas' => 'Paringin Selatan',
                'email' => 'barkatullah.asfi@balangankab.go.id',
                'telepon' => '081234567003',
                'alamat' => 'Jl. Ahmad Yani No. 3, Paringin Selatan',
                'status' => 'aktif'
            ],
            [
                'nama' => 'Candra Saputra Ganie, S.Sos, MM',
                'nip' => '198201232006041006',
                'pangkat' => 'Pembina',
                'golongan' => 'IV/a',
                'jabatan' => 'Kepala Bidang Statistik dan Persandian',
                'instansi' => 'Dinas Komunikasi, Informatika, Statistik dan Persandian',
                'tempat_bertugas' => 'Paringin Selatan',
                'email' => 'candra.ganie@balangankab.go.id',
                'telepon' => '081234567004',
                'alamat' => 'Jl. Ahmad Yani No. 4, Paringin Selatan',
                'status' => 'aktif'
            ],
            [
                'nama' => 'Muhammad Nor, S.Sos, MM',
                'nip' => '197108231993031005',
                'pangkat' => 'Pembina Tk.I',
                'golongan' => 'IV/b',
                'jabatan' => 'Kepala Dinas',
                'instansi' => 'Dinas Komunikasi, Informatika, Statistik dan Persandian',
                'tempat_bertugas' => 'Paringin Selatan',
                'email' => 'muhammad.nor@balangankab.go.id',
                'telepon' => '081234567005',
                'alamat' => 'Jl. Ahmad Yani No. 5, Paringin Selatan',
                'status' => 'aktif'
            ]
        ];

        foreach ($pegawai_data as $data) {
            Pegawai::create($data);
        }
    }
}