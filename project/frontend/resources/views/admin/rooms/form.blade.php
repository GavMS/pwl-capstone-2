@extends('dashboard.layout')
@section('title', isset($editRoom) ? 'Edit Ruangan' : 'Tambah Ruangan')
@section('page_title', isset($editRoom) ? 'Edit Ruangan' : 'Tambah Ruangan')

@section('content')

<style>
.back-btn {
    display:inline-flex; align-items:center; justify-content:center;
    width:2.25rem; height:2.25rem; background:#f0f2f5; border-radius:.5rem;
    color:#7b809a; text-decoration:none; transition:background .15s, color .15s; flex-shrink:0;
}
.back-btn:hover { background:#e2e8f0; color:#344767; }
.form-page-title { font-size:1.25rem; font-weight:700; color:#344767; margin:0 0 .2rem; }
.form-page-sub   { font-size:.875rem; color:#7b809a; margin:0; }

.warn-banner {
    display:flex; align-items:flex-start; gap:.875rem;
    padding:1rem 1.25rem; background:#fffbeb; border:1px solid #fde68a;
    border-radius:.875rem; margin-bottom:1.5rem;
}
.warn-banner i { color:#d97706; font-size:1rem; margin-top:.1rem; flex-shrink:0; }
.warn-banner-title { font-size:.875rem; font-weight:700; color:#92400e; margin:0 0 .2rem; }
.warn-banner-sub   { font-size:.8rem; color:#b45309; margin:0 0 .5rem; }
.warn-dep-list { list-style:disc; padding-left:1.2rem; margin:0; }
.warn-dep-list li { font-size:.8rem; color:#92400e; margin-bottom:.15rem; }

.error-banner {
    display:flex; align-items:center; gap:.875rem;
    padding:1rem 1.25rem; background:#fef2f2; border:1px solid #fecaca;
    border-radius:.875rem; margin-bottom:1.5rem;
    font-size:.875rem; font-weight:600; color:#dc2626;
}
.error-banner i { color:#dc2626; font-size:1rem; flex-shrink:0; }

.form-card { background:#fff; border-radius:1rem; box-shadow:0 20px 27px 0 rgba(0,0,0,.05); overflow:hidden; }
.form-card-bar { height:4px; background:linear-gradient(90deg,#7928ca,#ff007f); }
.form-body { padding:2rem; }

.field-group { margin-bottom:1.5rem; }
.field-label { display:block; font-size:.8125rem; font-weight:700; color:#344767; margin-bottom:.5rem; }
.field-label .req { color:#ea0606; margin-left:.2rem; }
.field-label .opt { font-size:.75rem; font-weight:400; color:#adb5bd; margin-left:.35rem; }

.input-wrap { position:relative; }
.input-icon {
    position:absolute; left:.875rem; top:50%; transform:translateY(-50%);
    color:#adb5bd; font-size:.875rem; pointer-events:none; z-index:1;
}
.input-icon-top {
    position:absolute; left:.875rem; top:1rem;
    color:#adb5bd; font-size:.875rem; pointer-events:none; z-index:1;
}
.form-input {
    width:100%; padding:.75rem .875rem .75rem 2.75rem;
    font-size:.875rem; color:#344767; background:#fff;
    border:1.5px solid #d2d6da; border-radius:.65rem; outline:none;
    transition:border-color .2s, box-shadow .2s; box-sizing:border-box; font-family:inherit;
}
.form-input:focus { border-color:#7928ca; box-shadow:0 0 0 3px rgba(121,40,202,.12); }
.form-input::placeholder { color:#adb5bd; }
textarea.form-input { padding:.75rem .875rem .75rem 2.75rem; resize:vertical; min-height:100px; }

.form-input-mono { font-family: monospace; text-transform: uppercase; letter-spacing:.05em; }

.field-hint { margin-top:.4rem; font-size:.775rem; color:#adb5bd; }
.field-hint i { margin-right:.2rem; }

.form-grid { display:grid; grid-template-columns:1fr 1fr; gap:0 1.5rem; }
.col-full  { grid-column:1 / -1; }
@media (max-width:640px) { .form-grid { grid-template-columns:1fr; } .col-full { grid-column:1; } }

.form-divider { border:none; border-top:1px solid #f0f2f5; margin:1.75rem 0 1.5rem; }

.section-label {
    font-size:.65rem; font-weight:700; text-transform:uppercase; letter-spacing:.09em;
    color:#adb5bd; margin:0 0 1.25rem;
    display:flex; align-items:center; gap:.5rem;
}
.section-label::after { content:''; flex:1; height:1px; background:#f0f2f5; }

.form-actions { display:flex; align-items:center; justify-content:flex-end; gap:.75rem; }
.btn-cancel-form {
    padding:.625rem 1.5rem; font-size:.8125rem; font-weight:600;
    color:#7b809a; background:#f0f2f5; border:none; border-radius:.65rem;
    cursor:pointer; text-decoration:none; transition:background .15s;
}
.btn-cancel-form:hover { background:#e2e8f0; color:#344767; }
.btn-submit-form {
    display:inline-flex; align-items:center; gap:.45rem;
    padding:.625rem 1.625rem; font-size:.8125rem; font-weight:700; color:#fff;
    background:linear-gradient(310deg,#7928ca,#ff007f); border:none; border-radius:.65rem;
    box-shadow:0 4px 7px -1px rgba(0,0,0,.11);
    cursor:pointer; transition:box-shadow .15s, transform .15s;
}
.btn-submit-form:hover { box-shadow:0 8px 25px -8px rgba(121,40,202,.7); transform:translateY(-1px); }
</style>

<div class="flex flex-wrap -mx-3">

    {{-- ─── Page Header ──────────────────────── --}}
    <div class="w-full px-3 mb-6">
        <div style="background:#fff; border-radius:1rem; box-shadow:0 20px 27px 0 rgba(0,0,0,.05); padding:1.25rem 1.5rem;">
            <div style="display:flex; align-items:center; gap:1rem;">
                <a href="{{ route('admin.rooms.index') }}" class="back-btn">
                    <i class="fas fa-arrow-left" style="font-size:.75rem;"></i>
                </a>
                <div>
                    <h4 class="form-page-title">
                        @if(isset($editRoom))
                            <i class="fas fa-edit" style="color:#7928ca; font-size:1rem; margin-right:.4rem;"></i>Edit Ruangan
                        @else
                            <i class="fas fa-plus-circle" style="color:#7928ca; font-size:1rem; margin-right:.4rem;"></i>Tambah Ruangan Baru
                        @endif
                    </h4>
                    <p class="form-page-sub">
                        {{ isset($editRoom)
                            ? 'Perbarui data ruangan laboratorium yang dipilih.'
                            : 'Isi formulir berikut untuk menambahkan ruangan baru.' }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- ─── Smart Edit Warning ───────────────── --}}
    @if(isset($editRoom) && count($deps) > 0)
    <div class="w-full px-3 mb-4">
        <div class="warn-banner">
            <i class="fas fa-exclamation-triangle"></i>
            <div>
                <p class="warn-banner-title">Perhatian: Ruangan Ini Memiliki Data Terkait</p>
                <p class="warn-banner-sub">Mengubah data ruangan tidak akan menghapus relasi, namun perlu diperhatikan:</p>
                <ul class="warn-dep-list">
                    @foreach($deps as $dep)
                        <li>{{ $dep['label'] ?? '-' }} ({{ $dep['count'] ?? 0 }} data terhubung)</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    @endif

    {{-- ─── Error ───────────────────────────── --}}
    @if($errors->any())
    <div class="w-full px-3 mb-4">
        <div class="error-banner">
            <i class="fas fa-times-circle"></i>
            {{ $errors->first() }}
        </div>
    </div>
    @endif

    {{-- ─── Form Card ────────────────────────── --}}
    <div class="w-full px-3">
        <div class="form-card">
            <div class="form-card-bar"></div>
            <div class="form-body">

                @if(isset($editRoom))
                    <form method="POST" action="{{ route('admin.rooms.update', $editRoom['id']) }}">
                    @csrf
                    @method('PUT')
                @else
                    <form method="POST" action="{{ route('admin.rooms.store') }}">
                    @csrf
                @endif

                {{-- ── Section: Identitas Ruangan ── --}}
                <p class="section-label"><i class="fas fa-door-open"></i> Identitas Ruangan</p>

                <div class="form-grid">

                    {{-- Nama Ruangan --}}
                    <div class="field-group col-full">
                        <label class="field-label" for="name">
                            Nama Ruangan <span class="req">*</span>
                        </label>
                        <div class="input-wrap">
                            <i class="fas fa-door-open input-icon"></i>
                            <input type="text" id="name" name="name" class="form-input"
                                   value="{{ old('name', $editRoom['name'] ?? '') }}"
                                   placeholder="Contoh: Laboratorium Komputer, Ruang Server..."
                                   required autocomplete="off" />
                        </div>
                    </div>

                    {{-- Kode Ruangan --}}
                    <div class="field-group">
                        <label class="field-label" for="code">
                            Kode Ruangan <span class="req">*</span>
                        </label>
                        <div class="input-wrap">
                            <i class="fas fa-tag input-icon"></i>
                            <input type="text" id="code" name="code" class="form-input form-input-mono"
                                   value="{{ old('code', $editRoom['code'] ?? '') }}"
                                   placeholder="LAB-01"
                                   required autocomplete="off"
                                   oninput="this.value=this.value.toUpperCase()" />
                        </div>
                        <p class="field-hint"><i class="fas fa-info-circle"></i> Kode harus unik. Akan otomatis diubah ke huruf kapital.</p>
                    </div>

                    {{-- Deskripsi --}}
                    <div class="field-group">
                        <label class="field-label" for="description">
                            Deskripsi / Keterangan <span class="opt">(Opsional)</span>
                        </label>
                        <div class="input-wrap" style="position:relative;">
                            <i class="fas fa-align-left input-icon-top"></i>
                            <textarea id="description" name="description"
                                      class="form-input"
                                      placeholder="Contoh: Digunakan untuk praktikum jaringan komputer, kapasitas 30 orang...">{{ old('description', $editRoom['description'] ?? '') }}</textarea>
                        </div>
                    </div>

                </div>

                {{-- ── Actions ── --}}
                <hr class="form-divider" />
                <div class="form-actions">
                    <a href="{{ route('admin.rooms.index') }}" class="btn-cancel-form">Batal</a>
                    <button type="submit" class="btn-submit-form">
                        <i class="fas fa-{{ isset($editRoom) ? 'save' : 'plus-circle' }}" style="font-size:.75rem;"></i>
                        {{ isset($editRoom) ? 'Simpan Perubahan' : 'Tambah Ruangan' }}
                    </button>
                </div>

                </form>
            </div>
        </div>
    </div>

</div>
@endsection
