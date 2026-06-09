@extends('dashboard.layout')
@section('title', 'Labeling Inventaris')
@section('page_title', 'Labeling Inventaris')

@section('content')
<style>
.page-header-card { background:#fff; border-radius:1rem; box-shadow:0 20px 27px 0 rgba(0,0,0,.05); padding:1.5rem; margin-bottom:1.5rem; }
.page-title { font-size:1.25rem; font-weight:700; color:#344767; margin:0 0 .25rem; }
.page-subtitle { font-size:.875rem; color:#7b809a; margin:0; }
.alert-box { padding:.875rem 1.25rem; border-radius:.75rem; font-size:.875rem; font-weight:600; display:flex; align-items:center; gap:.5rem; margin-bottom:1rem; }
.alert-success { background:#dcfce7; border:1px solid #bbf7d0; color:#15803d; }
.alert-error   { background:#fef2f2; border:1px solid #fecaca; color:#dc2626; }
.toolbar-card { background:#fff; border-radius:1rem; box-shadow:0 20px 27px 0 rgba(0,0,0,.05); padding:1.25rem; margin-bottom:1.5rem; display:flex; gap:.75rem; flex-wrap:wrap; align-items:center; justify-content:space-between; }
.search-input { padding:.5rem .875rem; font-size:.8125rem; border:1px solid #d2d6da; border-radius:.5rem; color:#344767; outline:none; width:280px; }
.select-filter { padding:.5rem .875rem; font-size:.8125rem; border:1px solid #d2d6da; border-radius:.5rem; color:#344767; background:#fff; cursor:pointer; }
.table-card { background:#fff; border-radius:1rem; box-shadow:0 20px 27px 0 rgba(0,0,0,.05); overflow:hidden; }
.inv-table { width:100%; border-collapse:collapse; }
.inv-table thead th { padding:.85rem 1rem; font-size:.65rem; font-weight:700; text-transform:uppercase; letter-spacing:.05em; color:#adb5bd; border-bottom:1px solid #f0f2f5; text-align:left; white-space:nowrap; }
.inv-table tbody td { padding:.8rem 1rem; border-bottom:1px solid #f0f2f5; vertical-align:middle; color:#475569; font-size:.85rem; }
.inv-table tbody tr:last-child td { border-bottom:none; }
.inv-table tbody tr:hover { background:#fafbfc; }
.item-title { font-size:.875rem; font-weight:600; color:#344767; margin:0; }
.item-draft-link { font-size:.75rem; color:#2152ff; text-decoration:none; font-weight:600; }
.item-draft-link:hover { text-decoration:underline; }
.cell-input { padding:.4rem .6rem; font-size:.78rem; border:1px solid #d2d6da; border-radius:.4rem; color:#495057; outline:none; }
.cell-input:focus { border-color:#2152ff; box-shadow:0 0 0 2px rgba(33,82,255,.12); }
.label-input { width:150px; font-family:'Courier New',monospace; font-weight:600; }
.btn-gen { width:28px; height:28px; background:#e2e8f0; color:#475569; border:none; border-radius:.375rem; cursor:pointer; font-size:.72rem; }
.btn-gen:hover { background:#cbd5e1; }
.btn-save { display:inline-flex; align-items:center; gap:.3rem; padding:.4rem .85rem; font-size:.75rem; font-weight:700; color:#fff; background:linear-gradient(310deg,#17ad37,#98ec2c); border:none; border-radius:.45rem; cursor:pointer; }
.btn-save:hover { opacity:.9; }
.qr-prev img { width:46px; height:46px; display:block; border-radius:.25rem; }
.muted { color:#cbd5e1; }
.status-pill { display:inline-flex; align-items:center; gap:.3rem; padding:.2rem .6rem; border-radius:.375rem; font-size:.68rem; font-weight:700; }
.status-recv { background:#dcfce7; color:#15803d; }
.status-wait { background:#fef3c7; color:#d97706; }
.empty-state { padding:3rem 1rem; text-align:center; color:#adb5bd; }
.hidden-form { display:none; }
</style>

<div class="flex flex-wrap -mx-3">
    <div class="w-full px-3">
        <div class="page-header-card">
            <h4 class="page-title"><i class="fas fa-qrcode mr-2" style="color:#7928ca;"></i> Pencetakan & Labeling Inventaris</h4>
            <p class="page-subtitle">Beri nomor label, QR Code, dan tanggal penerimaan untuk aset hasil pengadaan yang disetujui. Data tersimpan di database.</p>
        </div>
    </div>

    {{-- Alerts --}}
    @if(session('success'))
    <div class="w-full px-3"><div class="alert-box alert-success"><i class="fas fa-check-circle"></i>{{ session('success') }}</div></div>
    @endif
    @if($errors->any())
    <div class="w-full px-3"><div class="alert-box alert-error"><i class="fas fa-exclamation-circle"></i>{{ $errors->first() }}</div></div>
    @endif
    @if(isset($error) && $error)
    <div class="w-full px-3"><div class="alert-box alert-error"><i class="fas fa-exclamation-circle"></i>{{ $error }}</div></div>
    @endif

    {{-- Toolbar --}}
    <div class="w-full px-3">
        <div class="toolbar-card">
            <div style="display:flex; gap:.75rem; flex-wrap:wrap; align-items:center;">
                <input type="text" id="searchInput" class="search-input" placeholder="Cari barang atau pengadaan..." onkeyup="filterRows()">
                <select id="yearFilter" class="select-filter" onchange="filterRows()">
                    <option value="all">Semua Tahun</option>
                    @foreach(collect($assets)->pluck('draft_year')->unique()->sort()->values() as $yr)
                        <option value="{{ $yr }}">{{ $yr }}</option>
                    @endforeach
                </select>
            </div>
            <div style="font-size:.75rem; color:#adb5bd; font-weight:600;">Total: <span id="displayedCount">{{ count($assets) }}</span> aset</div>
        </div>
    </div>

    {{-- Table --}}
    <div class="w-full px-3">
        <div class="table-card">
            <div style="overflow-x:auto;">
                <table class="inv-table">
                    <thead>
                        <tr>
                            <th style="width:46px; text-align:center;">No</th>
                            <th style="width:80px;">Tahun</th>
                            <th style="min-width:220px;">Nama Barang / Pengadaan</th>
                            <th style="width:60px; text-align:center;">Qty</th>
                            <th style="width:150px;">Tgl. Diterima</th>
                            <th style="width:210px;">Nomor Label</th>
                            <th style="width:70px; text-align:center;">QR</th>
                            <th style="width:130px; text-align:center;">Status</th>
                            <th style="width:90px; text-align:center;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($assets as $idx => $a)
                        @php $aid = $a['id']; $received = !empty($a['received_date']); @endphp
                        <tr class="asset-row"
                            data-year="{{ $a['draft_year'] }}"
                            data-name="{{ strtolower($a['name'].' '.($a['draft_title'] ?? '')) }}">
                            <td style="text-align:center; font-weight:600;">{{ $idx + 1 }}</td>
                            <td style="font-weight:600; color:#344767;">{{ $a['draft_year'] }}</td>
                            <td>
                                <p class="item-title">{{ $a['name'] }}</p>
                                <a href="{{ route('stafadmin.procurement.show', $a['draft_id']) }}" class="item-draft-link">
                                    <i class="fas fa-file-invoice"></i> {{ $a['draft_title'] }}
                                </a>
                                @if(!empty($a['replaced_asset_name']))
                                <div style="font-size:.7rem; color:#f59e0b; margin-top:.15rem;">
                                    <i class="fas fa-exchange-alt"></i> Menggantikan: {{ $a['replaced_asset_name'] }}
                                </div>
                                @endif
                            </td>
                            <td style="text-align:center;">
                                <span style="background:#f1f5f9; padding:.2rem .55rem; border-radius:.35rem; font-weight:700;">{{ $a['quantity'] ?? 1 }}</span>
                            </td>
                            <td>
                                <input type="date" name="received_date" form="lf-{{ $aid }}" class="cell-input"
                                       value="{{ $a['received_date'] ?? '' }}" style="width:135px;">
                            </td>
                            <td>
                                <div style="display:flex; align-items:center; gap:.35rem;">
                                    <input type="text" name="label_number" form="lf-{{ $aid }}"
                                           id="label-{{ $aid }}" class="cell-input label-input label-trigger"
                                           data-id="{{ $aid }}" data-year="{{ $a['draft_year'] }}"
                                           value="{{ $a['label_number'] ?? '' }}"
                                           placeholder="INV-{{ $a['draft_year'] }}-..." autocomplete="off">
                                    <button type="button" class="btn-gen" title="Auto-generate"
                                            onclick="genLabel({{ $aid }}, {{ $a['draft_year'] }})"><i class="fas fa-magic"></i></button>
                                </div>
                            </td>
                            <td style="text-align:center;">
                                <div class="qr-prev" id="qrprev-{{ $aid }}">
                                    @if(!empty($a['qr_path']))
                                        <img src="{{ $a['qr_path'] }}" alt="QR">
                                    @else
                                        <span class="muted">—</span>
                                    @endif
                                </div>
                            </td>
                            <td style="text-align:center;">
                                <span class="status-pill {{ $received ? 'status-recv' : 'status-wait' }}">
                                    {{ $received ? 'Diterima' : 'Belum Diterima' }}
                                </span>
                            </td>
                            <td style="text-align:center;">
                                <button type="submit" form="lf-{{ $aid }}" class="btn-save"><i class="fas fa-save"></i> Simpan</button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9">
                                <div class="empty-state">
                                    <i class="fas fa-box-open" style="font-size:2.5rem; color:#cbd5e1; display:block; margin-bottom:.75rem;"></i>
                                    <p>Belum ada aset inventaris dari pengadaan yang difinalisasi.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Form tersembunyi per-aset (CSRF + hidden QR data url). Input di tabel terhubung lewat atribut form="lf-{id}". --}}
@foreach($assets as $a)
<form id="lf-{{ $a['id'] }}" class="hidden-form" method="POST" action="{{ route('stafadmin.inventaris.label', $a['id']) }}">
    @csrf
    <input type="hidden" name="qr_data_url" id="qr-data-{{ $a['id'] }}">
</form>
@endforeach

<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
// Generate QR -> PNG dataURL (sinkron via canvas)
function genQRDataUrl(text) {
    if (!text) return '';
    const tmp = document.createElement('div');
    tmp.style.display = 'none';
    document.body.appendChild(tmp);
    new QRCode(tmp, { text: text, width: 200, height: 200, correctLevel: QRCode.CorrectLevel.H });
    let url = '';
    const canvas = tmp.querySelector('canvas');
    if (canvas) { url = canvas.toDataURL('image/png'); }
    else { const img = tmp.querySelector('img'); if (img) url = img.src; }
    document.body.removeChild(tmp);
    return url;
}

// Sinkronkan QR (hidden + preview) dari nilai label baris tsb
function refreshRow(id) {
    const input  = document.getElementById('label-' + id);
    const hidden = document.getElementById('qr-data-' + id);
    const prev   = document.getElementById('qrprev-' + id);
    if (!input || !hidden) return;
    const val = input.value.trim();
    if (val) {
        const url = genQRDataUrl(val);
        hidden.value = url;
        if (prev) prev.innerHTML = '<img src="' + url + '" alt="QR">';
    } else {
        hidden.value = '';
        if (prev) prev.innerHTML = '<span class="muted">&mdash;</span>';
    }
}

function genLabel(id, year) {
    const input = document.getElementById('label-' + id);
    if (!input) return;
    input.value = 'INV-' + year + '-' + String(id).padStart(3, '0');
    refreshRow(id);
}

document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.label-trigger').forEach(function (inp) {
        const id = inp.dataset.id;
        inp.addEventListener('input', function () { refreshRow(id); });
        // Untuk label yang sudah tersimpan: isi hidden QR agar re-save tetap menyertakan QR
        if (inp.value.trim() !== '') {
            const hidden = document.getElementById('qr-data-' + id);
            if (hidden) hidden.value = genQRDataUrl(inp.value.trim());
        }
    });
});

// Filter pencarian + tahun
function filterRows() {
    const q    = document.getElementById('searchInput').value.toLowerCase();
    const year = document.getElementById('yearFilter').value;
    let count = 0;
    document.querySelectorAll('.asset-row').forEach(function (row) {
        const matchQ    = row.dataset.name.includes(q);
        const matchYear = (year === 'all') || (row.dataset.year === year);
        if (matchQ && matchYear) { row.style.display = ''; count++; }
        else { row.style.display = 'none'; }
    });
    document.getElementById('displayedCount').textContent = count;
}
</script>
@endsection
