@extends('layouts.app')

@section('title', 'Edit Pegawai - ' . $pegawai->nama)
@section('page-title', 'Edit Data Pegawai')

@section('page-actions')
    <div class="btn-group">
        <a href="{{ route('pegawai.show', $pegawai) }}" class="btn btn-outline-info">
            <i class="fas fa-eye me-2"></i>Detail
        </a>
        <a href="{{ route('pegawai.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Kembali
        </a>
    </div>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-user-edit me-2"></i>Edit Data: {{ $pegawai->nama }}
                </h5>
                <div class="text-muted mt-1">
                    <small>NIP: {{ $pegawai->nip_format }}</small>
                </div>
            </div>
            <div class="card-body">
                <form action="{{ route('pegawai.update', $pegawai) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <!-- Data Identitas -->
                    <div class="row">
                        <div class="col-12">
                            <h6 class="border-bottom pb-2 mb-3">
                                <i class="fas fa-id-card me-2"></i>Data Identitas
                            </h6>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-8">
                            <label for="nama" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control @error('nama') is-invalid @enderror" 
                                   id="nama" 
                                   name="nama" 
                                   value="{{ old('nama', $pegawai->nama) }}"
                                   placeholder="Contoh: Eddy Fahriannor, S.Sos">
                            @error('nama')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status">
                                <option value="">Pilih Status</option>
                                <option value="aktif" {{ old('status', $pegawai->status) == 'aktif' ? 'selected' : '' }}>Aktif</option>
                                <option value="non_aktif" {{ old('status', $pegawai->status) == 'non_aktif' ? 'selected' : '' }}>Non Aktif</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="nip" class="form-label">NIP <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control @error('nip') is-invalid @enderror" 
                                   id="nip" 
                                   name="nip" 
                                   value="{{ old('nip', $pegawai->nip) }}"
                                   placeholder="18 digit NIP"
                                   maxlength="18">
                            @error('nip')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Format: YYYYMMDDYYYYMMDDXX (18 digit)</div>
                        </div>
                        <div class="col-md-6">
                            <label for="golongan" class="form-label">Golongan <span class="text-danger">*</span></label>
                            <select class="form-select @error('golongan') is-invalid @enderror" id="golongan" name="golongan">
                                <option value="">Pilih Golongan</option>
                                @php
                                    $golongan_options = [
                                        'I/a', 'I/b', 'I/c', 'I/d',
                                        'II/a', 'II/b', 'II/c', 'II/d',
                                        'III/a', 'III/b', 'III/c', 'III/d',
                                        'IV/a', 'IV/b', 'IV/c', 'IV/d', 'IV/e'
                                    ];
                                @endphp
                                @foreach($golongan_options as $gol)
                                    <option value="{{ $gol }}" {{ old('golongan', $pegawai->golongan) == $gol ? 'selected' : '' }}>
                                        {{ $gol }}
                                    </option>
                                @endforeach
                            </select>
                            @error('golongan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="pangkat" class="form-label">Pangkat <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control @error('pangkat') is-invalid @enderror" 
                                   id="pangkat" 
                                   name="pangkat" 
                                   value="{{ old('pangkat', $pegawai->pangkat) }}"
                                   placeholder="Contoh: Penata Tk.I">
                            @error('pangkat')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="jabatan" class="form-label">Jabatan <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control @error('jabatan') is-invalid @enderror" 
                                   id="jabatan" 
                                   name="jabatan" 
                                   value="{{ old('jabatan', $pegawai->jabatan) }}"
                                   placeholder="Contoh: JF Sandiman Ahli Muda">
                            @error('jabatan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Data Instansi -->
                    <div class="row">
                        <div class="col-12">
                            <h6 class="border-bottom pb-2 mb-3 mt-4">
                                <i class="fas fa-building me-2"></i>Data Instansi
                            </h6>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="instansi" class="form-label">Instansi <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control @error('instansi') is-invalid @enderror" 
                                   id="instansi" 
                                   name="instansi" 
                                   value="{{ old('instansi', $pegawai->instansi) }}">
                            @error('instansi')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="tempat_bertugas" class="form-label">Tempat Bertugas <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control @error('tempat_bertugas') is-invalid @enderror" 
                                   id="tempat_bertugas" 
                                   name="tempat_bertugas" 
                                   value="{{ old('tempat_bertugas', $pegawai->tempat_bertugas) }}">
                            @error('tempat_bertugas')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Data Kontak -->
                    <div class="row">
                        <div class="col-12">
                            <h6 class="border-bottom pb-2 mb-3 mt-4">
                                <i class="fas fa-address-book me-2"></i>Data Kontak (Opsional)
                            </h6>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" 
                                   class="form-control @error('email') is-invalid @enderror" 
                                   id="email" 
                                   name="email" 
                                   value="{{ old('email', $pegawai->email) }}"
                                   placeholder="contoh@email.com">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="telepon" class="form-label">Telepon/HP</label>
                            <input type="text" 
                                   class="form-control @error('telepon') is-invalid @enderror" 
                                   id="telepon" 
                                   name="telepon" 
                                   value="{{ old('telepon', $pegawai->telepon) }}"
                                   placeholder="08xxxxxxxxxx">
                            @error('telepon')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-12">
                            <label for="alamat" class="form-label">Alamat</label>
                            <textarea class="form-control @error('alamat') is-invalid @enderror" 
                                      id="alamat" 
                                      name="alamat" 
                                      rows="3"
                                      placeholder="Alamat lengkap">{{ old('alamat', $pegawai->alamat) }}</textarea>
                            @error('alamat')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="row">
                        <div class="col-12">
                            <hr>
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('pegawai.show', $pegawai) }}" class="btn btn-secondary">
                                    <i class="fas fa-times me-2"></i>Batal
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Update Data
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Info Update -->
        <div class="card mt-3">
            <div class="card-body">
                <div class="row text-muted small">
                    <div class="col-md-6">
                        <i class="fas fa-calendar-plus me-1"></i>
                        Ditambahkan: {{ $pegawai->created_at->format('d M Y H:i') }}
                    </div>
                    <div class="col-md-6 text-md-end">
                        <i class="fas fa-calendar-edit me-1"></i>
                        Terakhir diubah: {{ $pegawai->updated_at->format('d M Y H:i') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Format NIP input
document.getElementById('nip').addEventListener('input', function(e) {
    // Hanya allow angka
    this.value = this.value.replace(/[^0-9]/g, '');
    
    // Batasi maksimal 18 digit
    if (this.value.length > 18) {
        this.value = this.value.slice(0, 18);
    }
});

// Format telepon input
document.getElementById('telepon').addEventListener('input', function(e) {
    // Hanya allow angka, +, -, dan spasi
    this.value = this.value.replace(/[^0-9\+\-\s]/g, '');
});
</script>
@endpush