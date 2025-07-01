@extends('layouts.app')

@section('title', 'Surat Tugas')
@section('page-title', 'Daftar Surat Tugas')

@section('page-actions')
    <a href="{{ route('surat-tugas.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>Buat Surat Tugas
    </a>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">
            <i class="fas fa-file-alt me-2"></i>Daftar Surat Tugas
        </h5>
    </div>
    <div class="card-body">
        <!-- Filter dan Pencarian -->
        <form method="GET" action="{{ route('surat-tugas.index') }}" class="mb-4">
            <div class="row g-3">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" 
                               class="form-control" 
                               name="search" 
                               value="{{ request('search') }}" 
                               placeholder="Cari nomor surat, perihal, tujuan...">
                    </div>
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="disetujui" {{ request('status') == 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                        <option value="dilaksanakan" {{ request('status') == 'dilaksanakan' ? 'selected' : '' }}>Dilaksanakan</option>
                        <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                        <option value="dibatalkan" {{ request('status') == 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="tahun" class="form-select">
                        <option value="">Semua Tahun</option>
                        @foreach($years as $year)
                            <option value="{{ $year }}" {{ request('tahun') == $year ? 'selected' : '' }}>
                                {{ $year }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="bulan" class="form-select">
                        <option value="">Semua Bulan</option>
                        @for($i = 1; $i <= 12; $i++)
                            <option value="{{ $i }}" {{ request('bulan') == $i ? 'selected' : '' }}>
                                {{ DateTime::createFromFormat('!m', $i)->format('F') }}
                            </option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-2">
                    <div class="d-grid gap-2 d-md-flex">
                        <button type="submit" class="btn btn-outline-primary">
                            <i class="fas fa-filter me-1"></i>Filter
                        </button>
                        <a href="{{ route('surat-tugas.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-1"></i>Reset
                        </a>
                    </div>
                </div>
            </div>
        </form>

        <!-- Tabel Data Surat Tugas -->
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th width="5%">No</th>
                        <th>Nomor Surat</th>
                        <th>Perihal</th>
                        <th>Pegawai</th>
                        <th>Tujuan</th>
                        <th>Tanggal</th>
                        <th>Status</th>
                        <th width="15%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($suratTugas as $key => $st)
                        <tr>
                            <td>{{ $suratTugas->firstItem() + $key }}</td>
                            <td>
                                <strong>{{ $st->nomor_surat }}</strong>
                                <br><small class="text-muted">{{ $st->tanggal_surat->format('d M Y') }}</small>
                            </td>
                            <td>
                                <strong>{{ Str::limit($st->perihal, 40) }}</strong>
                                @if($st->maksud_tujuan)
                                    <br><small class="text-muted">{{ Str::limit($st->maksud_tujuan, 50) }}</small>
                                @endif
                            </td>
                            <td>
                                @if($st->pegawai->count() > 0)
                                    @foreach($st->pegawai->take(2) as $pegawai)
                                        <span class="badge bg-light text-dark">
                                            {{ $pegawai->nama }}
                                            @if($pegawai->pivot->peran != 'anggota')
                                                <small>({{ ucfirst($pegawai->pivot->peran) }})</small>
                                            @endif
                                        </span><br>
                                    @endforeach
                                    @if($st->pegawai->count() > 2)
                                        <small class="text-muted">dan {{ $st->pegawai->count() - 2 }} lainnya</small>
                                    @endif
                                @else
                                    <span class="text-muted">Belum ada pegawai</span>
                                @endif
                            </td>
                            <td>
                                <i class="fas fa-map-marker-alt text-primary me-1"></i>
                                {{ $st->tempat_tujuan }}
                            </td>
                            <td>
                                <strong>{{ $st->tanggal_mulai->format('d M') }}</strong>
                                @if($st->tanggal_mulai->format('Y-m-d') != $st->tanggal_selesai->format('Y-m-d'))
                                    - {{ $st->tanggal_selesai->format('d M Y') }}
                                @else
                                    {{ $st->tanggal_mulai->format('Y') }}
                                @endif
                                <br><small class="text-muted">{{ $st->durasi_hari }} hari</small>
                            </td>
                            <td>
                                <span class="badge {{ $st->status_badge_class }}">
                                    {{ $st->status_label }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="{{ route('surat-tugas.show', $st) }}" 
                                       class="btn btn-outline-info" 
                                       title="Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if($st->canBeEdited())
                                        <a href="{{ route('surat-tugas.edit', $st) }}" 
                                           class="btn btn-outline-warning" 
                                           title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" 
                                                class="btn btn-outline-danger" 
                                                title="Hapus"
                                                onclick="confirmDelete({{ $st->id }}, '{{ $st->nomor_surat }}')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    @endif
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-outline-secondary dropdown-toggle" 
                                                data-bs-toggle="dropdown" title="Lainnya">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="#">
                                                <i class="fas fa-file-pdf me-2"></i>Download PDF
                                            </a></li>
                                            @if($st->status == 'disetujui')
                                                <li><a class="dropdown-item" href="{{ route('sppd.create', ['surat_tugas_id' => $st->id]) }}">
                                                    <i class="fas fa-plane me-2"></i>Buat SPPD
                                                </a></li>
                                            @endif
                                        </ul>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">Tidak ada surat tugas</h5>
                                <p class="text-muted">
                                    @if(request('search') || request('status') || request('tahun'))
                                        Tidak ditemukan surat tugas sesuai kriteria pencarian
                                    @else
                                        Belum ada surat tugas yang dibuat
                                    @endif
                                </p>
                                @if(!request('search') && !request('status') && !request('tahun'))
                                    <a href="{{ route('surat-tugas.create') }}" class="btn btn-primary">
                                        <i class="fas fa-plus me-2"></i>Buat Surat Tugas Pertama
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($suratTugas->hasPages())
            <div class="d-flex justify-content-between align-items-center mt-4">
                <div class="text-muted">
                    Menampilkan {{ $suratTugas->firstItem() }} sampai {{ $suratTugas->lastItem() }} 
                    dari {{ $suratTugas->total() }} data
                </div>
                {{ $suratTugas->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Summary Cards -->
<div class="row mt-4">
    @php
        $totalDraft = App\Models\SuratTugas::where('status', 'draft')->count();
        $totalDisetujui = App\Models\SuratTugas::where('status', 'disetujui')->count();
        $totalDilaksanakan = App\Models\SuratTugas::where('status', 'dilaksanakan')->count();
        $totalSelesai = App\Models\SuratTugas::where('status', 'selesai')->count();
    @endphp
    
    <div class="col-md-3">
        <div class="card text-center bg-secondary text-white">
            <div class="card-body">
                <h4>{{ $totalDraft }}</h4>
                <small>Draft</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center bg-primary text-white">
            <div class="card-body">
                <h4>{{ $totalDisetujui }}</h4>
                <small>Disetujui</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center bg-warning text-dark">
            <div class="card-body">
                <h4>{{ $totalDilaksanakan }}</h4>
                <small>Dilaksanakan</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center bg-success text-white">
            <div class="card-body">
                <h4>{{ $totalSelesai }}</h4>
                <small>Selesai</small>
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
function confirmDelete(id, nomorSurat) {
    if (confirm(`Apakah Anda yakin ingin menghapus surat tugas "${nomorSurat}"?\n\nData yang dihapus tidak dapat dikembalikan.`)) {
        const form = document.getElementById('delete-form');
        form.action = `/surat-tugas/${id}`;
        form.submit();
    }
}
</script>
@endpush