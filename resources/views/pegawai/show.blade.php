@extends('layouts.app')

@section('title', 'Detail Pegawai - ' . $pegawai->nama)
@section('page-title', 'Detail Pegawai')

@section('page-actions')
    <div class="btn-group">
        <a href="{{ route('pegawai.edit', $pegawai) }}" class="btn btn-warning">
            <i class="fas fa-edit me-2"></i>Edit
        </a>
        <button type="button" 
                class="btn btn-danger" 
                onclick="confirmDelete({{ $pegawai->id }}, '{{ $pegawai->nama }}')">
            <i class="fas fa-trash me-2"></i>Hapus
        </button>
        <a href="{{ route('pegawai.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Kembali
        </a>
    </div>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-8">
        <!-- Informasi Utama -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-user me-2"></i>Informasi Pegawai
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 text-center mb-4">
                        <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center" 
                             style="width: 120px; height: 120px;">
                            <i class="fas fa-user fa-4x text-muted"></i>
                        </div>
                        <h4 class="mt-3 mb-1">{{ $pegawai->nama }}</h4>
                        <p class="text-muted mb-2">{{ $pegawai->jabatan }}</p>
                        @if($pegawai->status == 'aktif')
                            <span class="badge bg-success fs-6">Aktif</span>
                        @else
                            <span class="badge bg-secondary fs-6">Non Aktif</span>
                        @endif
                    </div>
                    <div class="col-md-8">
                        <h6 class="text-primary border-bottom pb-2 mb-3">
                            <i class="fas fa-id-card me-2"></i>Data Identitas
                        </h6>
                        
                        <table class="table table-borderless">
                            <tr>
                                <td width="30%" class="fw-bold">NIP</td>
                                <td width="5%">:</td>
                                <td><code class="fs-6">{{ $pegawai->nip_format }}</code></td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Pangkat</td>
                                <td>:</td>
                                <td>{{ $pegawai->pangkat }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Golongan</td>
                                <td>:</td>
                                <td><span class="badge bg-info">{{ $pegawai->golongan }}</span></td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Jabatan</td>
                                <td>:</td>
                                <td>{{ $pegawai->jabatan }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Informasi Instansi -->
        <div class="card mt-4">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="fas fa-building me-2"></i>Informasi Instansi
                </h6>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <td width="30%" class="fw-bold">Instansi</td>
                        <td width="5%">:</td>
                        <td>{{ $pegawai->instansi }}</td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Tempat Bertugas</td>
                        <td>:</td>
                        <td>
                            <i class="fas fa-map-marker-alt text-danger me-1"></i>
                            {{ $pegawai->tempat_bertugas }}
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Informasi Kontak -->
        @if($pegawai->email || $pegawai->telepon || $pegawai->alamat)
        <div class="card mt-4">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="fas fa-address-book me-2"></i>Informasi Kontak
                </h6>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    @if($pegawai->email)
                    <tr>
                        <td width="30%" class="fw-bold">Email</td>
                        <td width="5%">:</td>
                        <td>
                            <a href="mailto:{{ $pegawai->email }}" class="text-decoration-none">
                                <i class="fas fa-envelope me-1"></i>{{ $pegawai->email }}
                            </a>
                        </td>
                    </tr>
                    @endif
                    @if($pegawai->telepon)
                    <tr>
                        <td class="fw-bold">Telepon</td>
                        <td>:</td>
                        <td>
                            <a href="tel:{{ $pegawai->telepon }}" class="text-decoration-none">
                                <i class="fas fa-phone me-1"></i>{{ $pegawai->telepon }}
                            </a>
                        </td>
                    </tr>
                    @endif
                    @if($pegawai->alamat)
                    <tr>
                        <td class="fw-bold">Alamat</td>
                        <td>:</td>
                        <td>
                            <i class="fas fa-home me-1"></i>{{ $pegawai->alamat }}
                        </td>
                    </tr>
                    @endif
                </table>
            </div>
        </div>
        @endif
    </div>

    <div class="col-lg-4">
        <!-- Statistik -->
        <div class="card">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="fas fa-chart-bar me-2"></i>Statistik
                </h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6">
                        <div class="border-end">
                            <h4 class="text-primary mb-1">0</h4>
                            <small class="text-muted">Surat Tugas</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <h4 class="text-success mb-1">0</h4>
                        <small class="text-muted">SPPD</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Info Sistem -->
        <div class="card mt-4">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="fas fa-info-circle me-2"></i>Informasi Sistem
                </h6>
            </div>
            <div class="card-body">
                <div class="small text-muted">
                    <div class="d-flex justify-content-between mb-2">
                        <span><i class="fas fa-calendar-plus me-1"></i>Ditambahkan:</span>
                        <span>{{ $pegawai->created_at->format('d M Y') }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span><i class="fas fa-clock me-1"></i>Jam:</span>
                        <span>{{ $pegawai->created_at->format('H:i') }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span><i class="fas fa-calendar-edit me-1"></i>Terakhir diubah:</span>
                        <span>{{ $pegawai->updated_at->format('d M Y') }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span><i class="fas fa-clock me-1"></i>Jam:</span>
                        <span>{{ $pegawai->updated_at->format('H:i') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="card mt-4">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="fas fa-lightning-bolt me-2"></i>Aksi Cepat
                </h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <button class="btn btn-outline-primary btn-sm" disabled>
                        <i class="fas fa-file-alt me-2"></i>Buat Surat Tugas
                    </button>
                    <button class="btn btn-outline-success btn-sm" disabled>
                        <i class="fas fa-plane me-2"></i>Buat SPPD
                    </button>
                    <a href="{{ route('pegawai.edit', $pegawai) }}" class="btn btn-outline-warning btn-sm">
                        <i class="fas fa-edit me-2"></i>Edit Data
                    </a>
                </div>
                <div class="text-muted small mt-2">
                    <i class="fas fa-info-circle me-1"></i>
                    Fitur surat tugas dan SPPD akan segera tersedia
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Form Delete (Hidden) -->
<form id="delete-form" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>
@endsection

@push('scripts')
<script>
function confirmDelete(id, nama) {
    if (confirm(`Apakah Anda yakin ingin menghapus data pegawai "${nama}"?\n\nData yang dihapus tidak dapat dikembalikan.`)) {
        const form = document.getElementById('delete-form');
        form.action = `/pegawai/${id}`;
        form.submit();
    }
}
</script>
@endpush