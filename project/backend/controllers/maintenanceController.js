const db = require('../config/db');

// ─────────────────────────────────────────────────────────
// GET /api/maintenance/asset/:assetId  — Riwayat log maintenance per aset
// ─────────────────────────────────────────────────────────
exports.getLogsByAsset = async (req, res) => {
    const { assetId } = req.params;
    try {
        // Cek aset ada
        const [assetRows] = await db.query('SELECT id, name FROM assets WHERE id = ?', [assetId]);
        if (assetRows.length === 0) {
            return res.status(404).json({ message: 'Aset tidak ditemukan.' });
        }

        // Ambil log beserta info petugas
        const [logs] = await db.query(
            `SELECT ml.*, u.name AS performed_by_name
             FROM maintenance_logs ml
             LEFT JOIN users u ON ml.performed_by = u.id
             WHERE ml.asset_id = ?
             ORDER BY ml.maintenance_date DESC, ml.created_at DESC`,
            [assetId]
        );

        // Ambil BHP yang digunakan untuk setiap log
        for (const log of logs) {
            const [usedItems] = await db.query(
                `SELECT mc.*, c.name AS consumable_name, c.unit, c.code AS consumable_code
                 FROM maintenance_consumables mc
                 JOIN consumables c ON mc.consumable_id = c.id
                 WHERE mc.log_id = ?`,
                [log.id]
            );
            log.used_consumables = usedItems;
        }

        res.json({ asset: assetRows[0], logs });
    } catch (error) {
        console.error('getLogsByAsset error:', error);
        res.status(500).json({ message: 'Server error: ' + (error.sqlMessage || error.message) });
    }
};

// ─────────────────────────────────────────────────────────
// GET /api/maintenance  — Semua log maintenance (lintas aset)
// ─────────────────────────────────────────────────────────
exports.getAllLogs = async (req, res) => {
    try {
        const [logs] = await db.query(
            `SELECT ml.*, 
                    a.name AS asset_name, a.code AS asset_code,
                    u.name AS performed_by_name
             FROM maintenance_logs ml
             LEFT JOIN assets a ON ml.asset_id = a.id
             LEFT JOIN users u ON ml.performed_by = u.id
             ORDER BY ml.maintenance_date DESC, ml.created_at DESC`
        );

        // Ambil BHP yang digunakan untuk setiap log
        for (const log of logs) {
            const [usedItems] = await db.query(
                `SELECT mc.*, c.name AS consumable_name, c.unit, c.code AS consumable_code
                 FROM maintenance_consumables mc
                 JOIN consumables c ON mc.consumable_id = c.id
                 WHERE mc.log_id = ?`,
                [log.id]
            );
            log.used_consumables = usedItems;
        }

        res.json({ logs });
    } catch (error) {
        console.error('getAllLogs error:', error);
        res.status(500).json({ message: 'Server error: ' + (error.sqlMessage || error.message) });
    }
};

// ─────────────────────────────────────────────────────────
// POST /api/maintenance/asset/:assetId — Tambah log maintenance baru
// (Termasuk: update kondisi aset + kurangi stok BHP yang digunakan)
// ─────────────────────────────────────────────────────────
exports.createLog = async (req, res) => {
    const { assetId } = req.params;
    const {
        maintenance_date,
        description,
        condition_after,
        cost,
        notes,
        consumables_used  // array of { consumable_id, quantity_used }
    } = req.body;

    // ── Validasi input ──
    if (!maintenance_date) {
        return res.status(400).json({ message: 'Tanggal maintenance wajib diisi.' });
    }
    if (!description || !description.trim()) {
        return res.status(400).json({ message: 'Deskripsi pekerjaan wajib diisi.' });
    }
    if (!condition_after) {
        return res.status(400).json({ message: 'Kondisi aset setelah maintenance wajib diisi.' });
    }

    const validConditions = ['Baik', 'Perlu Maintenance', 'Rusak Ringan', 'Rusak Berat'];
    if (!validConditions.includes(condition_after)) {
        return res.status(400).json({
            message: `Kondisi tidak valid. Pilih salah satu: ${validConditions.join(', ')}.`
        });
    }

    const connection = await db.getConnection();
    await connection.beginTransaction();

    try {
        // 1. Cek aset ada
        const [[asset]] = await connection.query(
            'SELECT id, name, condition_status FROM assets WHERE id = ?', [assetId]
        );
        if (!asset) {
            await connection.rollback();
            connection.release();
            return res.status(404).json({ message: 'Aset tidak ditemukan.' });
        }

        const conditionBefore = asset.condition_status || 'Baik';

        // 2. Validasi stok BHP sebelum kurangi (cek semua dulu, baru eksekusi)
        const usedList = Array.isArray(consumables_used) ? consumables_used : [];
        const resolvedItems = [];

        for (const item of usedList) {
            if (!item.consumable_id || !item.quantity_used || parseInt(item.quantity_used) <= 0) {
                continue; // lewati item yang tidak valid
            }

            const [[consumable]] = await connection.query(
                'SELECT id, name, stock, unit FROM consumables WHERE id = ? FOR UPDATE',
                [item.consumable_id]
            );

            if (!consumable) {
                await connection.rollback();
                connection.release();
                return res.status(404).json({
                    message: `BHP dengan ID ${item.consumable_id} tidak ditemukan.`
                });
            }

            const qty = parseInt(item.quantity_used);
            if (consumable.stock < qty) {
                await connection.rollback();
                connection.release();
                return res.status(400).json({
                    message: `Stok BHP "${consumable.name}" tidak mencukupi. Stok tersedia: ${consumable.stock} ${consumable.unit ?? ''}, diminta: ${qty}.`
                });
            }

            resolvedItems.push({ consumable, qty });
        }

        // 3. Insert maintenance log
        const [logResult] = await connection.query(
            `INSERT INTO maintenance_logs 
             (asset_id, performed_by, maintenance_date, description, condition_before, condition_after, cost, notes)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)`,
            [
                assetId,
                req.user?.id || null,
                maintenance_date,
                description.trim(),
                conditionBefore,
                condition_after,
                parseFloat(cost) || 0,
                notes?.trim() || null,
            ]
        );
        const logId = logResult.insertId;

        // 4. Update kondisi aset
        await connection.query(
            'UPDATE assets SET condition_status = ?, status = ?, updated_at = NOW() WHERE id = ?',
            [condition_after, condition_after, assetId]
        );

        // 5. Kurangi stok BHP & catat ke maintenance_consumables
        for (const { consumable, qty } of resolvedItems) {
            const newStock = consumable.stock - qty;

            await connection.query(
                'UPDATE consumables SET stock = ?, updated_at = NOW() WHERE id = ?',
                [newStock, consumable.id]
            );

            await connection.query(
                'INSERT INTO maintenance_consumables (log_id, consumable_id, quantity_used) VALUES (?, ?, ?)',
                [logId, consumable.id, qty]
            );
        }

        await connection.commit();
        res.status(201).json({
            message: 'Log maintenance berhasil ditambahkan.',
            log_id: logId,
            condition_before: conditionBefore,
            condition_after,
            consumables_deducted: resolvedItems.length,
        });
    } catch (error) {
        await connection.rollback();
        console.error('createLog error:', error);
        res.status(500).json({ message: 'Server error: ' + (error.sqlMessage || error.message) });
    } finally {
        connection.release();
    }
};

// ─────────────────────────────────────────────────────────
// DELETE /api/maintenance/:logId — Hapus satu log maintenance
// (Stok BHP yang pernah dikurangi TIDAK dikembalikan — hapus log hanya arsip)
// ─────────────────────────────────────────────────────────
exports.deleteLog = async (req, res) => {
    const { logId } = req.params;
    try {
        const [existing] = await db.query('SELECT id FROM maintenance_logs WHERE id = ?', [logId]);
        if (existing.length === 0) {
            return res.status(404).json({ message: 'Log maintenance tidak ditemukan.' });
        }

        // Hapus cascading ke maintenance_consumables otomatis karena ON DELETE CASCADE
        await db.query('DELETE FROM maintenance_logs WHERE id = ?', [logId]);
        res.json({ message: 'Log maintenance berhasil dihapus.' });
    } catch (error) {
        console.error('deleteLog error:', error);
        res.status(500).json({ message: 'Server error: ' + (error.sqlMessage || error.message) });
    }
};
