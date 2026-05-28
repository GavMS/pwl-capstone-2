const db = require('../config/db');

// ─────────────────────────────────────────────────────────
// Helper: generate kode ruangan otomatis
// ─────────────────────────────────────────────────────────
function generateRoomCode(name, existingCodes = []) {
    const prefixMap = {
        'laboratorium': 'LAB',
        'gudang': 'GDG',
        'ruangan': 'RNG',
        'ruang': 'RNG',
        'studio': 'STD',
    };
    const words = name.toLowerCase().split(/\s+/);
    const prefix = prefixMap[words[0]] ?? words[0].slice(0, 3).toUpperCase();
    const keyword = words.slice(1)
        .filter(w => !['dan','untuk','ke','di','yang','1','2','3','4','5','6','7','8','9'].includes(w))
        .map(w => w.slice(0, 3).toUpperCase())
        .slice(0, 2)
        .join('-');
    const base = keyword ? `${prefix}-${keyword}` : prefix;
    let seq = 1;
    while (existingCodes.includes(`${base}-${String(seq).padStart(2, '0')}`)) seq++;
    return `${base}-${String(seq).padStart(2, '0')}`;
}

// ─────────────────────────────────────────────────────────
// Helper: cek dependensi sebelum delete/edit
// ─────────────────────────────────────────────────────────
async function checkRoomDependencies(roomId) {
    const deps = [];

    // 1. assets
    const [assets] = await db.query(
        'SELECT COUNT(*) AS cnt FROM assets WHERE room_id = ?', [roomId]
    );
    if (assets[0].cnt > 0) {
        deps.push({ table: 'assets', label: 'Aset Inventaris', count: assets[0].cnt });
    }

    // 2. consumables (BHP)
    const [consumables] = await db.query(
        'SELECT COUNT(*) AS cnt FROM consumables WHERE room_id = ?', [roomId]
    );
    if (consumables[0].cnt > 0) {
        deps.push({ table: 'consumables', label: 'Barang Habis Pakai (BHP)', count: consumables[0].cnt });
    }

    // 3. users
    const [users] = await db.query(
        'SELECT COUNT(*) AS cnt FROM users WHERE room_id = ?', [roomId]
    );
    if (users[0].cnt > 0) {
        deps.push({ table: 'users', label: 'Pengguna', count: users[0].cnt });
    }

    return deps;
}

// ─────────────────────────────────────────────────────────
// GET /api/rooms — daftar semua ruangan + statistik
// ─────────────────────────────────────────────────────────
exports.getAll = async (req, res) => {
    try {
        const [rooms] = await db.query(`
            SELECT
                r.*,
                COALESCE(a.asset_count, 0)       AS asset_count,
                COALESCE(c.consumable_count, 0)  AS consumable_count,
                COALESCE(u.user_count, 0)        AS user_count
            FROM rooms r
            LEFT JOIN (
                SELECT room_id, COUNT(*) AS asset_count FROM assets GROUP BY room_id
            ) a ON r.id = a.room_id
            LEFT JOIN (
                SELECT room_id, COUNT(*) AS consumable_count FROM consumables GROUP BY room_id
            ) c ON r.id = c.room_id
            LEFT JOIN (
                SELECT room_id, COUNT(*) AS user_count FROM users GROUP BY room_id
            ) u ON r.id = u.room_id
            ORDER BY r.created_at DESC
        `);
        res.json({ rooms });
    } catch (error) {
        console.error('getAll rooms error:', error);
        res.status(500).json({ message: 'Server error' });
    }
};

// ─────────────────────────────────────────────────────────
// GET /api/rooms/:id — detail satu ruangan
// ─────────────────────────────────────────────────────────
exports.getOne = async (req, res) => {
    try {
        const [rows] = await db.query('SELECT * FROM rooms WHERE id = ?', [req.params.id]);
        if (rows.length === 0) return res.status(404).json({ message: 'Ruangan tidak ditemukan' });
        res.json({ room: rows[0] });
    } catch (error) {
        console.error('getOne room error:', error);
        res.status(500).json({ message: 'Server error' });
    }
};

// ─────────────────────────────────────────────────────────
// GET /api/rooms/:id/check-delete — smart delete check
// ─────────────────────────────────────────────────────────
exports.checkDelete = async (req, res) => {
    try {
        const [rows] = await db.query('SELECT * FROM rooms WHERE id = ?', [req.params.id]);
        if (rows.length === 0) return res.status(404).json({ message: 'Ruangan tidak ditemukan' });

        const room = rows[0];
        const dependencies = await checkRoomDependencies(req.params.id);

        res.json({
            room,
            canDelete: dependencies.length === 0,
            dependencies
        });
    } catch (error) {
        console.error('checkDelete room error:', error);
        res.status(500).json({ message: 'Server error' });
    }
};

// ─────────────────────────────────────────────────────────
// GET /api/rooms/:id/check-edit — smart edit info
// ─────────────────────────────────────────────────────────
exports.checkEdit = async (req, res) => {
    try {
        const [rows] = await db.query('SELECT * FROM rooms WHERE id = ?', [req.params.id]);
        if (rows.length === 0) return res.status(404).json({ message: 'Ruangan tidak ditemukan' });

        const room = rows[0];
        const dependencies = await checkRoomDependencies(req.params.id);

        res.json({ room, dependencies });
    } catch (error) {
        console.error('checkEdit room error:', error);
        res.status(500).json({ message: 'Server error' });
    }
};

// ─────────────────────────────────────────────────────────
// POST /api/rooms — tambah ruangan baru
// ─────────────────────────────────────────────────────────
exports.create = async (req, res) => {
    const { name, description } = req.body;

    if (!name || !name.trim()) {
        return res.status(400).json({ message: 'Nama ruangan wajib diisi' });
    }

    try {
        // Cek duplikat nama
        const [existingName] = await db.query('SELECT id FROM rooms WHERE name = ?', [name.trim()]);
        if (existingName.length > 0) {
            return res.status(409).json({ message: `Nama ruangan "${name.trim()}" sudah digunakan` });
        }

        // Generate kode otomatis
        const [existingRooms] = await db.query('SELECT code FROM rooms');
        const code = generateRoomCode(name.trim(), existingRooms.map(r => r.code));

        const [result] = await db.query(
            'INSERT INTO rooms (name, code, description) VALUES (?, ?, ?)',
            [name.trim(), code, description?.trim() || null]
        );

        const [newRoom] = await db.query('SELECT * FROM rooms WHERE id = ?', [result.insertId]);
        res.status(201).json({ message: 'Ruangan berhasil ditambahkan', room: newRoom[0] });
    } catch (error) {
        console.error('create room error:', error);
        if (error.code === 'ER_DUP_ENTRY') {
            return res.status(409).json({ message: 'Nama ruangan sudah digunakan' });
        }
        res.status(500).json({ message: 'Server error' });
    }
};

// ─────────────────────────────────────────────────────────
// PUT /api/rooms/:id — update ruangan
// ─────────────────────────────────────────────────────────
exports.update = async (req, res) => {
    const { name, description } = req.body;
    const { id } = req.params;

    if (!name || !name.trim()) {
        return res.status(400).json({ message: 'Nama ruangan wajib diisi' });
    }

    try {
        const [rows] = await db.query('SELECT id FROM rooms WHERE id = ?', [id]);
        if (rows.length === 0) return res.status(404).json({ message: 'Ruangan tidak ditemukan' });

        // Cek duplikat nama (exclude self)
        const [existingName] = await db.query(
            'SELECT id FROM rooms WHERE name = ? AND id != ?', [name.trim(), id]
        );
        if (existingName.length > 0) {
            return res.status(409).json({ message: `Nama ruangan "${name.trim()}" sudah digunakan` });
        }

        await db.query(
            'UPDATE rooms SET name = ?, description = ?, updated_at = NOW() WHERE id = ?',
            [name.trim(), description?.trim() || null, id]
        );

        const [updated] = await db.query('SELECT * FROM rooms WHERE id = ?', [id]);
        res.json({ message: 'Ruangan berhasil diperbarui', room: updated[0] });
    } catch (error) {
        console.error('update room error:', error);
        if (error.code === 'ER_DUP_ENTRY') {
            return res.status(409).json({ message: 'Nama ruangan sudah digunakan' });
        }
        res.status(500).json({ message: 'Server error' });
    }
};

// ─────────────────────────────────────────────────────────
// DELETE /api/rooms/:id — hapus ruangan (dengan dependency check)
// ─────────────────────────────────────────────────────────
exports.destroy = async (req, res) => {
    try {
        const [rows] = await db.query('SELECT * FROM rooms WHERE id = ?', [req.params.id]);
        if (rows.length === 0) return res.status(404).json({ message: 'Ruangan tidak ditemukan' });

        const dependencies = await checkRoomDependencies(req.params.id);
        if (dependencies.length > 0) {
            return res.status(409).json({
                message: 'Ruangan tidak dapat dihapus karena masih memiliki data terkait',
                dependencies
            });
        }

        await db.query('DELETE FROM rooms WHERE id = ?', [req.params.id]);
        res.json({ message: 'Ruangan berhasil dihapus' });
    } catch (error) {
        console.error('destroy room error:', error);
        res.status(500).json({ message: 'Server error' });
    }
};
