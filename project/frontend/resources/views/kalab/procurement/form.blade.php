@extends('dashboard.layout')
@section('title', $editDraft ? 'Edit Draf Pengadaan' : 'Buat Draf Pengadaan Baru')

@section('content')
<style>
/* ─── Page Styles ─────────────────────────────────── */
.form-card {
    background: #fff;
    border-radius: 1rem;
    box-shadow: 0 20px 27px 0 rgba(0,0,0,.05);
    padding: 2rem;
}
.form-title {
    font-size: 1.15rem;
    font-weight: 700;
    color: #344767;
    margin-bottom: 1.5rem;
    border-bottom: 1px solid #f0f2f5;
    padding-bottom: .75rem;
}
/* ─── Inputs ──────────────────────────────────────── */
.form-group {
    margin-bottom: 1.25rem;
}
.form-label {
    display: block;
    font-size: .75rem;
    font-weight: 700;
    color: #344767;
    margin-bottom: .5rem;
    text-transform: uppercase;
}
.form-control {
    width: 100%;
    padding: .5rem .75rem;
    font-size: .875rem;
    border: 1px solid #d2d6da;
    border-radius: .5rem;
    color: #495057;
    background-color: #fff;
    outline: none;
    transition: border-color .2s, box-shadow .2s;
}
.form-control:focus {
    border-color: #7928ca;
    box-shadow: 0 0 0 2px rgba(121,40,202,.15);
}
/* ─── Buttons ─────────────────────────────────────── */
.btn-save {
    display: inline-flex; align-items: center; gap: .5rem;
    padding: .6rem 1.5rem; font-size: .8125rem; font-weight: 600; color: #fff;
    background: linear-gradient(310deg, #7928ca, #ff007f);
    border: none; border-radius: .5rem; cursor: pointer;
    box-shadow: 0 4px 7px -1px rgba(0,0,0,.11), 0 2px 4px -1px rgba(0,0,0,.07);
    transition: transform .15s, box-shadow .15s;
}
.btn-save:hover { transform: translateY(-1px); box-shadow: 0 8px 25px -8px rgba(121,40,202,.7); }

.btn-secondary {
    display: inline-flex; align-items: center; gap: .5rem;
    padding: .6rem 1.5rem; font-size: .8125rem; font-weight: 600; color: #7b809a;
    background: #f0f2f5; border: none; border-radius: .5rem; cursor: pointer;
    transition: background .15s; text-decoration: none;
}
.btn-secondary:hover { background: #e2e8f0; color: #7b809a; }

.btn-add-item {
    display: inline-flex; align-items: center; gap: .35rem;
    padding: .45rem 1rem; font-size: .75rem; font-weight: 600; color: #2152ff;
    background: #eff6ff; border: 1px dashed #bfdbfe; border-radius: .45rem; cursor: pointer;
    transition: background .15s;
}
.btn-add-item:hover { background: #dbeafe; }

.btn-remove-item {
    padding: .35rem .75rem; border-radius: .45rem; font-size: .75rem; font-weight: 600;
    background: #fef2f2; color: #dc2626; border: 1px solid #fecaca;
    display: inline-flex; align-items: center; justify-content: center; gap: .35rem;
    cursor: pointer; transition: background .15s;
}
.btn-remove-item:hover { background: #fee2e2; }
.hidden { display: none !important; }

/* ─── Table ───────────────────────────────────────── */
.items-table { width: 100%; border-collapse: collapse; }
.items-table thead th {
    padding: .75rem .5rem;
    font-size: .65rem; font-weight: 700; text-transform: uppercase;
    color: #adb5bd; border-bottom: 1px solid #f0f2f5;
    text-align: left;
}
.items-table tbody td {
    padding: .75rem .5rem;
    border-bottom: 1px solid #f0f2f5;
    vertical-align: top;
}
.total-summary-box {
    background: #f8f9fa;
    border-radius: .75rem;
    padding: 1rem 1.5rem;
    margin-top: 1.5rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
</style>

<div class="flex flex-wrap -mx-3">
    <div class="w-full px-3">
        <div class="form-card">
            <h4 class="form-title">
                <i class="fas fa-file-invoice-dollar text-purple-700 mr-2"></i>
                {{ $editDraft ? 'Edit Draf Pengadaan Barang' : 'Buat Draf Pengadaan Barang Baru' }}
            </h4>

            @if($errors->any())
            <div class="mb-4 p-4 rounded-lg bg-red-50 border border-red-200 text-red-600 text-sm">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form action="{{ $editDraft ? route('kalab.procurement.update', $editDraft['id']) : route('kalab.procurement.store') }}" method="POST">
                @csrf
                @if($editDraft)
                    @method('PUT')
                @endif

                {{-- ─── Section 1: Header Draf ─────────────────────────── --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                    <div class="form-group">
                        <label class="form-label" for="title">Judul Usulan Draf <span class="text-red-500">*</span></label>
                        <input type="text" id="title" name="title" class="form-control" placeholder="Contoh: Draf Pengadaan Lab Komputer 2026" value="{{ old('title', $editDraft['title'] ?? '') }}" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="year">Tahun Anggaran <span class="text-red-500">*</span></label>
                        <input type="number" id="year" name="year" class="form-control" min="2020" max="2100" value="{{ old('year', $editDraft['year'] ?? date('Y')) }}" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="status">Status Usulan</label>
                        <select id="status" name="status" class="form-control">
                            <option value="draft" {{ old('status', $editDraft['status'] ?? '') === 'draft' ? 'selected' : '' }}>Simpan Sebagai Draft</option>
                            <option value="submitted" {{ old('status', $editDraft['status'] ?? '') === 'submitted' ? 'selected' : '' }}>Ajukan Langsung (Kunci)</option>
                        </select>
                    </div>
                </div>

                {{-- ─── Section 2: Detail Item Draf ─────────────────────── --}}
                <h5 class="form-title text-sm mt-6">
                    <i class="fas fa-list-ol text-purple-700 mr-2"></i>
                    Daftar Barang yang Diusulkan
                </h5>

                <div style="overflow-x: auto;" class="mb-4">
                    <table class="items-table">
                        <thead>
                            <tr>
                                <th style="width: 130px;">Tipe Barang</th>
                                <th style="width: 220px;">Nama Barang</th>
                                <th style="width: 140px;">Harga Satuan (Rp)</th>
                                <th style="width: 80px;">Kuantitas</th>
                                <th style="width: 160px;">Link Pembelian</th>
                                <th style="width: 200px;">Menggantikan Barang Lama</th>
                                <th>Catatan</th>
                                <th id="aksiHeader" style="width: 50px; text-align: center;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="itemsContainer">
                            {{-- Baris akan dirender dinamis di sini oleh JavaScript --}}
                        </tbody>
                    </table>
                </div>

                <div class="flex justify-between items-center mb-6">
                    <button type="button" class="btn-add-item" onclick="addItemRow()">
                        <i class="fas fa-plus"></i> Tambah Baris Barang
                    </button>
                </div>

                {{-- ─── Section 3: Ringkasan & Submit ───────────────────── --}}
                <div class="total-summary-box">
                    <div>
                        <p class="text-xs text-slate-500 font-bold uppercase tracking-wider">Total Perkiraan Anggaran</p>
                        <h4 class="text-xl font-bold text-slate-800" id="grandTotalLabel">Rp 0</h4>
                    </div>
                    <div class="flex gap-3">
                        <a href="{{ route('kalab.procurement.index') }}" class="btn-secondary">
                            Batal
                        </a>
                        <button type="submit" class="btn-save">
                            <i class="fas fa-save"></i>
                            {{ $editDraft ? 'Simpan Perubahan' : 'Buat Draf' }}
                        </button>
                    </div>
                </div>

            </form>
        </div>
    </div>
</div>

<script>
// Data Aset Inventaris untuk Dropdown "Menggantikan Barang"
const assets = @json($assets);

// State items untuk edit mode
const initialItems = @json($items);

let itemIndex = 0;

// Jalankan ketika halaman selesai diload
document.addEventListener('DOMContentLoaded', () => {
    if (initialItems && initialItems.length > 0) {
        initialItems.forEach(item => addItemRow(item));
    } else {
        // Beri 1 baris kosong default jika buat baru
        addItemRow();
    }
});

// Fungsi Menambahkan Baris Item
function addItemRow(data = null) {
    const container = document.getElementById('itemsContainer');
    const tr = document.createElement('tr');
    tr.id = `item-row-${itemIndex}`;
    tr.className = 'item-row';

    // Opsi Dropdown Aset Pengganti
    let assetOptions = '<option value="">-- Pilih Aset --</option>';
    assets.forEach(a => {
        const selected = (data && data.replaced_asset_id == a.id) ? 'selected' : '';
        assetOptions += `<option value="${a.id}" ${selected}>${a.name} (${a.code || 'Tanpa Kode'})</option>`;
    });

    tr.innerHTML = `
        <td>
            <select name="items[${itemIndex}][item_type]" class="form-control" required>
                <option value="inventaris" ${(data && data.item_type === 'inventaris') ? 'selected' : ''}>Inventaris</option>
                <option value="bhp" ${(data && data.item_type === 'bhp') ? 'selected' : ''}>BHP</option>
            </select>
        </td>
        <td>
            <input type="text" name="items[${itemIndex}][name]" class="form-control" placeholder="Nama barang..." value="${data ? data.name : ''}" required>
        </td>
        <td>
            <input type="number" name="items[${itemIndex}][price]" class="form-control calc-trigger input-price" min="0" placeholder="0" value="${data ? data.price : '0'}" required>
        </td>
        <td>
            <input type="number" name="items[${itemIndex}][quantity]" class="form-control calc-trigger input-qty" min="1" placeholder="1" value="${data ? data.quantity : '1'}" required>
        </td>
        <td>
            <input type="url" name="items[${itemIndex}][purchase_link]" class="form-control" placeholder="https://..." value="${data ? (data.purchase_link || '') : ''}">
        </td>
        <td>
            <select name="items[${itemIndex}][replaced_asset_id]" class="form-control">
                ${assetOptions}
            </select>
        </td>
        <td>
            <input type="text" name="items[${itemIndex}][notes]" class="form-control" placeholder="Catatan/spesifikasi..." value="${data ? (data.notes || '') : ''}">
        </td>
        <td class="aksi-col" style="text-align: center;">
            <button type="button" class="btn-remove-item" onclick="removeItemRow(${itemIndex})">
                <i class="fas fa-trash"></i> Hapus
            </button>
        </td>
    `;

    container.appendChild(tr);

    // Pasang Event Listener untuk Update Total Harga
    tr.querySelectorAll('.calc-trigger').forEach(input => {
        input.addEventListener('input', calculateGrandTotal);
    });

    itemIndex++;
    calculateGrandTotal();
    updateRemoveButtonsVisibility();
}

// Fungsi Menghapus Baris Item
function removeItemRow(index) {
    const row = document.getElementById(`item-row-${index}`);
    if (row) {
        row.remove();
    }
    // Jika semua baris dihapus, beri baris kosong lagi
    const rows = document.querySelectorAll('.item-row');
    if (rows.length === 0) {
        addItemRow();
    } else {
        updateRemoveButtonsVisibility();
    }
    calculateGrandTotal();
}

// Menyembunyikan seluruh kolom aksi jika hanya ada 1 baris
function updateRemoveButtonsVisibility() {
    const rows = document.querySelectorAll('.item-row');
    const aksiHeader = document.getElementById('aksiHeader');
    const aksiCols = document.querySelectorAll('.aksi-col');

    if (rows.length <= 1) {
        if (aksiHeader) aksiHeader.classList.add('hidden');
        aksiCols.forEach(col => col.classList.add('hidden'));
    } else {
        if (aksiHeader) aksiHeader.classList.remove('hidden');
        aksiCols.forEach(col => col.classList.remove('hidden'));
    }
}

// Fungsi Kalkulasi Total Anggaran secara Real-time
function calculateGrandTotal() {
    let grandTotal = 0;
    const rows = document.querySelectorAll('.item-row');
    
    rows.forEach(row => {
        const price = parseFloat(row.querySelector('.input-price').value) || 0;
        const qty = parseInt(row.querySelector('.input-qty').value) || 0;
        grandTotal += price * qty;
    });

    // Format mata uang rupiah
    const formattedTotal = new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        maximumFractionDigits: 0
    }).format(grandTotal);

    document.getElementById('grandTotalLabel').textContent = formattedTotal;
}
</script>
@endsection
