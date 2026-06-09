const db = require('../config/db');

// ─────────────────────────────────────────────────────────
// GET /api/procurement — Daftar semua draf pengadaan
// ─────────────────────────────────────────────────────────
exports.getAllDrafts = async (req, res) => {
    try {
        const query = `
            SELECT pd.*, u.name AS creator_name,
                   COALESCE(items.item_count, 0) AS item_count,
                   COALESCE(items.total_price, 0) AS total_price
            FROM procurement_drafts pd
            LEFT JOIN users u ON pd.created_by = u.id
            LEFT JOIN (
                SELECT draft_id, COUNT(*) AS item_count, SUM(price * quantity) AS total_price
                FROM procurement_items
                GROUP BY draft_id
            ) items ON pd.id = items.draft_id
            ORDER BY pd.created_at DESC
        `;
        const [rows] = await db.query(query);
        res.json({ drafts: rows });
    } catch (error) {
        console.error('getAllDrafts error:', error);
        res.status(500).json({ message: 'Server error: ' + (error.sqlMessage || error.message) });
    }
};

// ─────────────────────────────────────────────────────────
// GET /api/procurement/:id — Detail satu draf + item didalamnya
// ─────────────────────────────────────────────────────────
exports.getDraftById = async (req, res) => {
    const { id } = req.params;
    try {
        // 1. Ambil Header Draf
        const [draftRows] = await db.query(
            `SELECT pd.*, u.name AS creator_name
             FROM procurement_drafts pd
             LEFT JOIN users u ON pd.created_by = u.id
             WHERE pd.id = ?`,
            [id]
        );

        if (draftRows.length === 0) {
            return res.status(404).json({ message: 'Draf pengadaan tidak ditemukan' });
        }

        // 2. Ambil semua item draf
        const [itemRows] = await db.query(
            `SELECT pi.*, a.name AS replaced_asset_name, a.code AS replaced_asset_code
             FROM procurement_items pi
             LEFT JOIN assets a ON pi.replaced_asset_id = a.id
             WHERE pi.draft_id = ?`,
            [id]
        );

        res.json({
            draft: draftRows[0],
            items: itemRows
        });
    } catch (error) {
        console.error('getDraftById error:', error);
        res.status(500).json({ message: 'Server error: ' + (error.sqlMessage || error.message) });
    }
};

// ─────────────────────────────────────────────────────────
// POST /api/procurement — Buat draf pengadaan baru
// ─────────────────────────────────────────────────────────
exports.createDraft = async (req, res) => {
    const { title, year, status, items } = req.body;

    if (!title || !title.trim()) {
        return res.status(400).json({ message: 'Judul draf wajib diisi' });
    }
    if (!year) {
        return res.status(400).json({ message: 'Tahun draf wajib diisi' });
    }

    const connection = await db.getConnection();
    await connection.beginTransaction();

    try {
        // 1. Simpan Header Draf
        const [draftResult] = await connection.query(
            'INSERT INTO procurement_drafts (title, year, status, created_by) VALUES (?, ?, ?, ?)',
            [title.trim(), year, status || 'draft', req.user.id]
        );
        const draftId = draftResult.insertId;

        // 2. Simpan Item Detail
        if (items && Array.isArray(items) && items.length > 0) {
            for (const item of items) {
                if (!item.name || !item.name.trim()) continue;

                await connection.query(
                    `INSERT INTO procurement_items 
                     (draft_id, item_type, name, price, quantity, purchase_link, replaced_asset_id, notes) 
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?)`,
                    [
                        draftId,
                        item.item_type || 'inventaris',
                        item.name.trim(),
                        item.price || 0,
                        item.quantity || 0,
                        item.purchase_link?.trim() || null,
                        item.replaced_asset_id || null,
                        item.notes?.trim() || null
                    ]
                );
            }
        }

        await connection.commit();
        res.status(201).json({ message: 'Draf pengadaan berhasil dibuat', id: draftId });
    } catch (error) {
        await connection.rollback();
        console.error('createDraft error:', error);
        res.status(500).json({ message: 'Server error: ' + (error.sqlMessage || error.message) });
    } finally {
        connection.release();
    }
};

// ─────────────────────────────────────────────────────────
// PUT /api/procurement/:id — Update draf pengadaan
// ─────────────────────────────────────────────────────────
exports.updateDraft = async (req, res) => {
    const { id } = req.params;
    const { title, year, status, items } = req.body;

    if (!title || !title.trim()) {
        return res.status(400).json({ message: 'Judul draf wajib diisi' });
    }
    if (!year) {
        return res.status(400).json({ message: 'Tahun draf wajib diisi' });
    }

    const connection = await db.getConnection();
    await connection.beginTransaction();

    try {
        // 1. Cek keberadaan draf
        const [existing] = await connection.query('SELECT id, status FROM procurement_drafts WHERE id = ?', [id]);
        if (existing.length === 0) {
            connection.release();
            return res.status(404).json({ message: 'Draf pengadaan tidak ditemukan' });
        }

        // 1b. Draf yang sudah dikunci tidak boleh diubah lagi
        if (existing[0].status !== 'draft') {
            connection.release();
            return res.status(400).json({ message: 'Draf sudah diajukan/difinalisasi dan tidak dapat diubah lagi.' });
        }

        // 2. Update Header
        await connection.query(
            'UPDATE procurement_drafts SET title = ?, year = ?, status = ?, updated_at = NOW() WHERE id = ?',
            [title.trim(), year, status || 'draft', id]
        );

        // 3. Hapus item draf lama agar bersih, lalu insert ulang
        await connection.query('DELETE FROM procurement_items WHERE draft_id = ?', [id]);

        // 4. Masukkan item baru
        if (items && Array.isArray(items) && items.length > 0) {
            for (const item of items) {
                if (!item.name || !item.name.trim()) continue;

                await connection.query(
                    `INSERT INTO procurement_items 
                     (draft_id, item_type, name, price, quantity, purchase_link, replaced_asset_id, notes) 
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?)`,
                    [
                        id,
                        item.item_type || 'inventaris',
                        item.name.trim(),
                        item.price || 0,
                        item.quantity || 0,
                        item.purchase_link?.trim() || null,
                        item.replaced_asset_id || null,
                        item.notes?.trim() || null
                    ]
                );
            }
        }

        await connection.commit();
        res.json({ message: 'Draf pengadaan berhasil diperbarui' });
    } catch (error) {
        await connection.rollback();
        console.error('updateDraft error:', error);
        res.status(500).json({ message: 'Server error: ' + (error.sqlMessage || error.message) });
    } finally {
        connection.release();
    }
};

// ─────────────────────────────────────────────────────────
// DELETE /api/procurement/:id — Hapus draf pengadaan
// ─────────────────────────────────────────────────────────
exports.deleteDraft = async (req, res) => {
    const { id } = req.params;
    try {
        // Cek draf ada
        const [existing] = await db.query('SELECT id, status FROM procurement_drafts WHERE id = ?', [id]);
        if (existing.length === 0) {
            return res.status(404).json({ message: 'Draf pengadaan tidak ditemukan' });
        }

        // Hanya draf mentah (belum diajukan) yang boleh dihapus
        if (existing[0].status !== 'draft') {
            return res.status(400).json({ message: 'Draf yang sudah diajukan/difinalisasi tidak dapat dihapus.' });
        }

        // Hapus draf (item otomatis terhapus karena ON DELETE CASCADE)
        await db.query('DELETE FROM procurement_drafts WHERE id = ?', [id]);
        res.json({ message: 'Draf pengadaan berhasil dihapus' });
    } catch (error) {
        console.error('deleteDraft error:', error);
        res.status(500).json({ message: 'Server error: ' + (error.sqlMessage || error.message) });
    }
};

// ─────────────────────────────────────────────────────────
// PATCH /api/procurement/:id/items/:itemId/review — Review per item
// ─────────────────────────────────────────────────────────
exports.updateItemStatus = async (req, res) => {
    const { id, itemId } = req.params;
    const { review_status } = req.body;

    if (!['approved', 'rejected', 'pending'].includes(review_status)) {
        return res.status(400).json({ message: 'Status tidak valid. Harus "approved", "rejected", atau "pending".' });
    }

    try {
        const [drafts] = await db.query('SELECT id, status FROM procurement_drafts WHERE id = ?', [id]);
        if (drafts.length === 0) return res.status(404).json({ message: 'Draf tidak ditemukan' });
        if (drafts[0].status !== 'submitted') {
            return res.status(400).json({ message: 'Draf sudah difinalisasi atau belum diajukan. Item tidak dapat diubah.' });
        }

        const [result] = await db.query(
            'UPDATE procurement_items SET review_status = ? WHERE id = ? AND draft_id = ?',
            [review_status, itemId, id]
        );
        if (result.affectedRows === 0) {
            return res.status(404).json({ message: 'Item tidak ditemukan dalam draf ini' });
        }

        res.json({ message: `Status item berhasil diubah menjadi "${review_status}"` });
    } catch (error) {
        console.error('updateItemStatus error:', error);
        res.status(500).json({ message: 'Server error: ' + (error.sqlMessage || error.message) });
    }
};

// ─────────────────────────────────────────────────────────
// POST /api/procurement/:id/finalize — Finalisasi draf (kunci permanen)
// ─────────────────────────────────────────────────────────
exports.finalizeDraft = async (req, res) => {
    const { id } = req.params;
    const connection = await db.getConnection();
    await connection.beginTransaction();

    try {
        const [drafts] = await connection.query('SELECT id, status, year FROM procurement_drafts WHERE id = ?', [id]);
        if (drafts.length === 0) {
            connection.release();
            return res.status(404).json({ message: 'Draf tidak ditemukan' });
        }
        if (drafts[0].status !== 'submitted') {
            connection.release();
            return res.status(400).json({ message: 'Hanya draf berstatus "submitted" yang dapat difinalisasi.' });
        }

        // Otomatis setujui item yang masih "pending" (belum diputuskan Kaprodi)
        await connection.query(
            'UPDATE procurement_items SET review_status = "approved" WHERE draft_id = ? AND review_status = "pending"',
            [id]
        );

        // Kunci draf sebagai finalized (status = approved)
        await connection.query(
            'UPDATE procurement_drafts SET status = "approved", updated_at = NOW() WHERE id = ?',
            [id]
        );

        // ── Materialisasi inventaris: tiap item 'inventaris' yang disetujui → 1 baris aset nyata ──
        // (per-baris item; BHP dilewati; idempotent via assets.source_item_id)
        const [approvedInvItems] = await connection.query(
            `SELECT pi.id, pi.name, pi.price
             FROM procurement_items pi
             WHERE pi.draft_id = ?
               AND pi.item_type = 'inventaris'
               AND pi.review_status = 'approved'
               AND NOT EXISTS (SELECT 1 FROM assets a WHERE a.source_item_id = pi.id)`,
            [id]
        );
        for (const item of approvedInvItems) {
            await connection.query(
                `INSERT INTO assets (name, condition_status, year, price, status, source_item_id, received_date)
                 VALUES (?, 'Baik', ?, ?, 'Baik', ?, NULL)`,
                [item.name, drafts[0].year, item.price || 0, item.id]
            );
        }

        await connection.commit();
        res.json({
            message: 'Draf berhasil difinalisasi. Item yang belum diputuskan otomatis disetujui.',
            assets_created: approvedInvItems.length
        });
    } catch (error) {
        await connection.rollback();
        console.error('finalizeDraft error:', error);
        res.status(500).json({ message: 'Server error: ' + (error.sqlMessage || error.message) });
    } finally {
        connection.release();
    }
};

// ─────────────────────────────────────────────────────────
// GET /api/procurement/stats — Hitung draf per status
// ─────────────────────────────────────────────────────────
exports.getStats = async (req, res) => {
    try {
        const [rows] = await db.query(
            'SELECT status, COUNT(*) AS count FROM procurement_drafts GROUP BY status'
        );
        const stats = { draft: 0, submitted: 0, approved: 0, rejected: 0, total: 0 };
        rows.forEach(r => {
            if (stats.hasOwnProperty(r.status)) stats[r.status] = Number(r.count);
            stats.total += Number(r.count);
        });
        res.json({ stats });
    } catch (error) {
        console.error('getStats error:', error);
        res.status(500).json({ message: 'Server error: ' + (error.sqlMessage || error.message) });
    }
};

// ─────────────────────────────────────────────────────────
// PATCH /api/procurement/:id/status — Update status draf (approve/reject)
// ─────────────────────────────────────────────────────────
exports.updateStatus = async (req, res) => {
    const { id } = req.params;
    const { status } = req.body;

    const validStatuses = ['approved', 'rejected'];
    if (!status || !validStatuses.includes(status)) {
        return res.status(400).json({ message: 'Status tidak valid. Harus "approved" atau "rejected".' });
    }

    try {
        const [existing] = await db.query('SELECT id, status FROM procurement_drafts WHERE id = ?', [id]);
        if (existing.length === 0) {
            return res.status(404).json({ message: 'Draf pengadaan tidak ditemukan' });
        }
        if (existing[0].status !== 'submitted') {
            return res.status(400).json({ message: 'Hanya draf yang telah diajukan (submitted) yang dapat disetujui atau ditolak.' });
        }

        await db.query('UPDATE procurement_drafts SET status = ?, updated_at = NOW() WHERE id = ?', [status, id]);
        res.json({ message: `Status draf berhasil diperbarui menjadi "${status}"` });
    } catch (error) {
        console.error('updateStatus error:', error);
        res.status(500).json({ message: 'Server error: ' + (error.sqlMessage || error.message) });
    }
};

// ─────────────────────────────────────────────────────────
// GET /api/procurement/assets/list — Dropdown Aset Inventaris
// ─────────────────────────────────────────────────────────
exports.getAssetsList = async (req, res) => {
    try {
        const [rows] = await db.query('SELECT id, name, code, status FROM assets ORDER BY name');
        res.json({ assets: rows });
    } catch (error) {
        console.error('getAssetsList error:', error);
        res.status(500).json({ message: 'Server error: ' + (error.sqlMessage || error.message) });
    }
};
