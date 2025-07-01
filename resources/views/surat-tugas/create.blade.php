@extends('layouts.app')

@section('title', 'Buat Surat Tugas')
@section('page-title', 'Buat Surat Tugas Baru')

@section('page-actions')
    <a href="{{ route('surat-tugas.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-2"></i>Kembali
    </a>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12">
        <form action="{{ route('surat-tugas.store') }}" method="POST" id="suratTugasForm">
            @csrf
            
            <div class="row">
                <!-- Kolom Kiri -->
                <div class="col-lg-8">
                    <!-- Informasi Dasar -->
                    <div class="card">
                        <div class="card-header">
                            <h6 class="card-title mb-0">
                                <i class="fas fa-info-circle me-2"></i>Informasi Dasar Surat Tugas
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="nomor_surat" class="form-label">Nomor Surat <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control @error('nomor_surat') is-invalid @enderror" 
                                           id="nomor_surat" 
                                           name="nomor_surat" 
                                           value="{{ old('nomor_surat', $nomorSurat) }}"
                                           readonly>
                                    @error('nomor_surat')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Nomor akan di-generate otomatis</div>
                                </div>
                                <div class="col-md-6">
                                    <label for="tanggal_surat" class="form-label">Tanggal Surat <span class="text-danger">*</span></label>
                                    <input type="date" 
                                           class="form-control @error('tanggal_surat') is-invalid @enderror" 
                                           id="tanggal_surat" 
                                           name="tanggal_surat" 
                                           value="{{ old('tanggal_surat', date('Y-m-d')) }}">
                                    @error('tanggal_surat')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-12">
                                    <label for="perihal" class="form-label">Perihal/Tentang <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control @error('perihal') is-invalid @enderror" 
                                           id="perihal" 
                                           name="perihal" 
                                           value="{{ old('perihal') }}"
                                           placeholder="Contoh: Survey Laptop dan PC">
                                    @error('perihal')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-12">
                                    <label for="maksud_tujuan" class="form-label">Maksud dan Tujuan <span class="text-danger">*</span></label>
                                    <textarea class="form-control @error('maksud_tujuan') is-invalid @enderror" 
                                              id="maksud_tujuan" 
                                              name="maksud_tujuan" 
                                              rows="3"
                                              placeholder="Jelaskan maksud dan tujuan dari tugas ini...">{{ old('maksud_tujuan') }}</textarea>
                                    @error('maksud_tujuan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-12">
                                    <label for="dasar_hukum" class="form-label">Dasar Hukum</label>
                                    <textarea class="form-control @error('dasar_hukum') is-invalid @enderror" 
                                              id="dasar_hukum" 
                                              name="dasar_hukum" 
                                              rows="2"
                                              placeholder="Contoh: Undang-undang, Peraturan, atau dasar hukum lainnya...">{{ old('dasar_hukum') }}</textarea>
                                    @error('dasar_hukum')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Detail Pelaksanaan -->
                    <div class="card mt-4">
                        <div class="card-header">
                            <h6 class="card-title mb-0">
                                <i class="fas fa-calendar me-2"></i>Detail Pelaksanaan
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="tempat_tujuan" class="form-label">Tempat Tujuan <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control @error('tempat_tujuan') is-invalid @enderror" 
                                           id="tempat_tujuan" 
                                           name="tempat_tujuan" 
                                           value="{{ old('tempat_tujuan') }}"
                                           placeholder="Contoh: Banjarbaru">
                                    @error('tempat_tujuan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="transportasi" class="form-label">Transportasi</label>
                                    <select class="form-select @error('transportasi') is-invalid @enderror" 
                                            id="transportasi" 
                                            name="transportasi">
                                        <option value="">Pilih Transportasi</option>
                                        <option value="Kendaraan Dinas" {{ old('transportasi') == 'Kendaraan Dinas' ? 'selected' : '' }}>Kendaraan Dinas</option>
                                        <option value="Kendaraan Pribadi" {{ old('transportasi') == 'Kendaraan Pribadi' ? 'selected' : '' }}>Kendaraan Pribadi</option>
                                        <option value="Angkutan Umum" {{ old('transportasi') == 'Angkutan Umum' ? 'selected' : '' }}>Angkutan Umum</option>
                                        <option value="Pesawat" {{ old('transportasi') == 'Pesawat' ? 'selected' : '' }}>Pesawat</option>
                                    </select>
                                    @error('transportasi')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <label for="tanggal_mulai" class="form-label">Tanggal Mulai <span class="text-danger">*</span></label>
                                    <input type="date" 
                                           class="form-control @error('tanggal_mulai') is-invalid @enderror" 
                                           id="tanggal_mulai" 
                                           name="tanggal_mulai" 
                                           value="{{ old('tanggal_mulai') }}">
                                    @error('tanggal_mulai')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-3">
                                    <label for="tanggal_selesai" class="form-label">Tanggal Selesai <span class="text-danger">*</span></label>
                                    <input type="date" 
                                           class="form-control @error('tanggal_selesai') is-invalid @enderror" 
                                           id="tanggal_selesai" 
                                           name="tanggal_selesai" 
                                           value="{{ old('tanggal_selesai') }}">
                                    @error('tanggal_selesai')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-3">
                                    <label for="waktu_mulai" class="form-label">Waktu Mulai</label>
                                    <input type="time" 
                                           class="form-control @error('waktu_mulai') is-invalid @enderror" 
                                           id="waktu_mulai" 
                                           name="waktu_mulai" 
                                           value="{{ old('waktu_mulai') }}">
                                    @error('waktu_mulai')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-3">
                                    <label for="waktu_selesai" class="form-label">Waktu Selesai</label>
                                    <input type="time" 
                                           class="form-control @error('waktu_selesai') is-invalid @enderror" 
                                           id="waktu_selesai" 
                                           name="waktu_selesai" 
                                           value="{{ old('waktu_selesai') }}">
                                    @error('waktu_selesai')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-12">
                                    <label for="kegiatan_detail" class="form-label">Detail Kegiatan</label>
                                    <textarea class="form-control @error('kegiatan_detail') is-invalid @enderror" 
                                              id="kegiatan_detail" 
                                              name="kegiatan_detail" 
                                              rows="3"
                                              placeholder="Jelaskan detail kegiatan yang akan dilaksanakan...">{{ old('kegiatan_detail') }}</textarea>
                                    @error('kegiatan_detail')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pegawai yang Ditugaskan -->
                    <div class="card mt-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h6 class="card-title mb-0">
                                <i class="fas fa-users me-2"></i>Pegawai yang Ditugaskan
                            </h6>
                            <button type="button" class="btn btn-sm btn-primary" onclick="addPegawai()">
                                <i class="fas fa-plus me-1"></i>Tambah Pegawai
                            </button>
                        </div>
                        <div class="card-body">
                            <div id="pegawai-container">
                                <!-- Template akan diisi oleh JavaScript -->
                            </div>
                            @error('pegawai_ids')
                                <div class="text-danger mt-2">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Kolom Kanan -->
                <div class="col-lg-4">
                    <!-- Anggaran -->
                    <div class="card">
                        <div class="card-header">
                            <h6 class="card-title mb-0">
                                <i class="fas fa-money-bill me-2"></i>Informasi Anggaran
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="sumber_anggaran" class="form-label">Sumber Anggaran</label>
                                <select class="form-select @error('sumber_anggaran') is-invalid @enderror" 
                                        id="sumber_anggaran" 
                                        name="sumber_anggaran">
                                    <option value="">Pilih Sumber Anggaran</option>
                                    <option value="APBD" {{ old('sumber_anggaran') == 'APBD' ? 'selected' : '' }}>APBD</option>
                                    <option value="APBN" {{ old('sumber_anggaran') == 'APBN' ? 'selected' : '' }}>APBN</option>
                                    <option value="Lainnya" {{ old('sumber_anggaran') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                                </select>
                                @error('sumber_anggaran')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="anggaran_estimasi" class="form-label">Estimasi Anggaran</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" 
                                           class="form-control @error('anggaran_estimasi') is-invalid @enderror" 
                                           id="anggaran_estimasi" 
                                           name="anggaran_estimasi" 
                                           value="{{ old('anggaran_estimasi') }}"
                                           min="0"
                                           step="1000">
                                </div>
                                @error('anggaran_estimasi')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Penandatangan -->
                    <div class="card mt-4">
                        <div class="card-header">
                            <h6 class="card-title mb-0">
                                <i class="fas fa-signature me-2"></i>Penandatangan
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="pembuat_surat" class="form-label">Pembuat Surat <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control @error('pembuat_surat') is-invalid @enderror" 
                                       id="pembuat_surat" 
                                       name="pembuat_surat" 
                                       value="{{ old('pembuat_surat', 'Muhammad Nor, S.Sos, MM') }}">
                                @error('pembuat_surat')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="penandatangan" class="form-label">Penandatangan <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control @error('penandatangan') is-invalid @enderror" 
                                       id="penandatangan" 
                                       name="penandatangan" 
                                       value="{{ old('penandatangan', 'Muhammad Nor, S.Sos, MM') }}">
                                @error('penandatangan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="tempat_pembuatan" class="form-label">Tempat Pembuatan <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control @error('tempat_pembuatan') is-invalid @enderror" 
                                       id="tempat_pembuatan" 
                                       name="tempat_pembuatan" 
                                       value="{{ old('tempat_pembuatan', 'Paringin Selatan') }}">
                                @error('tempat_pembuatan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Catatan -->
                    <div class="card mt-4">
                        <div class="card-header">
                            <h6 class="card-title mb-0">
                                <i class="fas fa-sticky-note me-2"></i>Catatan
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="catatan" class="form-label">Catatan Tambahan</label>
                                <textarea class="form-control @error('catatan') is-invalid @enderror" 
                                          id="catatan" 
                                          name="catatan" 
                                          rows="3"
                                          placeholder="Catatan atau keterangan tambahan...">{{ old('catatan') }}</textarea>
                                @error('catatan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="card mt-4">
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Simpan Surat Tugas
                                </button>
                                <a href="{{ route('surat-tugas.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times me-2"></i>Batal
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Template Pegawai (Hidden) -->
<div id="pegawai-template" style="display: none;">
    <div class="pegawai-item border rounded p-3 mb-3">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h6 class="mb-0">Pegawai <span class="pegawai-number"></span></h6>
            <button type="button" class="btn btn-sm btn-outline-danger" onclick="removePegawai(this)">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div class="row">
            <div class="col-md-6 mb-2">
                <label class="form-label">Pegawai <span class="text-danger">*</span></label>
                <select class="form-select pegawai-select" name="pegawai_ids[]" required>
                    <option value="">Pilih Pegawai</option>
                    @foreach($pegawai as $p)
                        <option value="{{ $p->id }}">{{ $p->nama }} - {{ $p->nip }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6 mb-2">
                <label class="form-label">Peran <span class="text-danger">*</span></label>
                <select class="form-select" name="peran[]" required>
                    <option value="anggota">Anggota</option>
                    <option value="ketua">Ketua</option>
                    <option value="sekretaris">Sekretaris</option>
                    <option value="bendahara">Bendahara</option>
                </select>
            </div>
            <div class="col-md-8 mb-2">
                <label class="form-label">Tugas Khusus</label>
                <input type="text" class="form-control" name="tugas_khusus[]" 
                       placeholder="Tugas khusus untuk pegawai ini...">
            </div>
            <div class="col-md-4 mb-2">
                <label class="form-label">Honor (Rp)</label>
                <input type="number" class="form-control" name="honor[]" 
                       min="0" step="1000" placeholder="0">
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let pegawaiCounter = 0;

function addPegawai() {
    pegawaiCounter++;
    const template = document.getElementById('pegawai-template').innerHTML;
    const container = document.getElementById('pegawai-container');
    
    const newPegawai = document.createElement('div');
    newPegawai.innerHTML = template;
    newPegawai.querySelector('.pegawai-number').textContent = pegawaiCounter;
    
    container.appendChild(newPegawai.firstElementChild);
    
    // Update select to prevent duplicate selection
    updatePegawaiSelects();
}

function removePegawai(button) {
    if (document.querySelectorAll('.pegawai-item').length > 1) {
        button.closest('.pegawai-item').remove();
        updatePegawaiSelects();
    } else {
        alert('Minimal harus ada 1 pegawai yang ditugaskan');
    }
}

function updatePegawaiSelects() {
    const selects = document.querySelectorAll('.pegawai-select');
    const selectedValues = Array.from(selects).map(select => select.value).filter(val => val);
    
    selects.forEach(select => {
        const currentValue = select.value;
        const options = select.querySelectorAll('option');
        
        options.forEach(option => {
            if (option.value && selectedValues.includes(option.value) && option.value !== currentValue) {
                option.disabled = true;
                option.style.color = '#ccc';
            } else {
                option.disabled = false;
                option.style.color = '';
            }
        });
    });
}

// Initialize with one pegawai
document.addEventListener('DOMContentLoaded', function() {
    addPegawai();
    
    // Add change event listeners to pegawai selects
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('pegawai-select')) {
            updatePegawaiSelects();
        }
    });
    
    // Auto-set tanggal selesai when tanggal mulai changes
    document.getElementById('tanggal_mulai').addEventListener('change', function() {
        const tanggalMulai = this.value;
        const tanggalSelesai = document.getElementById('tanggal_selesai');
        
        if (tanggalMulai && !tanggalSelesai.value) {
            tanggalSelesai.value = tanggalMulai;
        }
    });
});
</script>
@endpush