const db = require('../config/db');

// ─────────────────────────────────────────────────────────
// GET /api/assets — Daftar lengkap semua aset inventaris beserta ruangan
// ─────────────────────────────────────────────────────────
exports.getAllAssets = async (req, res) => {
    try {
        const query = `
            SELECT a.*, r.code AS room_code, r.name AS room_name
            FROM assets a
            LEFT JOIN rooms r ON a.room_id = r.id
            ORDER BY a.created_at DESC
        `;
        const [rows] = await db.query(query);
        res.json({ assets: rows });
    } catch (error) {
        console.error('getAllAssets error:', error);
        res.status(500).json({ message: 'Server error: ' + (error.sqlMessage || error.message) });
    }
};

// ─────────────────────────────────────────────────────────
// GET /api/assets/procurement — Aset hasil materialisasi pengadaan
// (untuk halaman labeling Staf Admin). Hanya aset dengan source_item_id.
// ─────────────────────────────────────────────────────────
exports.getProcurementAssets = async (req, res) => {
    try {
        const query = `
            SELECT a.id, a.name, a.label_number, a.qr_path, a.status, a.condition_status,
                   DATE_FORMAT(a.received_date, '%Y-%m-%d') AS received_date,
                   pi.quantity, pi.item_type, pi.replaced_asset_id,
                   pd.id AS draft_id, pd.title AS draft_title, pd.year AS draft_year,
                   ra.name AS replaced_asset_name, ra.code AS replaced_asset_code
            FROM assets a
            JOIN procurement_items pi   ON a.source_item_id = pi.id
            JOIN procurement_drafts pd  ON pi.draft_id = pd.id
            LEFT JOIN assets ra         ON pi.replaced_asset_id = ra.id
            WHERE a.source_item_id IS NOT NULL
            ORDER BY pd.year DESC, a.id ASC
        `;
        const [rows] = await db.query(query);
        res.json({ assets: rows });
    } catch (error) {
        console.error('getProcurementAssets error:', error);
        res.status(500).json({ message: 'Server error: ' + (error.sqlMessage || error.message) });
    }
};

// ─────────────────────────────────────────────────────────
// PATCH /api/assets/:id — Update label, QR, & tanggal terima (Staf Admin)
// Jika barang diterima & item-nya menggantikan aset lama → aset lama "Diganti".
// ─────────────────────────────────────────────────────────
exports.updateAsset = async (req, res) => {
    const { id } = req.params;
    const { label_number, received_date, qr_path } = req.body;

    // qr_path hanya di-update jika dikirim (agar QR lama tidak terhapus saat re-save tanpa QR baru)
    const qrProvided = qr_path !== undefined && qr_path !== null && String(qr_path).trim() !== '';

    const connection = await db.getConnection();
    await connection.beginTransaction();
    try {
        const [[asset]] = await connection.query(
            'SELECT id, source_item_id FROM assets WHERE id = ?', [id]
        );
        if (!asset) {
            await connection.rollback();
            connection.release();
            return res.status(404).json({ message: 'Aset tidak ditemukan.' });
        }

        if (qrProvided) {
            await connection.query(
                `UPDATE assets
                 SET label_number = ?, received_date = ?, qr_path = ?, updated_at = NOW()
                 WHERE id = ?`,
                [label_number?.trim() || null, received_date || null, qr_path.trim(), id]
            );
        } else {
            await connection.query(
                `UPDATE assets
                 SET label_number = ?, received_date = ?, updated_at = NOW()
                 WHERE id = ?`,
                [label_number?.trim() || null, received_date || null, id]
            );
        }

        // Milestone C — pensiunkan aset lama jika barang penggantinya sudah diterima
        if (received_date && asset.source_item_id) {
            const [[item]] = await connection.query(
                'SELECT replaced_asset_id FROM procurement_items WHERE id = ?',
                [asset.source_item_id]
            );
            if (item && item.replaced_asset_id) {
                await connection.query(
                    `UPDATE assets SET status = 'Diganti', condition_status = 'Diganti', updated_at = NOW() WHERE id = ?`,
                    [item.replaced_asset_id]
                );
            }
        }

        await connection.commit();
        res.json({ message: 'Data aset berhasil diperbarui.' });
    } catch (error) {
        await connection.rollback();
        if (error.code === 'ER_DUP_ENTRY') {
            return res.status(409).json({ message: 'Nomor label sudah digunakan aset lain. Gunakan nomor berbeda.' });
        }
        console.error('updateAsset error:', error);
        res.status(500).json({ message: 'Server error: ' + (error.sqlMessage || error.message) });
    } finally {
        connection.release();
    }
};
