@extends('layouts.app')

@section('title', 'Data Pegawai')
@section('page-title', 'Data Pegawai')

@section('page-actions')
    <a href="{{ route('pegawai.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>Tambah Pegawai
    </a>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">
            <i class="fas fa-users me-2"></i>Daftar Pegawai
        </h5>
    </div>
    <div class="card-body">
        <!-- Filter dan Pencarian -->
        <form method="GET" action="{{ route('pegawai.index') }}" class="mb-4">
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" 
                               class="form-control" 
                               name="search" 
                               value="{{ request('search') }}" 
                               placeholder="Cari nama, NIP, atau jabatan...">
                    </div>
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="non_aktif" {{ request('status') == 'non_aktif' ? 'selected' : '' }}>Non Aktif</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <div class="d-grid gap-2 d-md-flex">
                        <button type="submit" class="btn btn-outline-primary">
                            <i class="fas fa-filter me-1"></i>Filter
                        </button>
                        <a href="{{ route('pegawai.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-1"></i>Reset
                        </a>
                    </div>
                </div>
            </div>
        </form>

        <!-- Tabel Data Pegawai -->
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th width="5%">No</th>
                        <th>Nama</th>
                        <th>NIP</th>
                        <th>Pangkat/Gol</th>
                        <th>Jabatan</th>
                        <th>Status</th>
                        <th width="15%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pegawai as $key => $p)
                        <tr>
                            <td>{{ $pegawai->firstItem() + $key }}</td>
                            <td>
                                <strong>{{ $p->nama }}</strong>
                                @if($p->email)
                                    <br><small class="text-muted">{{ $p->email }}</small>
                                @endif
                            </td>
                            <td>
                                <code>{{ $p->nip_format }}</code>
                            </td>
                            <td>
                                {{ $p->pangkat }}
                                <br><small class="text-muted">{{ $p->golongan }}</small>
                            </td>
                            <td>{{ $p->jabatan }}</td>
                            <td>
                                @if($p->status == 'aktif')
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-secondary">Non Aktif</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="{{ route('pegawai.show', $p) }}" 
                                       class="btn btn-outline-info" 
                                       title="Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('pegawai.edit', $p) }}" 
                                       class="btn btn-outline-warning" 
                                       title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" 
                                            class="btn btn-outline-danger" 
                                            title="Hapus"
                                            onclick="confirmDelete({{ $p->id }}, '{{ $p->nama }}')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">Tidak ada data pegawai</h5>
                                <p class="text-muted">
                                    @if(request('search') || request('status'))
                                        Tidak ditemukan pegawai sesuai kriteria pencarian
                                    @else
                                        Belum ada data pegawai yang ditambahkan
                                    @endif
                                </p>
                                @if(!request('search') && !request('status'))
                                    <a href="{{ route('pegawai.create') }}" class="btn btn-primary">
                                        <i class="fas fa-plus me-2"></i>Tambah Pegawai Pertama
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($pegawai->hasPages())
            <div class="d-flex justify-content-between align-items-center mt-4">
                <div class="text-muted">
                    Menampilkan {{ $pegawai->firstItem() }} sampai {{ $pegawai->lastItem() }} 
                    dari {{ $pegawai->total() }} data
                </div>
                {{ $pegawai->withQueryString()->links() }}
            </div>
        @endif
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