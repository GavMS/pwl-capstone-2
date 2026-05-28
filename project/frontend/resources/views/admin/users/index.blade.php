@extends('dashboard.layout')
@section('title', 'Manajemen User')

@section('content')

<style>
/* ─── Page Styles ─────────────────────────────────── */
.page-header-card {
    background: #fff;
    border-radius: 1rem;
    box-shadow: 0 20px 27px 0 rgba(0,0,0,.05);
    padding: 1.5rem;
    margin-bottom: 1.5rem;
}
.page-title {
    font-size: 1.25rem;
    font-weight: 700;
    color: #344767;
    margin: 0 0 .25rem;
}
.page-subtitle {
    font-size: .875rem;
    color: #7b809a;
    margin: 0;
}
/* ─── Tambah User Button ──────────────────────────── */
.btn-add-user {
    display: inline-flex;
    align-items: center;
    gap: .5rem;
    padding: .6rem 1.25rem;
    font-size: .8125rem;
    font-weight: 600;
    color: #fff;
    background: linear-gradient(310deg, #7928ca, #ff007f);
    border: none;
    border-radius: .5rem;
    box-shadow: 0 4px 7px -1px rgba(0,0,0,.11), 0 2px 4px -1px rgba(0,0,0,.07);
    cursor: pointer;
    text-decoration: none;
    white-space: nowrap;
    transition: box-shadow .15s ease, transform .15s ease;
}
.btn-add-user:hover {
    box-shadow: 0 8px 25px -8px rgba(121,40,202,.7);
    transform: translateY(-1px);
    color: #fff;
}
/* ─── Alerts ──────────────────────────────────────── */
.alert-success {
    display: flex; align-items: center; gap: .75rem;
    padding: .875rem 1.25rem;
    background: #f0fdf4; border: 1px solid #bbf7d0;
    border-radius: .75rem; margin-bottom: 1rem;
    font-size: .875rem; font-weight: 600; color: #15803d;
}
.alert-error {
    display: flex; align-items: center; gap: .75rem;
    padding: .875rem 1.25rem;
    background: #fef2f2; border: 1px solid #fecaca;
    border-radius: .75rem; margin-bottom: 1rem;
    font-size: .875rem; font-weight: 600; color: #dc2626;
}
/* ─── Table Card ──────────────────────────────────── */
.table-card {
    background: #fff;
    border-radius: 1rem;
    box-shadow: 0 20px 27px 0 rgba(0,0,0,.05);
    overflow: hidden;
}
.table-card-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1rem 1.5rem;
    border-bottom: 1px solid #f0f2f5;
    flex-wrap: wrap;
    gap: .75rem;
}
.table-card-title {
    font-size: 1rem;
    font-weight: 700;
    color: #344767;
    margin: 0;
}
/* ─── Search ──────────────────────────────────────── */
.search-wrap {
    position: relative;
}
.search-wrap .search-icon {
    position: absolute;
    left: .75rem; top: 50%;
    transform: translateY(-50%);
    color: #adb5bd; font-size: .75rem;
    pointer-events: none;
}
.search-wrap input {
    padding: .5rem .875rem .5rem 2.2rem;
    font-size: .8125rem;
    border: 1px solid #d2d6da;
    border-radius: .5rem;
    color: #344767;
    outline: none;
    width: 220px;
    transition: border-color .2s, box-shadow .2s;
}
.search-wrap input:focus {
    border-color: #7928ca;
    box-shadow: 0 0 0 2px rgba(121,40,202,.15);
}
/* ─── Table ───────────────────────────────────────── */
.users-table { width: 100%; border-collapse: collapse; }
.users-table thead th {
    padding: .75rem 1.25rem;
    font-size: .65rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .05em;
    color: #adb5bd;
    border-bottom: 1px solid #f0f2f5;
    white-space: nowrap;
    background: #fff;
}
.users-table tbody td {
    padding: .875rem 1.25rem;
    border-bottom: 1px solid #f0f2f5;
    vertical-align: middle;
}
.users-table tbody tr:last-child td { border-bottom: none; }
.users-table tbody tr:hover { background: #fafbfc; }
/* ─── User Avatar + Name ──────────────────────────── */
.user-cell { display: flex; align-items: center; gap: .75rem; }
.user-avatar {
    width: 2.25rem; height: 2.25rem;
    border-radius: .5rem;
    background: linear-gradient(310deg, #7928ca, #ff007f);
    display: flex; align-items: center; justify-content: center;
    color: #fff; font-size: .8125rem; font-weight: 700;
    flex-shrink: 0;
    box-shadow: 0 4px 6px -1px rgba(121,40,202,.4);
}
.user-name { font-size: .875rem; font-weight: 600; color: #344767; margin: 0; }
.user-email { font-size: .8125rem; color: #7b809a; margin: 0; }
.cell-text { font-size: .875rem; color: #7b809a; }
.cell-date { font-size: .8rem; color: #adb5bd; white-space: nowrap; }
/* ─── Role Badge — Soft Pastel, Seragam ───────────── */
.role-badge {
    display: inline-flex;
    align-items: center;
    gap: .35rem;
    padding: .3rem .85rem;
    border-radius: .45rem;
    font-size: .7rem;
    font-weight: 700;
    white-space: nowrap;
    letter-spacing: .01em;
    border: 1.5px solid transparent;
}
.role-badge .rb-dot {
    width: 6px; height: 6px;
    border-radius: 50%;
    display: inline-block;
    flex-shrink: 0;
}
/* Soft pastel per-role */
.role-administrator { background:#f3e8ff; color:#7928ca; border-color:#e9d5ff; }
.role-administrator .rb-dot { background:#7928ca; }
.role-kepala        { background:#eff6ff; color:#1d4ed8; border-color:#bfdbfe; }
.role-kepala        .rb-dot { background:#2152ff; }
.role-kaprodi       { background:#fff7ed; color:#c2410c; border-color:#fed7aa; }
.role-kaprodi       .rb-dot { background:#ea580c; }
.role-stafadmin     { background:#f0fdf4; color:#15803d; border-color:#bbf7d0; }
.role-stafadmin     .rb-dot { background:#16a34a; }
.role-staflab       { background:#f8fafc; color:#475569; border-color:#cbd5e1; }
.role-staflab       .rb-dot { background:#64748b; }
/* ─── Action Buttons ──────────────────────────────── */
.action-group { display: flex; align-items: center; gap: .4rem; }
.btn-edit, .btn-delete {
    display: inline-flex; align-items: center; gap: .3rem;
    padding: .35rem .85rem;
    font-size: .75rem; font-weight: 600;
    color: #fff; border: none; border-radius: .45rem;
    cursor: pointer; text-decoration: none;
    transition: transform .15s, box-shadow .15s;
}
.btn-edit   { background: linear-gradient(310deg, #2152ff, #21d4fd); box-shadow: 0 3px 5px -1px rgba(33,82,255,.4); }
.btn-delete { background: linear-gradient(310deg, #ea0606, #ff667c); box-shadow: 0 3px 5px -1px rgba(234,6,6,.4); }
.btn-edit:hover, .btn-delete:hover { transform: translateY(-1px); box-shadow: 0 5px 10px -2px rgba(0,0,0,.3); color:#fff; }
/* ─── Table Footer ────────────────────────────────── */
.table-footer {
    padding: .75rem 1.5rem;
    border-top: 1px solid #f0f2f5;
    font-size: .8rem; color: #adb5bd;
}
/* ─── Modal Overlay & Box ─────────────────────────── */
#modalOverlay {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(15, 23, 42, .55);
    z-index: 9000;
    backdrop-filter: blur(2px);
}
#deleteModalBox {
    display: none;
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%) scale(.95);
    z-index: 9001;
    width: 90%;
    max-width: 460px;
    background: #fff;
    border-radius: 1.25rem;
    box-shadow: 0 25px 50px -12px rgba(0,0,0,.3);
    overflow: hidden;
    transition: transform .2s ease, opacity .2s ease;
    opacity: 0;
}
#deleteModalBox.is-open {
    transform: translate(-50%, -50%) scale(1);
    opacity: 1;
}
.modal-header {
    display: flex; align-items: center; gap: .875rem;
    padding: 1.25rem 1.5rem;
    border-bottom: 1px solid #f0f2f5;
}
.modal-icon-wrap {
    width: 2.75rem; height: 2.75rem; border-radius: .75rem; flex-shrink: 0;
    background: linear-gradient(310deg, #ea0606, #ff667c);
    display: flex; align-items: center; justify-content: center;
    box-shadow: 0 4px 6px -1px rgba(234,6,6,.4);
}
.modal-icon-wrap i { color: #fff; font-size: .875rem; }
.modal-header-title { font-size: 1rem; font-weight: 700; color: #344767; margin: 0 0 .15rem; }
.modal-header-sub   { font-size: .775rem; color: #7b809a; margin: 0; }
.modal-body { padding: 1.25rem 1.5rem; }
/* Loading Spinner */
.modal-loading { text-align: center; padding: 1.5rem 0; }
.modal-loading .spinner {
    width: 2.5rem; height: 2.5rem; border-radius: 50%;
    border: 3px solid #f0f2f5;
    border-top-color: #7928ca;
    animation: spin .7s linear infinite;
    margin: 0 auto .75rem;
}
@keyframes spin { to { transform: rotate(360deg); } }
.modal-loading p { font-size: .8125rem; color: #7b809a; margin: 0; }
/* User Info Box */
.modal-user-card {
    background: #f8f9fa;
    border-radius: .75rem;
    padding: 1rem;
    margin-bottom: 1rem;
}
.modal-user-card-label {
    font-size: .625rem; font-weight: 700;
    text-transform: uppercase; letter-spacing: .08em;
    color: #adb5bd; margin: 0 0 .75rem;
}
.modal-user-info { display: flex; align-items: center; gap: .75rem; }
.modal-user-avatar {
    width: 2.5rem; height: 2.5rem; border-radius: .5rem;
    background: linear-gradient(310deg, #7928ca, #ff007f);
    display: flex; align-items: center; justify-content: center;
    color: #fff; font-size: .875rem; font-weight: 700; flex-shrink: 0;
}
.modal-user-name  { font-size: .875rem; font-weight: 700; color: #344767; margin: 0 0 .1rem; }
.modal-user-email { font-size: .775rem; color: #7b809a; margin: 0; }
.modal-user-role  { margin-top: .625rem; display: flex; align-items: center; gap: .5rem; font-size: .8rem; color: #7b809a; }
/* Alert boxes in modal */
.modal-alert {
    display: flex; align-items: flex-start; gap: .75rem;
    padding: .875rem 1rem;
    border-radius: .75rem;
    margin-bottom: .75rem;
    font-size: .8125rem;
}
.modal-alert i { margin-top: .1rem; font-size: .875rem; }
.modal-alert-title { font-weight: 700; margin: 0 0 .2rem; }
.modal-alert-text  { margin: 0; }
.modal-alert-warn  { background: #fffbeb; border: 1px solid #fde68a; color: #92400e; }
.modal-alert-warn  i { color: #d97706; }
.modal-alert-danger { background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; }
.modal-alert-danger i { color: #dc2626; }
.modal-dep-list { margin: .375rem 0 0 1rem; padding: 0; font-size: .775rem; }
.modal-dep-list li { margin-bottom: .2rem; }
/* Modal Footer */
.modal-footer {
    display: flex; align-items: center; justify-content: flex-end; gap: .625rem;
    padding: 1rem 1.5rem;
    border-top: 1px solid #f0f2f5;
}
.btn-cancel {
    padding: .5rem 1.125rem;
    font-size: .8125rem; font-weight: 600;
    color: #7b809a; background: #f0f2f5;
    border: none; border-radius: .5rem;
    cursor: pointer; transition: background .15s;
}
.btn-cancel:hover { background: #e2e8f0; }
.btn-confirm-delete {
    display: inline-flex; align-items: center; gap: .375rem;
    padding: .5rem 1.25rem;
    font-size: .8125rem; font-weight: 600; color: #fff;
    background: linear-gradient(310deg, #ea0606, #ff667c);
    border: none; border-radius: .5rem;
    box-shadow: 0 4px 7px -1px rgba(234,6,6,.4);
    cursor: pointer; transition: box-shadow .15s, transform .15s;
}
.btn-confirm-delete:hover { transform: translateY(-1px); box-shadow: 0 6px 15px -3px rgba(234,6,6,.5); }
/* ─── Empty State ─────────────────────────────────── */
.empty-state { text-align: center; padding: 3rem 1.5rem; }
.empty-state i { font-size: 2.5rem; color: #d2d6da; display: block; margin-bottom: 1rem; }
.empty-state p { font-size: .875rem; color: #adb5bd; margin: 0; }
</style>

<div class="flex flex-wrap -mx-3">

    {{-- ─── Header ────────────────────────────────── --}}
    <div class="w-full px-3 mb-4">
        <div class="page-header-card">
            <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:.75rem;">
                <div>
                    <h4 class="page-title">Manajemen Pengguna</h4>
                    <p class="page-subtitle">Kelola akun pengguna sistem beserta hak akses rolenya.</p>
                </div>
                <a href="{{ route('admin.users.create') }}" class="btn-add-user">
                    <i class="fas fa-user-plus"></i>
                    Tambah User
                </a>
            </div>
        </div>
    </div>

    {{-- ─── Alert Success ──────────────────────────── --}}
    @if(session('success'))
    <div class="w-full px-3 mb-3">
        <div class="alert-success">
            <i class="fas fa-check-circle"></i>
            {{ session('success') }}
        </div>
    </div>
    @endif

    {{-- ─── Alert Error ────────────────────────────── --}}
    @if($errors->any() || isset($error))
    <div class="w-full px-3 mb-3">
        <div class="alert-error">
            <i class="fas fa-exclamation-circle"></i>
            {{ $errors->any() ? $errors->first() : $error }}
        </div>
    </div>
    @endif

    {{-- ─── Table Card ─────────────────────────────── --}}
    <div class="w-full px-3">
        <div class="table-card">

            {{-- Card Header --}}
            <div class="table-card-header">
                <h6 class="table-card-title">Daftar Akun Pengguna</h6>
                <div class="search-wrap">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" id="searchInput" placeholder="Cari nama / email..." />
                </div>
            </div>

            {{-- Table --}}
            <div style="overflow-x: auto;">
                <table class="users-table" id="usersTable">
                    <thead>
                        <tr>
                            <th style="width:50px; text-align:center;">No</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th style="text-align:center;">Role</th>
                            <th style="text-align:center;">Dibuat</th>
                            <th style="text-align:center; width:140px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="usersTableBody">
                        @forelse($users as $index => $u)
                        @php
                            $roleClass = match($u['role'] ?? '') {
                                'Administrator'       => 'role-administrator',
                                'Kepala Laboratorium' => 'role-kepala',
                                'Ketua Program Studi' => 'role-kaprodi',
                                'Staf Administrasi'   => 'role-stafadmin',
                                'Staf Laboratorium'   => 'role-staflab',
                                default               => 'role-staflab',
                            };
                            $roleIcon = match($u['role'] ?? '') {
                                'Administrator'       => 'fa-user-shield',
                                'Kepala Laboratorium' => 'fa-flask',
                                'Ketua Program Studi' => 'fa-graduation-cap',
                                'Staf Administrasi'   => 'fa-file-alt',
                                'Staf Laboratorium'   => 'fa-tools',
                                default               => 'fa-user',
                            };
                        @endphp
                        <tr class="user-row">
                            <td style="text-align:center;">
                                <span class="cell-text" style="font-weight:600;">{{ $index + 1 }}</span>
                            </td>
                            <td>
                                <div class="user-cell">
                                    <div class="user-avatar">{{ strtoupper(substr($u['name'], 0, 1)) }}</div>
                                    <div>
                                        <p class="user-name user-name-text">{{ $u['name'] }}</p>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="cell-text user-email-text">{{ $u['email'] }}</span>
                            </td>
                            <td style="text-align:center;">
                                <span class="role-badge {{ $roleClass }}">
                                    <span class="rb-dot"></span>
                                    {{ $u['role'] ?? '-' }}
                                </span>
                            </td>
                            <td style="text-align:center;">
                                <span class="cell-date">
                                    {{ isset($u['created_at']) ? \Carbon\Carbon::parse($u['created_at'])->format('d M Y') : '-' }}
                                </span>
                            </td>
                            <td style="text-align:center;">
                                <div class="action-group" style="justify-content:center;">
                                    <a href="{{ route('admin.users.edit', $u['id']) }}" class="btn-edit">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <button type="button" class="btn-delete"
                                        onclick="openDeleteModal({{ $u['id'] }}, '{{ addslashes($u['name']) }}', '{{ addslashes($u['email']) }}', '{{ addslashes($u['role'] ?? '') }}')">
                                        <i class="fas fa-trash-alt"></i> Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6">
                                <div class="empty-state">
                                    <i class="fas fa-users"></i>
                                    <p>Belum ada data pengguna.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Footer --}}
            <div class="table-footer">
                Total: <strong style="color:#344767;">{{ count($users) }}</strong> pengguna
            </div>
        </div>
    </div>

</div>

{{-- ════════════════════════════════════════════════
     SMART DELETE MODAL — di luar flow, truly fixed
════════════════════════════════════════════════ --}}
<div id="modalOverlay" onclick="closeDeleteModal()"></div>

<div id="deleteModalBox">
    {{-- Header --}}
    <div class="modal-header">
        <div class="modal-icon-wrap">
            <i class="fas fa-exclamation-triangle"></i>
        </div>
        <div>
            <p class="modal-header-title">Konfirmasi Hapus User</p>
            <p class="modal-header-sub">Tindakan ini tidak dapat dibatalkan</p>
        </div>
    </div>

    {{-- Loading --}}
    <div id="deleteLoading" class="modal-body">
        <div class="modal-loading">
            <div class="spinner"></div>
            <p>Memeriksa keamanan data...</p>
        </div>
    </div>

    {{-- Content (after check) --}}
    <div id="deleteContent" style="display:none;">
        <div class="modal-body">
            {{-- User Info --}}
            <div class="modal-user-card">
                <p class="modal-user-card-label">Data Pengguna</p>
                <div class="modal-user-info">
                    <div class="modal-user-avatar" id="modalInitial">A</div>
                    <div>
                        <p class="modal-user-name" id="modalName">-</p>
                        <p class="modal-user-email" id="modalEmail">-</p>
                    </div>
                </div>
                <div class="modal-user-role">
                    <i class="fas fa-shield-alt" style="color:#adb5bd;font-size:.75rem;"></i>
                    <span>Role: <strong id="modalRole" style="color:#344767;">-</strong></span>
                </div>
            </div>

            {{-- Blocked --}}
            <div id="deleteDenied" style="display:none;">
                <div class="modal-alert modal-alert-warn">
                    <i class="fas fa-lock"></i>
                    <div>
                        <p class="modal-alert-title">Penghapusan Diblokir</p>
                        <p class="modal-alert-text">User ini masih memiliki data terkait yang aktif:</p>
                        <ul class="modal-dep-list" id="depList"></ul>
                    </div>
                </div>
            </div>

            {{-- Allowed --}}
            <div id="deleteAllowed" style="display:none;">
                <div class="modal-alert modal-alert-danger">
                    <i class="fas fa-trash-alt"></i>
                    <div>
                        <p class="modal-alert-title">Yakin ingin menghapus user ini?</p>
                        <p class="modal-alert-text">Semua data login user akan dihapus permanen dari sistem.</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Footer --}}
        <div class="modal-footer">
            <button type="button" class="btn-cancel" onclick="closeDeleteModal()">Batal</button>
            <form id="deleteForm" method="POST" style="display:none; margin:0;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-confirm-delete">
                    <i class="fas fa-trash-alt"></i> Ya, Hapus
                </button>
            </form>
        </div>
    </div>
</div>

<script>
// ── Search ───────────────────────────────────────────
document.getElementById('searchInput').addEventListener('input', function () {
    const q = this.value.toLowerCase();
    document.querySelectorAll('#usersTableBody .user-row').forEach(row => {
        const name  = (row.querySelector('.user-name-text')?.textContent  ?? '').toLowerCase();
        const email = (row.querySelector('.user-email-text')?.textContent ?? '').toLowerCase();
        row.style.display = (name.includes(q) || email.includes(q)) ? '' : 'none';
    });
});

// ── Smart Delete ─────────────────────────────────────
function openDeleteModal(id, name, email, role) {
    const overlay  = document.getElementById('modalOverlay');
    const modalBox = document.getElementById('deleteModalBox');

    // Reset
    document.getElementById('deleteLoading').style.display  = 'block';
    document.getElementById('deleteContent').style.display  = 'none';
    document.getElementById('deleteDenied').style.display   = 'none';
    document.getElementById('deleteAllowed').style.display  = 'none';
    document.getElementById('deleteForm').style.display     = 'none';

    // Show
    overlay.style.display  = 'block';
    modalBox.style.display = 'block';
    setTimeout(() => modalBox.classList.add('is-open'), 10);

    // AJAX check
    fetch(`/admin/users/${id}/check-delete`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(data => {
        const u = data.user ?? {};
        document.getElementById('modalInitial').textContent = (u.name ?? name).charAt(0).toUpperCase();
        document.getElementById('modalName').textContent    = u.name  ?? name;
        document.getElementById('modalEmail').textContent   = u.email ?? email;
        document.getElementById('modalRole').textContent    = u.role  ?? role;

        document.getElementById('deleteLoading').style.display = 'none';
        document.getElementById('deleteContent').style.display = 'block';

        if (data.canDelete) {
            document.getElementById('deleteAllowed').style.display = 'block';
            const form = document.getElementById('deleteForm');
            form.action = `/admin/users/${id}`;
            form.style.display = 'block';
        } else {
            document.getElementById('deleteDenied').style.display = 'block';
            const list = document.getElementById('depList');
            list.innerHTML = '';
            (data.dependencies ?? []).forEach(dep => {
                const li = document.createElement('li');
                li.textContent = `${dep.label} (${dep.count} data)`;
                list.appendChild(li);
            });
        }
    })
    .catch(() => {
        document.getElementById('deleteLoading').style.display = 'none';
        document.getElementById('deleteContent').style.display = 'block';
        document.getElementById('deleteDenied').style.display  = 'block';
        document.getElementById('depList').innerHTML = '<li>Gagal memeriksa data. Coba lagi.</li>';
    });
}

function closeDeleteModal() {
    const overlay  = document.getElementById('modalOverlay');
    const modalBox = document.getElementById('deleteModalBox');
    modalBox.classList.remove('is-open');
    setTimeout(() => {
        overlay.style.display  = 'none';
        modalBox.style.display = 'none';
    }, 200);
}
</script>
@endsection
