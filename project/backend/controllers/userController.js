const bcrypt = require('bcrypt');
const db = require('../config/db');

// ─────────────────────────────────────────────
// Helper: cek apakah user masih punya FK di tabel lain
// ─────────────────────────────────────────────
async function checkUserDependencies(userId) {
    const dependencies = [];

    // Daftar tabel yang berpotensi mereferensikan users.id
    // Tambahkan tabel lain jika schema berkembang
    const tablesToCheck = [
        { table: 'peminjaman',   column: 'user_id',       label: 'Peminjaman' },
        { table: 'pengajuan',    column: 'user_id',       label: 'Pengajuan' },
        { table: 'aset_assign',  column: 'user_id',       label: 'Penugasan Aset' },
        { table: 'aktivitas',    column: 'user_id',       label: 'Log Aktivitas' },
        { table: 'barang',       column: 'penanggung_id', label: 'Barang (Penanggung Jawab)' },
    ];

    for (const dep of tablesToCheck) {
        try {
            // Cek apakah tabel exists dulu (aman meski belum ada tabelnya)
            const [tables] = await db.query(
                `SELECT COUNT(*) as cnt FROM information_schema.TABLES 
                 WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ?`,
                [dep.table]
            );

            if (tables[0].cnt === 0) continue; // tabel belum ada, skip

            const [rows] = await db.query(
                `SELECT COUNT(*) as cnt FROM \`${dep.table}\` WHERE \`${dep.column}\` = ?`,
                [userId]
            );

            if (rows[0].cnt > 0) {
                dependencies.push({ label: dep.label, count: rows[0].cnt });
            }
        } catch (err) {
            // Ignore jika kolom tidak ada
        }
    }

    return dependencies;
}

// ─────────────────────────────────────────────
// GET /api/users — Daftar semua user beserta role
// ─────────────────────────────────────────────
exports.getAll = async (req, res) => {
    try {
        const [rows] = await db.query(`
            SELECT u.id, u.name, u.email, u.role_id, r.name AS role,
                   u.is_active, u.created_at, u.updated_at
            FROM users u
            LEFT JOIN roles r ON u.role_id = r.id
            ORDER BY u.created_at DESC
        `);
        res.json({ users: rows });
    } catch (err) {
        console.error('getAll users error:', err);
        res.status(500).json({ message: 'Server error' });
    }
};

// ─────────────────────────────────────────────
// GET /api/users/:id — Ambil 1 user by ID (Smart Edit)
// ─────────────────────────────────────────────
exports.getOne = async (req, res) => {
    try {
        const { id } = req.params;
        const [rows] = await db.query(`
            SELECT u.id, u.name, u.email, u.role_id, r.name AS role,
                   u.is_active, u.created_at, u.updated_at
            FROM users u
            LEFT JOIN roles r ON u.role_id = r.id
            WHERE u.id = ?
        `, [id]);

        if (rows.length === 0) {
            return res.status(404).json({ message: 'User tidak ditemukan' });
        }

        // Cek dependencies untuk Smart Edit warning
        const dependencies = await checkUserDependencies(id);

        res.json({ user: rows[0], dependencies });
    } catch (err) {
        console.error('getOne user error:', err);
        res.status(500).json({ message: 'Server error' });
    }
};

// ─────────────────────────────────────────────
// GET /api/users/:id/check-delete — Cek keamanan sebelum hapus (Smart Delete)
// ─────────────────────────────────────────────
exports.checkDelete = async (req, res) => {
    try {
        const { id } = req.params;
        const [rows] = await db.query(`
            SELECT u.id, u.name, u.email, r.name AS role
            FROM users u
            LEFT JOIN roles r ON u.role_id = r.id
            WHERE u.id = ?
        `, [id]);

        if (rows.length === 0) {
            return res.status(404).json({ message: 'User tidak ditemukan' });
        }

        const dependencies = await checkUserDependencies(id);
        const canDelete = dependencies.length === 0;

        res.json({
            user: rows[0],
            canDelete,
            dependencies,
            message: canDelete
                ? 'User dapat dihapus.'
                : `User tidak dapat dihapus karena masih terkait dengan: ${dependencies.map(d => `${d.label} (${d.count} data)`).join(', ')}.`
        });
    } catch (err) {
        console.error('checkDelete error:', err);
        res.status(500).json({ message: 'Server error' });
    }
};

// ─────────────────────────────────────────────
// GET /api/roles — Daftar semua role (untuk dropdown)
// ─────────────────────────────────────────────
exports.getRoles = async (req, res) => {
    try {
        const [rows] = await db.query('SELECT id, name FROM roles ORDER BY id');
        res.json({ roles: rows });
    } catch (err) {
        console.error('getRoles error:', err);
        res.status(500).json({ message: 'Server error' });
    }
};

// ─────────────────────────────────────────────
// POST /api/users — Tambah user baru
// ─────────────────────────────────────────────
exports.create = async (req, res) => {
    const { name, email, password, role_id, is_active } = req.body;

    if (!name || !email || !password || !role_id) {
        return res.status(400).json({ message: 'Semua field wajib diisi (name, email, password, role_id)' });
    }

    try {
        // Cek email sudah ada
        const [existing] = await db.query('SELECT id FROM users WHERE email = ?', [email]);
        if (existing.length > 0) {
            return res.status(409).json({ message: 'Email sudah terdaftar' });
        }

        const hashedPwd = await bcrypt.hash(password, 10);
        const activeVal = (is_active === false || is_active === '0' || is_active === 0) ? 0 : 1;
        const [result] = await db.query(
            'INSERT INTO users (name, email, password, role_id, roles_id, is_active) VALUES (?, ?, ?, ?, ?, ?)',
            [name, email, hashedPwd, role_id, role_id, activeVal]
        );

        res.status(201).json({ message: 'User berhasil ditambahkan', id: result.insertId });
    } catch (err) {
        console.error('create user error:', err);
        res.status(500).json({ message: 'Server error' });
    }
};

// ─────────────────────────────────────────────
// PUT /api/users/:id — Update user (Smart Edit)
// ─────────────────────────────────────────────
exports.update = async (req, res) => {
    const { id } = req.params;
    const { name, email, password, role_id, is_active } = req.body;

    if (!name || !email || !role_id) {
        return res.status(400).json({ message: 'Field name, email, dan role_id wajib diisi' });
    }

    try {
        // Cek user ada
        const [existing] = await db.query('SELECT id FROM users WHERE id = ?', [id]);
        if (existing.length === 0) {
            return res.status(404).json({ message: 'User tidak ditemukan' });
        }

        // Cek email duplikat (selain diri sendiri)
        const [emailCheck] = await db.query('SELECT id FROM users WHERE email = ? AND id != ?', [email, id]);
        if (emailCheck.length > 0) {
            return res.status(409).json({ message: 'Email sudah digunakan oleh user lain' });
        }

        const activeVal = (is_active === false || is_active === '0' || is_active === 0) ? 0 : 1;

        if (password && password.trim() !== '') {
            // Update dengan password baru
            const hashedPwd = await bcrypt.hash(password, 10);
            await db.query(
                'UPDATE users SET name=?, email=?, password=?, role_id=?, roles_id=?, is_active=?, updated_at=NOW() WHERE id=?',
                [name, email, hashedPwd, role_id, role_id, activeVal, id]
            );
        } else {
            // Update tanpa mengubah password
            await db.query(
                'UPDATE users SET name=?, email=?, role_id=?, roles_id=?, is_active=?, updated_at=NOW() WHERE id=?',
                [name, email, role_id, role_id, activeVal, id]
            );
        }

        res.json({ message: 'User berhasil diperbarui' });
    } catch (err) {
        console.error('update user error:', err);
        res.status(500).json({ message: 'Server error' });
    }
};

// ─────────────────────────────────────────────
// DELETE /api/users/:id — Hapus user (Smart Delete — dependency-aware)
// ─────────────────────────────────────────────
exports.destroy = async (req, res) => {
    const { id } = req.params;

    try {
        // Cek user ada
        const [existing] = await db.query('SELECT id, name, email FROM users WHERE id = ?', [id]);
        if (existing.length === 0) {
            return res.status(404).json({ message: 'User tidak ditemukan' });
        }

        // Smart Delete: cek dependencies sebelum hapus
        const dependencies = await checkUserDependencies(id);
        if (dependencies.length > 0) {
            return res.status(409).json({
                message: 'User tidak dapat dihapus karena masih memiliki data terkait.',
                dependencies
            });
        }

        await db.query('DELETE FROM users WHERE id = ?', [id]);
        res.json({ message: 'User berhasil dihapus' });
    } catch (err) {
        console.error('destroy user error:', err);
        res.status(500).json({ message: 'Server error' });
    }
};

// ─────────────────────────────────────────────
// GET /api/users/dashboard-stats — Statistik untuk dashboard admin
// ─────────────────────────────────────────────
exports.getDashboardStats = async (req, res) => {
    try {
        // Pengguna aktif & total
        const [[userStats]] = await db.query(`
            SELECT
                COUNT(*) AS total_users,
                SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) AS active_users
            FROM users
        `);

        // Ruangan
        const [[roomStats]] = await db.query(`
            SELECT COUNT(*) AS room_count FROM rooms
        `);

        // Aset inventaris
        const [[assetStats]] = await db.query(`
            SELECT COUNT(*) AS asset_count FROM assets
        `).catch(() => [[{ asset_count: 0 }]]);

        // BHP (consumables)
        const [[bhpStats]] = await db.query(`
            SELECT
                COUNT(*) AS bhp_count,
                SUM(CASE WHEN quantity <= 5 THEN 1 ELSE 0 END) AS low_stock_count
            FROM consumables
        `).catch(() => [[{ bhp_count: 0, low_stock_count: 0 }]]);

        // Gedung unik dari rooms (distinct building — pakai prefix kode)
        const [[buildingStats]] = await db.query(`
            SELECT COUNT(DISTINCT SUBSTRING_INDEX(code, '-', 1)) AS building_count FROM rooms
        `).catch(() => [[{ building_count: 0 }]]);

        // Pengguna terbaru (5 terakhir)
        const [recentUsers] = await db.query(`
            SELECT u.id, u.name, u.email, r.name AS role, u.is_active
            FROM users u
            LEFT JOIN roles r ON u.role_id = r.id
            ORDER BY u.created_at DESC
            LIMIT 5
        `);

        res.json({
            stats: {
                total_users:    parseInt(userStats.total_users)   || 0,
                active_users:   parseInt(userStats.active_users)  || 0,
                room_count:     parseInt(roomStats.room_count)     || 0,
                building_count: parseInt(buildingStats.building_count) || 0,
                asset_count:    parseInt(assetStats.asset_count)  || 0,
                bhp_count:      parseInt(bhpStats.bhp_count)      || 0,
                low_stock_count:parseInt(bhpStats.low_stock_count)|| 0,
            },
            recentUsers
        });
    } catch (err) {
        console.error('getDashboardStats error:', err);
        res.status(500).json({ message: 'Server error' });
    }
};
