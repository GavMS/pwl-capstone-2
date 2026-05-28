@extends('dashboard.layout')
@section('title', 'Manajemen Pengguna')
@section('page_title', 'Manajemen Pengguna')

@section('content')

<style>
/* ─── Page Header ─────────────────────────────── */
.btn-add {
    display: inline-flex; align-items: center; gap: .5rem;
    padding: .6rem 1.25rem; font-size: .8125rem; font-weight: 600;
    color: #fff; background: linear-gradient(310deg, #7928ca, #ff007f);
    border: none; border-radius: .5rem;
    box-shadow: 0 4px 7px -1px rgba(0,0,0,.11), 0 2px 4px -1px rgba(0,0,0,.07);
    cursor: pointer; text-decoration: none; white-space: nowrap;
    transition: box-shadow .15s, transform .15s;
}
.btn-add:hover { box-shadow: 0 8px 25px -8px rgba(121,40,202,.7); transform: translateY(-1px); color: #fff; }

/* ─── Alerts ──────────────────────────────────── */
.alert-success {
    display: flex; align-items: center; gap: .75rem;
    padding: .875rem 1.25rem; background: #f0fdf4; border: 1px solid #bbf7d0;
    border-radius: .75rem; margin-bottom: 1rem; font-size: .875rem; font-weight: 600; color: #15803d;
}
.alert-error {
    display: flex; align-items: center; gap: .75rem;
    padding: .875rem 1.25rem; background: #fef2f2; border: 1px solid #fecaca;
    border-radius: .75rem; margin-bottom: 1rem; font-size: .875rem; font-weight: 600; color: #dc2626;
}

/* ─── Filter Row ──────────────────────────────── */
.filter-row {
    display: flex; align-items: center; justify-content: space-between;
    flex-wrap: wrap; gap: .75rem; margin-bottom: 1rem;
}
.filter-left { display: flex; align-items: center; gap: .625rem; flex-wrap: wrap; }
.search-wrap { position: relative; }
.search-wrap .search-icon {
    position: absolute; left: .875rem; top: 50%; transform: translateY(-50%);
    color: #94a3b8; font-size: .8125rem; pointer-events: none;
    line-height: 1;
}
.search-wrap input {
    padding: .55rem .875rem .55rem 2.35rem; font-size: .8125rem;
    border: 1px solid #d2d6da; border-radius: .5rem; color: #344767;
    outline: none; width: 260px; background: #fff;
    transition: border-color .2s, box-shadow .2s;
}
.search-wrap input:focus { border-color: #7928ca; box-shadow: 0 0 0 2px rgba(121,40,202,.15); }
.role-filter {
    padding: .5rem .875rem; font-size: .8125rem;
    border: 1px solid #d2d6da; border-radius: .5rem; color: #344767;
    outline: none; background: #fff; cursor: pointer;
    transition: border-color .2s;
}
.role-filter:focus { border-color: #7928ca; }
.count-label { font-size: .8125rem; color: #adb5bd; white-space: nowrap; }

/* ─── Table Card ──────────────────────────────── */
.table-card {
    background: #fff; border-radius: 1rem;
    border: 1px solid #eef0f5;
    box-shadow: 0 1px 3px rgba(0,0,0,.04), 0 8px 20px -12px rgba(0,0,0,.08);
    overflow: hidden;
}

/* ─── Table ───────────────────────────────────── */
.users-table { width: 100%; border-collapse: collapse; }
.users-table thead th {
    padding: .75rem 1.25rem; font-size: .65rem; font-weight: 700;
    text-transform: uppercase; letter-spacing: .05em; color: #adb5bd;
    border-bottom: 1px solid #f0f2f5; white-space: nowrap; background: #fff;
    text-align: left;
}
.users-table tbody td {
    padding: .875rem 1.25rem; border-bottom: 1px solid #f0f2f5; vertical-align: middle;
}
.users-table tbody tr:last-child td { border-bottom: none; }
.users-table tbody tr:hover { background: #fafbfc; }

/* ─── User Cell ───────────────────────────────── */
.user-cell { display: flex; align-items: center; gap: .75rem; }
.user-avatar {
    width: 2.25rem; height: 2.25rem; border-radius: 50%;
    background: linear-gradient(310deg, #7928ca, #ff007f);
    display: flex; align-items: center; justify-content: center;
    color: #fff; font-size: .8125rem; font-weight: 700; flex-shrink: 0;
    box-shadow: 0 4px 6px -1px rgba(121,40,202,.4);
}
.user-name { font-size: .875rem; font-weight: 600; color: #344767; margin: 0; }
.cell-text { font-size: .875rem; color: #7b809a; }
.cell-text-mono { font-size: .8125rem; color: #7b809a; font-family: monospace; }

/* ─── Status Badge ────────────────────────────── */
.status-badge {
    display: inline-flex; align-items: center; gap: .3rem;
    padding: .25rem .7rem; border-radius: 9999px;
    font-size: .7rem; font-weight: 600;
}
.status-aktif { background: #f0fdf4; color: #15803d; }
.status-aktif .status-dot { width: 6px; height: 6px; border-radius: 50%; background: #16a34a; flex-shrink: 0; }
.status-nonaktif { background: #f1f5f9; color: #64748b; }
.status-nonaktif .status-dot { width: 6px; height: 6px; border-radius: 50%; background: #94a3b8; flex-shrink: 0; }

/* ─── Action Buttons ──────────────────────────── */
.action-group { display: flex; align-items: center; gap: .375rem; justify-content: center; }
.btn-icon {
    display: inline-flex; align-items: center; justify-content: center;
    width: 2rem; height: 2rem; border-radius: .4rem;
    border: 1px solid #e2e8f0; background: #fff;
    cursor: pointer; text-decoration: none;
    transition: background .15s, border-color .15s, transform .15s;
    color: #64748b;
}
.btn-icon:hover { transform: translateY(-1px); }
.btn-icon-edit:hover { background: #eff6ff; border-color: #bfdbfe; color: #1d4ed8; }
.btn-icon-delete:hover { background: #fef2f2; border-color: #fecaca; color: #dc2626; }
.btn-icon i { font-size: .7rem; }

/* ─── Empty State ─────────────────────────────── */
.empty-state { text-align: center; padding: 3rem 1.5rem; }
.empty-state i { font-size: 2.5rem; color: #d2d6da; display: block; margin-bottom: 1rem; }
.empty-state p { font-size: .875rem; color: #adb5bd; margin: 0; }

/* ─── Modal Overlay & Box ─────────────────────── */
#modalOverlay {
    display: none; position: fixed; inset: 0;
    background: rgba(15,23,42,.55); z-index: 9000; backdrop-filter: blur(2px);
}
#deleteModalBox {
    display: none; position: fixed; top: 50%; left: 50%;
    transform: translate(-50%,-50%) scale(.95);
    z-index: 9001; width: 90%; max-width: 460px; background: #fff;
    border-radius: 1.25rem; box-shadow: 0 25px 50px -12px rgba(0,0,0,.3);
    overflow: hidden; transition: transform .2s ease, opacity .2s ease; opacity: 0;
}
#deleteModalBox.is-open { transform: translate(-50%,-50%) scale(1); opacity: 1; }
.modal-header {
    display: flex; align-items: center; gap: .875rem;
    padding: 1.25rem 1.5rem; border-bottom: 1px solid #f0f2f5;
}
.modal-icon-wrap {
    width: 2.75rem; height: 2.75rem; border-radius: .75rem; flex-shrink: 0;
    background: linear-gradient(310deg,#ea0606,#ff667c);
    display: flex; align-items: center; justify-content: center;
    box-shadow: 0 4px 6px -1px rgba(234,6,6,.4);
}
.modal-icon-wrap i { color: #fff; font-size: .875rem; }
.modal-header-title { font-size: 1rem; font-weight: 700; color: #344767; margin: 0 0 .15rem; }
.modal-header-sub   { font-size: .775rem; color: #7b809a; margin: 0; }
.modal-body { padding: 1.25rem 1.5rem; }
.modal-loading { text-align: center; padding: 1.5rem 0; }
.modal-loading .spinner {
    width: 2.5rem; height: 2.5rem; border-radius: 50%;
    border: 3px solid #f0f2f5; border-top-color: #7928ca;
    animation: spin .7s linear infinite; margin: 0 auto .75rem;
}
@keyframes spin { to { transform: rotate(360deg); } }
.modal-loading p { font-size: .8125rem; color: #7b809a; margin: 0; }
.modal-user-card { background: #f8f9fa; border-radius: .75rem; padding: 1rem; margin-bottom: 1rem; }
.modal-user-card-label { font-size: .625rem; font-weight: 700; text-transform: uppercase; letter-spacing: .08em; color: #adb5bd; margin: 0 0 .75rem; }
.modal-user-info { display: flex; align-items: center; gap: .75rem; }
.modal-user-avatar {
    width: 2.5rem; height: 2.5rem; border-radius: .5rem;
    background: linear-gradient(310deg,#7928ca,#ff007f);
    display: flex; align-items: center; justify-content: center;
    color: #fff; font-size: .875rem; font-weight: 700; flex-shrink: 0;
}
.modal-user-name  { font-size: .875rem; font-weight: 700; color: #344767; margin: 0 0 .1rem; }
.modal-user-email { font-size: .775rem; color: #7b809a; margin: 0; }
.modal-user-role  { margin-top: .625rem; display: flex; align-items: center; gap: .5rem; font-size: .8rem; color: #7b809a; }
.modal-alert {
    display: flex; align-items: flex-start; gap: .75rem;
    padding: .875rem 1rem; border-radius: .75rem; margin-bottom: .75rem; font-size: .8125rem;
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
.modal-footer {
    display: flex; align-items: center; justify-content: flex-end; gap: .625rem;
    padding: 1rem 1.5rem; border-top: 1px solid #f0f2f5;
}
.btn-cancel {
    padding: .5rem 1.125rem; font-size: .8125rem; font-weight: 600;
    color: #7b809a; background: #f0f2f5; border: none; border-radius: .5rem;
    cursor: pointer; transition: background .15s;
}
.btn-cancel:hover { background: #e2e8f0; }
.btn-confirm-delete {
    display: inline-flex; align-items: center; gap: .375rem;
    padding: .5rem 1.25rem; font-size: .8125rem; font-weight: 600; color: #fff;
    background: linear-gradient(310deg,#ea0606,#ff667c); border: none; border-radius: .5rem;
    box-shadow: 0 4px 7px -1px rgba(234,6,6,.4);
    cursor: pointer; transition: box-shadow .15s, transform .15s;
}
.btn-confirm-delete:hover { transform: translateY(-1px); box-shadow: 0 6px 15px -3px rgba(234,6,6,.5); }
</style>

{{-- Page Header --}}
<div style="display:flex; align-items:flex-start; justify-content:space-between; flex-wrap:wrap; gap:.75rem; margin-bottom:1.25rem;">
    <div>
        <h4 style="font-size:1.25rem; font-weight:700; color:#344767; margin:0 0 .25rem;">Manajemen Pengguna</h4>
        <p style="font-size:.875rem; color:#7b809a; margin:0;">Kelola akun pengguna AsetLab dan role-nya</p>
    </div>
    <a href="{{ route('admin.users.create') }}" class="btn-add">
        <i class="fas fa-plus"></i>
        Tambah Pengguna
    </a>
</div>

{{-- Alerts --}}
@if(session('success'))
<div class="alert-success">
    <i class="fas fa-check-circle"></i>
    {{ session('success') }}
</div>
@endif

@if($errors->any() || isset($error))
<div class="alert-error">
    <i class="fas fa-exclamation-circle"></i>
    {{ $errors->any() ? $errors->first() : $error }}
</div>
@endif

{{-- Filter Row --}}
<div class="filter-row">
    <div class="filter-left">
        <div class="search-wrap">
            <i class="fas fa-search search-icon"></i>
            <input type="text" id="searchInput" placeholder="Cari nama, email, atau NIP..." />
        </div>
        <select id="roleFilter" class="role-filter" onchange="filterTable()">
            <option value="">Semua Role</option>
            <option value="administrator">Administrator</option>
            <option value="kepala laboratorium">Kepala Laboratorium</option>
            <option value="ketua program studi">Ketua Program Studi</option>
            <option value="staf administrasi">Staf Administrasi</option>
            <option value="staf laboratorium">Staf Laboratorium</option>
        </select>
    </div>
    <span class="count-label" id="userCount">{{ count($users) }} dari {{ count($users) }} pengguna</span>
</div>

{{-- Table Card --}}
<div class="table-card">
    <div style="overflow-x:auto;">
        <table class="users-table" id="usersTable">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>NIP</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th style="text-align:center; width:80px;">Aksi</th>
                </tr>
            </thead>
            <tbody id="usersTableBody">
                @forelse($users as $u)
                @php
                    // Support kedua field: is_active (baru) dan status (lama)
                    $isAktif = isset($u['is_active']) ? (bool)$u['is_active'] : (strtolower($u['status'] ?? 'aktif') !== 'nonaktif');
                @endphp
                <tr class="user-row">
                    <td>
                        <div class="user-cell">
                            <div class="user-avatar">{{ strtoupper(substr($u['name'] ?? 'U', 0, 2)) }}</div>
                            <span class="user-name user-name-text">{{ $u['name'] ?? '-' }}</span>
                        </div>
                    </td>
                    <td>
                        <span class="cell-text-mono user-nip-text">{{ $u['nip'] ?? '-' }}</span>
                    </td>
                    <td>
                        <span class="cell-text user-email-text">{{ $u['email'] ?? '-' }}</span>
                    </td>
                    <td>
                        <span class="cell-text user-role-text">{{ $u['role'] ?? '-' }}</span>
                    </td>
                    <td>
                        <span class="status-badge {{ $isAktif ? 'status-aktif' : 'status-nonaktif' }}">
                            <span class="status-dot"></span>
                            {{ $isAktif ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </td>
                    <td>
                        <div class="action-group">
                            <a href="{{ route('admin.users.edit', $u['id']) }}" class="btn-icon btn-icon-edit" title="Edit">
                                <i class="fas fa-pencil-alt"></i>
                            </a>
                            <button type="button" class="btn-icon btn-icon-delete" title="Hapus"
                                onclick="openDeleteModal({{ $u['id'] }}, '{{ addslashes($u['name']) }}', '{{ addslashes($u['email']) }}', '{{ addslashes($u['role'] ?? '') }}')">
                                <i class="fas fa-trash-alt"></i>
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
</div>

{{-- Smart Delete Modal --}}
<div id="modalOverlay" onclick="closeDeleteModal()"></div>

<div id="deleteModalBox">
    <div class="modal-header">
        <div class="modal-icon-wrap"><i class="fas fa-exclamation-triangle"></i></div>
        <div>
            <p class="modal-header-title">Konfirmasi Hapus User</p>
            <p class="modal-header-sub">Tindakan ini tidak dapat dibatalkan</p>
        </div>
    </div>

    <div id="deleteLoading" class="modal-body">
        <div class="modal-loading">
            <div class="spinner"></div>
            <p>Memeriksa keamanan data...</p>
        </div>
    </div>

    <div id="deleteContent" style="display:none;">
        <div class="modal-body">
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
const totalUsers = {{ count($users) }};

function filterTable() {
    const q = document.getElementById('searchInput').value.toLowerCase();
    const roleVal = document.getElementById('roleFilter').value.toLowerCase();
    let visible = 0;

    document.querySelectorAll('#usersTableBody .user-row').forEach(row => {
        const name  = (row.querySelector('.user-name-text')?.textContent  ?? '').toLowerCase();
        const email = (row.querySelector('.user-email-text')?.textContent ?? '').toLowerCase();
        const nip   = (row.querySelector('.user-nip-text')?.textContent   ?? '').toLowerCase();
        const role  = (row.querySelector('.user-role-text')?.textContent  ?? '').toLowerCase();

        const matchSearch = !q || name.includes(q) || email.includes(q) || nip.includes(q);
        const matchRole   = !roleVal || role.includes(roleVal);

        if (matchSearch && matchRole) {
            row.style.display = '';
            visible++;
        } else {
            row.style.display = 'none';
        }
    });

    document.getElementById('userCount').textContent = `${visible} dari ${totalUsers} pengguna`;
}

document.getElementById('searchInput').addEventListener('input', filterTable);

function openDeleteModal(id, name, email, role) {
    const overlay  = document.getElementById('modalOverlay');
    const modalBox = document.getElementById('deleteModalBox');

    document.getElementById('deleteLoading').style.display  = 'block';
    document.getElementById('deleteContent').style.display  = 'none';
    document.getElementById('deleteDenied').style.display   = 'none';
    document.getElementById('deleteAllowed').style.display  = 'none';
    document.getElementById('deleteForm').style.display     = 'none';

    overlay.style.display  = 'block';
    modalBox.style.display = 'block';
    setTimeout(() => modalBox.classList.add('is-open'), 10);

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
