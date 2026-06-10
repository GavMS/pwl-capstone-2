@extends('dashboard.layout')
@section('title', $editDraft ? 'Edit Draf Pengadaan' : 'Buat Draf Pengadaan')
@section('page_title', $editDraft ? 'Edit Draf' : 'Buat Draf Baru')

@section('content')

<style>
/* ─── Back button ─────────────────────────────────── */
.back-btn {
    display: inline-flex; align-items: center; justify-content: center;
    width: 2.25rem; height: 2.25rem; background: #f0f2f5; border-radius: .5rem;
    color: #7b809a; text-decoration: none; transition: background .15s, color .15s; flex-shrink: 0;
}
.back-btn:hover { background: #e2e8f0; color: #344767; }
.form-page-title { font-size: 1.25rem; font-weight: 700; color: #344767; margin: 0 0 .2rem; }
.form-page-sub   { font-size: .875rem; color: #7b809a; margin: 0; }

/* ─── Error banner ────────────────────────────────── */
.error-banner {
    display: flex; align-items: center; gap: .875rem; padding: 1rem 1.25rem;
    background: #fef2f2; border: 1px solid #fecaca; border-radius: .875rem;
    margin-bottom: 1.5rem; font-size: .875rem; font-weight: 600; color: #dc2626;
}
.error-banner i { color: #dc2626; font-size: 1rem; flex-shrink: 0; }

/* ─── Form card ───────────────────────────────────── */
.form-card { background: #fff; border-radius: 1rem; box-shadow: 0 20px 27px 0 rgba(0,0,0,.05); overflow: hidden; margin-bottom: 1.5rem; }
.form-card-bar { height: 4px; background: linear-gradient(90deg, #7928ca, #ff007f); }
.form-body { padding: 2rem; }

/* ─── Field groups ────────────────────────────────── */
.field-group { margin-bottom: 1.5rem; }
.field-label { display: block; font-size: .8125rem; font-weight: 700; color: #344767; margin-bottom: .5rem; }
.field-label .req { color: #ea0606; margin-left: .2rem; }
.field-label .opt { font-size: .75rem; font-weight: 400; color: #adb5bd; margin-left: .35rem; }
.input-wrap { position: relative; }
.input-icon { position: absolute; left: .875rem; top: 50%; transform: translateY(-50%); color: #adb5bd; font-size: .875rem; pointer-events: none; z-index: 1; }
.form-input {
    width: 100%; padding: .75rem .875rem .75rem 2.75rem; font-size: .875rem;
    color: #344767; background: #fff; border: 1.5px solid #d2d6da; border-radius: .65rem;
    outline: none; transition: border-color .2s, box-shadow .2s; box-sizing: border-box; font-family: inherit;
}
.form-input:focus { border-color: #7928ca; box-shadow: 0 0 0 3px rgba(121,40,202,.12); }
.form-input::placeholder { color: #adb5bd; }
select.form-input { appearance: none; -webkit-appearance: none; cursor: pointer; padding-right: 2.5rem; }
.select-chevron { position: absolute; right: .875rem; top: 50%; transform: translateY(-50%); color: #adb5bd; font-size: .7rem; pointer-events: none; }

/* ─── Grid ────────────────────────────────────────── */
.form-grid { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 0 1.5rem; }
@media (max-width: 768px) { .form-grid { grid-template-columns: 1fr 1fr; } }
@media (max-width: 480px) { .form-grid { grid-template-columns: 1fr; } }

/* ─── Section label ───────────────────────────────── */
.section-label {
    font-size: .65rem; font-weight: 700; text-transform: uppercase; letter-spacing: .09em;
    color: #adb5bd; margin: 0 0 1.25rem; display: flex; align-items: center; gap: .5rem;
}
.section-label::after { content: ''; flex: 1; height: 1px; background: #f0f2f5; }
.form-divider { border: none; border-top: 1px solid #f0f2f5; margin: 1.75rem 0 1.5rem; }

/* ─── Items table ─────────────────────────────────── */
.items-table { width: 100%; border-collapse: collapse; }
.items-table thead th {
    padding: .75rem .5rem; font-size: .65rem; font-weight: 700; text-transform: uppercase;
    color: #adb5bd; border-bottom: 1px solid #f0f2f5; text-align: left;
}
.items-table tbody td { padding: .75rem .5rem; border-bottom: 1px solid #f0f2f5; vertical-align: top; }
.items-table tbody tr:last-child td { border-bottom: none; }

/* ─── Item field inputs ───────────────────────────── */
.item-input {
    width: 100%; padding: .5rem .75rem; font-size: .8125rem; color: #344767;
    border: 1.5px solid #d2d6da; border-radius: .5rem; outline: none;
    transition: border-color .2s, box-shadow .2s; box-sizing: border-box; font-family: inherit;
}
.item-input:focus { border-color: #7928ca; box-shadow: 0 0 0 2px rgba(121,40,202,.12); }
.item-input::placeholder { color: #adb5bd; }
select.item-input { appearance: none; -webkit-appearance: none; cursor: pointer; }

/* ─── Action buttons ──────────────────────────────── */
.btn-add-item {
    display: inline-flex; align-items: center; gap: .35rem;
    padding: .5rem 1rem; font-size: .75rem; font-weight: 600; color: #2152ff;
    background: #eff6ff; border: 1px dashed #bfdbfe; border-radius: .45rem;
    cursor: pointer; transition: background .15s;
}
.btn-add-item:hover { background: #dbeafe; }
.btn-remove-item {
    padding: .35rem .65rem; border-radius: .45rem; font-size: .8rem; font-weight: 600;
    background: #fef2f2; color: #dc2626; border: 1px solid #fecaca;
    display: inline-flex; align-items: center; justify-content: center;
    cursor: pointer; transition: background .15s;
}
.btn-remove-item:hover { background: #fee2e2; }
.hidden { display: none !important; }

/* ─── Total bar + actions ─────────────────────────── */
.total-bar {
    background: linear-gradient(135deg, #f5f3ff, #fdf2f8);
    border: 1px solid #e9d5ff; border-radius: .75rem;
    padding: 1rem 1.5rem; margin-top: 1.25rem;
    display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: .75rem;
}
.form-actions { display: flex; align-items: center; gap: .75rem; }
.btn-cancel-form {
    padding: .625rem 1.5rem; font-size: .8125rem; font-weight: 600;
    color: #7b809a; background: #f0f2f5; border: none; border-radius: .65rem;
    cursor: pointer; text-decoration: none; transition: background .15s;
}
.btn-cancel-form:hover { background: #e2e8f0; color: #344767; }
.btn-submit-form {
    display: inline-flex; align-items: center; gap: .45rem;
    padding: .625rem 1.625rem; font-size: .8125rem; font-weight: 700; color: #fff;
    background: linear-gradient(310deg, #7928ca, #ff007f);
    border: none; border-radius: .65rem;
    box-shadow: 0 4px 7px -1px rgba(0,0,0,.11), 0 2px 4px -1px rgba(0,0,0,.07);
    cursor: pointer; transition: box-shadow .15s, transform .15s;
}
.btn-submit-form:hover { box-shadow: 0 8px 25px -8px rgba(121,40,202,.7); transform: translateY(-1px); }
</style>

<div class="flex flex-wrap -mx-3">

    {{-- ─── Page Header ─── --}}
    <div class="w-full px-3 mb-6">
        <div style="background:#fff; border-radius:1rem; box-shadow:0 20px 27px 0 rgba(0,0,0,.05); padding:1.25rem 1.5rem;">
            <div style="display:flex; align-items:center; gap:1rem;">
                <a href="{{ route('kalab.procurement.index') }}" class="back-btn">
                    <i class="fas fa-arrow-left" style="font-size:.75rem;"></i>
                </a>
                <div>
                    <h4 class="form-page-title">
                        @if($editDraft)
                            <i class="fas fa-file-edit" style="color:#7928ca; font-size:1rem; margin-right:.4rem;"></i>Edit Draf Pengadaan
                        @else
                            <i class="fas fa-file-plus" style="color:#7928ca; font-size:1rem; margin-right:.4rem;"></i>Buat Draf Pengadaan Baru
                        @endif
                    </h4>
                    <p class="form-page-sub">
                        {{ $editDraft ? 'Perbarui isi draf pengadaan barang.' : 'Isi formulir berikut untuk membuat usulan pengadaan baru.' }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- ─── Error ─── --}}
    @if($errors->any())
    <div class="w-full px-3 mb-4">
        <div class="error-banner">
            <i class="fas fa-times-circle"></i>
            {{ $errors->first() }}
        </div>
    </div>
    @endif

    {{-- ─── Form Card ─── --}}
    <div class="w-full px-3">
        <div class="form-card">
            <div class="form-card-bar"></div>
            <div class="form-body">

                <form action="{{ $editDraft ? route('kalab.procurement.update', $editDraft['id']) : route('kalab.procurement.store') }}" method="POST">
                    @csrf
                    @if($editDraft) @method('PUT') @endif

                    {{-- Seksi: Informasi Pengadaan --}}
                    <p class="section-label"><i class="fas fa-file-invoice"></i> Informasi Pengadaan</p>

                    <div class="form-grid">
                        <div class="field-group" style="grid-column: 1 / -1;">
                            <label class="field-label" for="title">Judul Usulan <span class="req">*</span></label>
                            <div class="input-wrap">
                                <i class="fas fa-heading input-icon"></i>
                                <input type="text" id="title" name="title" class="form-input"
                                       value="{{ old('title', $editDraft['title'] ?? '') }}"
                                       placeholder="Contoh: Draf Pengadaan Lab Komputer 2026" required />
                            </div>
                        </div>

                        <div class="field-group">
                            <label class="field-label" for="year">Tahun Anggaran <span class="req">*</span></label>
                            <div class="input-wrap">
                                <i class="fas fa-calendar-alt input-icon"></i>
                                <input type="number" id="year" name="year" class="form-input"
                                       min="2020" max="2100"
                                       value="{{ old('year', $editDraft['year'] ?? date('Y')) }}" required />
                            </div>
                        </div>

                        <div class="field-group">
                            <label class="field-label" for="status">Status Usulan <span class="req">*</span></label>
                            <div class="input-wrap">
                                <i class="fas fa-toggle-on input-icon"></i>
                                <select id="status" name="status" class="form-input">
                                    <option value="draft" {{ old('status', $editDraft['status'] ?? 'draft') === 'draft' ? 'selected' : '' }}>Simpan sebagai Draft</option>
                                    <option value="submitted" {{ old('status', $editDraft['status'] ?? '') === 'submitted' ? 'selected' : '' }}>Ajukan Langsung (Kunci)</option>
                                </select>
                                <i class="fas fa-chevron-down select-chevron"></i>
                            </div>
                        </div>
                    </div>

                    <hr class="form-divider" />

                    {{-- Seksi: Rincian Barang --}}
                    <p class="section-label"><i class="fas fa-list-ol"></i> Daftar Barang yang Diusulkan</p>

                    <div style="overflow-x:auto; margin-bottom:1rem;">
                        <table class="items-table">
                            <thead>
                                <tr>
                                    <th style="width:120px;">Tipe</th>
                                    <th style="min-width:200px;">Nama Barang</th>
                                    <th style="width:140px;">Harga Satuan (Rp)</th>
                                    <th style="width:80px;">Qty</th>
                                    <th style="width:180px;">Link Pembelian</th>
                                    <th id="replaceHeader" style="width:180px;">Menggantikan Aset</th>
                                    <th style="width:120px;">Catatan</th>
                                    <th id="aksiHeader" style="width:50px; text-align:center;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="itemsContainer"></tbody>
                        </table>
                    </div>

                    <button type="button" class="btn-add-item" onclick="addItemRow()">
                        <i class="fas fa-plus"></i> Tambah Baris Barang
                    </button>

                    {{-- Total + Submit --}}
                    <div class="total-bar">
                        <div>
                            <p style="font-size:.7rem; color:#7b809a; font-weight:700; text-transform:uppercase; margin-bottom:.2rem;">Total Perkiraan Anggaran</p>
                            <h4 style="font-size:1.35rem; font-weight:800; color:#7928ca; margin:0;" id="grandTotalLabel">Rp 0</h4>
                        </div>
                        <div class="form-actions">
                            <a href="{{ route('kalab.procurement.index') }}" class="btn-cancel-form">Batal</a>
                            <button type="submit" class="btn-submit-form">
                                <i class="fas fa-save" style="font-size:.75rem;"></i>
                                {{ $editDraft ? 'Simpan Perubahan' : 'Buat Draf' }}
                            </button>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

<script>
const assets      = @json($assets);       // list inventaris
const consumables = @json($consumables);  // list BHP (tidak dipakai di dropdown, tapi dikirim untuk referensi)
const initialItems = @json($items);
let itemIndex = 0;

document.addEventListener('DOMContentLoaded', () => {
    if (initialItems && initialItems.length > 0) {
        initialItems.forEach(item => addItemRow(item));
    } else {
        addItemRow();
    }
});

function addItemRow(data = null) {
    const container = document.getElementById('itemsContainer');
    const tr = document.createElement('tr');
    tr.id = `item-row-${itemIndex}`;
    tr.className = 'item-row';

    const currentType = data ? data.item_type : 'inventaris';

    // Opsi dropdown inventaris
    let assetOptions = '<option value="">— Tidak Mengganti —</option>';
    assets.forEach(a => {
        const sel = (data && data.replaced_asset_id == a.id) ? 'selected' : '';
        assetOptions += `<option value="${a.id}" ${sel}>${a.name} (${a.code || '-'})</option>`;
    });

    tr.innerHTML = `
        <td>
            <select name="items[${itemIndex}][item_type]" class="item-input type-select" required onchange="onTypeChange(this)">
                <option value="inventaris" ${currentType === 'inventaris' ? 'selected' : ''}>Inventaris</option>
                <option value="bhp"        ${currentType === 'bhp'        ? 'selected' : ''}>BHP</option>
            </select>
        </td>
        <td><input type="text" name="items[${itemIndex}][name]" class="item-input" placeholder="Nama barang..." value="${data ? escHtml(data.name) : ''}" required></td>
        <td><input type="number" name="items[${itemIndex}][price]" class="item-input calc-trigger input-price" min="0" placeholder="0" value="${data ? data.price : '0'}" required></td>
        <td><input type="number" name="items[${itemIndex}][quantity]" class="item-input calc-trigger input-qty" min="1" placeholder="1" value="${data ? data.quantity : '1'}" required></td>
        <td><input type="url" name="items[${itemIndex}][purchase_link]" class="item-input" placeholder="https://..." value="${data ? escHtml(data.purchase_link || '') : ''}"></td>
        <td class="replace-cell">
            <select name="items[${itemIndex}][replaced_asset_id]" class="item-input replace-select" ${currentType === 'bhp' ? 'style="display:none;"' : ''}>${assetOptions}</select>
            <input type="text" class="item-input replace-disabled" value="Tidak berlaku (BHP)" disabled style="background-color: #f8f9fa; color: #adb5bd; cursor: not-allowed; ${currentType === 'inventaris' ? 'display:none;' : ''}" />
        </td>
        <td><input type="text" name="items[${itemIndex}][notes]" class="item-input" placeholder="Catatan..." value="${data ? escHtml(data.notes || '') : ''}"></td>
        <td class="aksi-col" style="text-align:center;">
            <button type="button" class="btn-remove-item" onclick="removeItemRow(${itemIndex})" title="Hapus baris">
                <i class="fas fa-trash"></i>
            </button>
        </td>
    `;

    container.appendChild(tr);
    tr.querySelectorAll('.calc-trigger').forEach(input => input.addEventListener('input', calculateGrandTotal));
    itemIndex++;
    calculateGrandTotal();
    updateRemoveVisibility();
}

// ── Dipanggil saat tipe berubah ──────────────────────────────
function onTypeChange(selectEl) {
    const row = selectEl.closest('tr');
    const replaceSelect = row.querySelector('.replace-select');
    const replaceDisabled = row.querySelector('.replace-disabled');

    if (selectEl.value === 'bhp') {
        replaceSelect.style.display = 'none';
        replaceSelect.value = ''; // reset nilai
        replaceDisabled.style.display = '';
    } else {
        replaceSelect.style.display = '';
        replaceDisabled.style.display = 'none';
    }
}

function escHtml(str) {
    if (!str) return '';
    return String(str).replace(/&/g,'&amp;').replace(/"/g,'&quot;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
}

function removeItemRow(index) {
    const row = document.getElementById(`item-row-${index}`);
    if (row) row.remove();
    if (document.querySelectorAll('.item-row').length === 0) addItemRow();
    else updateRemoveVisibility();
    calculateGrandTotal();
}

function updateRemoveVisibility() {
    const rows = document.querySelectorAll('.item-row');
    const aksiHeader = document.getElementById('aksiHeader');
    const aksiCols = document.querySelectorAll('.aksi-col');
    if (rows.length <= 1) {
        if (aksiHeader) aksiHeader.classList.add('hidden');
        aksiCols.forEach(c => c.classList.add('hidden'));
    } else {
        if (aksiHeader) aksiHeader.classList.remove('hidden');
        aksiCols.forEach(c => c.classList.remove('hidden'));
    }
}

function calculateGrandTotal() {
    let total = 0;
    document.querySelectorAll('.item-row').forEach(row => {
        const price = parseFloat(row.querySelector('.input-price').value) || 0;
        const qty   = parseInt(row.querySelector('.input-qty').value) || 0;
        total += price * qty;
    });
    document.getElementById('grandTotalLabel').textContent =
        new Intl.NumberFormat('id-ID', { style:'currency', currency:'IDR', maximumFractionDigits:0 }).format(total);
}
</script>

@endsection
