const db = require('../config/db');

// ─────────────────────────────────────────────────────────
// GET /api/consumables/meta — Metadata untuk dropdown form BHP
// Mengembalikan: kategori, lokasi (rooms), satuan
// ─────────────────────────────────────────────────────────
exports.getMeta = async (req, res) => {
    try {
        // Default kategori yang sudah didefinisikan
        const defaultCategories = ['APD', 'Reagen', 'Gelas', 'Plastik', 'Filter', 'Media', 'Kultur', 'Kimia', 'Lainnya'];

        // Satuan umum yang sering dipakai di lab
        const defaultUnits = [
            'box (100 pcs)', 'box (50 pcs)', 'box (20 pcs)',
            'pcs', 'liter', 'mL', 'gram', 'kg',
            'lusin', 'pak', 'rak (96 pcs)', 'bag (500 pcs)',
            'pack (100 pcs)', 'botol', 'tube', 'ampul',
        ];

        // Ambil kategori distinct dari DB (yang sudah ada)
        const [dbCategories] = await db.query(
            `SELECT DISTINCT category FROM consumables WHERE category IS NOT NULL AND category != '' ORDER BY category`
        );
        const existingCats = dbCategories.map(r => r.category);
        // Gabung: defaultCategories + yang ada di DB (tanpa duplikat)
        const mergedCategories = [...new Set([...defaultCategories, ...existingCats])];

        // Ambil semua rooms sebagai pilihan lokasi penyimpanan
        const [rooms] = await db.query(`SELECT id, code, name FROM rooms ORDER BY code`);

        // Ambil lokasi distinct dari DB consumables (yang sudah ada, bisa string custom)
        const [dbLocations] = await db.query(
            `SELECT DISTINCT location FROM consumables WHERE location IS NOT NULL AND location != '' ORDER BY location`
        );
        const existingLocs = dbLocations.map(r => r.location);
        const roomCodes = rooms.map(r => r.code);
        // Lokasi custom (yang ada di consumables tapi bukan kode room)
        const customLocations = existingLocs.filter(l => !roomCodes.includes(l));

        res.json({
            categories: mergedCategories,
            units: defaultUnits,
            rooms: rooms.map(r => ({ code: r.code, name: r.name, label: `${r.code} — ${r.name}` })),
            customLocations,
        });
    } catch (error) {
        console.error('getMeta error:', error);
        res.status(500).json({ message: 'Server error: ' + (error.sqlMessage || error.message) });
    }
};

// ─────────────────────────────────────────────────────────
// GET /api/consumables — Daftar lengkap semua BHP beserta ruangan
// ─────────────────────────────────────────────────────────
exports.getAllConsumables = async (req, res) => {
    try {
        const query = `
            SELECT c.*, r.code AS room_code, r.name AS room_name 
            FROM consumables c
            LEFT JOIN rooms r ON c.room_id = r.id
            ORDER BY c.created_at DESC
        `;
        const [rows] = await db.query(query);
        res.json({ consumables: rows });
    } catch (error) {
        console.error('getAllConsumables error:', error);
        res.status(500).json({ message: 'Server error: ' + (error.sqlMessage || error.message) });
    }
};

// ─────────────────────────────────────────────────────────
// POST /api/consumables — Tambah BHP baru
// ─────────────────────────────────────────────────────────
exports.createConsumable = async (req, res) => {
    const { name, code, category, unit, stock, min_stock, price, location, description } = req.body;

    if (!name || !name.trim()) {
        return res.status(400).json({ message: 'Nama BHP wajib diisi.' });
    }

    try {
        const [result] = await db.query(
            `INSERT INTO consumables (name, code, category, unit, stock, min_stock, price, location, description)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)`,
            [
                name.trim(),
                code?.trim() || null,
                category?.trim() || null,
                unit?.trim() || null,
                parseInt(stock) || 0,
                parseInt(min_stock) || 0,
                parseFloat(price) || 0,
                location?.trim() || null,
                description?.trim() || null,
            ]
        );
        res.status(201).json({ message: 'BHP berhasil ditambahkan.', id: result.insertId });
    } catch (error) {
        console.error('createConsumable error:', error);
        if (error.code === 'ER_DUP_ENTRY') {
            return res.status(409).json({ message: 'Kode BHP sudah digunakan. Gunakan kode yang berbeda.' });
        }
        res.status(500).json({ message: 'Server error: ' + (error.sqlMessage || error.message) });
    }
};

// ─────────────────────────────────────────────────────────
// PUT /api/consumables/:id — Update data BHP
// ─────────────────────────────────────────────────────────
exports.updateConsumable = async (req, res) => {
    const { id } = req.params;
    const { name, code, category, unit, stock, min_stock, price, location, description } = req.body;

    if (!name || !name.trim()) {
        return res.status(400).json({ message: 'Nama BHP wajib diisi.' });
    }

    try {
        const [existing] = await db.query('SELECT id FROM consumables WHERE id = ?', [id]);
        if (existing.length === 0) {
            return res.status(404).json({ message: 'BHP tidak ditemukan.' });
        }

        await db.query(
            `UPDATE consumables 
             SET name=?, code=?, category=?, unit=?, stock=?, min_stock=?, price=?, location=?, description=?, updated_at=NOW()
             WHERE id=?`,
            [
                name.trim(),
                code?.trim() || null,
                category?.trim() || null,
                unit?.trim() || null,
                parseInt(stock) || 0,
                parseInt(min_stock) || 0,
                parseFloat(price) || 0,
                location?.trim() || null,
                description?.trim() || null,
                id
            ]
        );
        res.json({ message: 'Data BHP berhasil diperbarui.' });
    } catch (error) {
        console.error('updateConsumable error:', error);
        if (error.code === 'ER_DUP_ENTRY') {
            return res.status(409).json({ message: 'Kode BHP sudah digunakan oleh item lain.' });
        }
        res.status(500).json({ message: 'Server error: ' + (error.sqlMessage || error.message) });
    }
};

// ─────────────────────────────────────────────────────────
// DELETE /api/consumables/:id — Hapus BHP
// ─────────────────────────────────────────────────────────
exports.deleteConsumable = async (req, res) => {
    const { id } = req.params;
    try {
        const [existing] = await db.query('SELECT id, name FROM consumables WHERE id = ?', [id]);
        if (existing.length === 0) {
            return res.status(404).json({ message: 'BHP tidak ditemukan.' });
        }

        // Cek apakah BHP pernah digunakan dalam log maintenance
        const [usageCheck] = await db.query(
            'SELECT COUNT(*) AS cnt FROM maintenance_consumables WHERE consumable_id = ?', [id]
        );
        if (usageCheck[0].cnt > 0) {
            return res.status(409).json({
                message: `BHP "${existing[0].name}" tidak dapat dihapus karena sudah tercatat dalam ${usageCheck[0].cnt} log maintenance.`
            });
        }

        await db.query('DELETE FROM consumables WHERE id = ?', [id]);
        res.json({ message: 'BHP berhasil dihapus.' });
    } catch (error) {
        console.error('deleteConsumable error:', error);
        res.status(500).json({ message: 'Server error: ' + (error.sqlMessage || error.message) });
    }
};

// ─────────────────────────────────────────────────────────
// PATCH /api/consumables/:id/adjust-stock — Tambah / Kurangi stok manual
// ─────────────────────────────────────────────────────────
exports.adjustStock = async (req, res) => {
    const { id } = req.params;
    const { adjustment, notes } = req.body; // adjustment: integer, positif = tambah, negatif = kurang

    if (adjustment === undefined || adjustment === null || isNaN(parseInt(adjustment))) {
        return res.status(400).json({ message: 'Nilai penyesuaian stok (adjustment) wajib berupa angka.' });
    }

    const adj = parseInt(adjustment);
    if (adj === 0) {
        return res.status(400).json({ message: 'Nilai penyesuaian tidak boleh 0.' });
    }

    const connection = await db.getConnection();
    await connection.beginTransaction();

    try {
        const [[consumable]] = await connection.query(
            'SELECT id, name, stock FROM consumables WHERE id = ? FOR UPDATE', [id]
        );
        if (!consumable) {
            await connection.rollback();
            connection.release();
            return res.status(404).json({ message: 'BHP tidak ditemukan.' });
        }

        const newStock = consumable.stock + adj;
        if (newStock < 0) {
            await connection.rollback();
            connection.release();
            return res.status(400).json({
                message: `Stok tidak mencukupi. Stok saat ini: ${consumable.stock}, pengurangan diminta: ${Math.abs(adj)}.`
            });
        }

        await connection.query(
            'UPDATE consumables SET stock = ?, updated_at = NOW() WHERE id = ?',
            [newStock, id]
        );

        await connection.commit();
        res.json({
            message: `Stok ${consumable.name} berhasil disesuaikan. Stok baru: ${newStock}.`,
            old_stock: consumable.stock,
            new_stock: newStock,
            adjustment: adj
        });
    } catch (error) {
        await connection.rollback();
        console.error('adjustStock error:', error);
        res.status(500).json({ message: 'Server error: ' + (error.sqlMessage || error.message) });
    } finally {
        connection.release();
    }
};
