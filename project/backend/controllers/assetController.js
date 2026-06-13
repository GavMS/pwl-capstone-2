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
            SELECT a.id, a.name, a.label_number, a.qr_path, a.univ_qr_path, a.status, a.condition_status,
                   a.source_item_id,
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
    const { label_number, received_date, qr_path, univ_qr_path } = req.body;

    const qrProvided = qr_path !== undefined && qr_path !== null && String(qr_path).trim() !== '';
    const univQrProvided = univ_qr_path !== undefined && univ_qr_path !== null && String(univ_qr_path).trim() !== '';

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

        let updateQuery = 'UPDATE assets SET label_number = ?, updated_at = NOW()';
        const params = [label_number?.trim() || null];

        // received_date hanya di-update jika dikirim (bukan undefined)
        // Ini mencegah penghapusan tanggal saat saveLabel dipanggil tanpa received_date
        const receivedDateProvided = received_date !== undefined;
        if (receivedDateProvided) {
            updateQuery += ', received_date = ?';
            params.push(received_date || null);
        }

        if (qrProvided) {
            updateQuery += ', qr_path = ?';
            params.push(qr_path.trim());
        }
        if (univQrProvided) {
            updateQuery += ', univ_qr_path = ?';
            params.push(univ_qr_path.trim());
        }
        updateQuery += ' WHERE id = ?';
        params.push(id);

        await connection.query(updateQuery, params);

        // Milestone C — pensiunkan aset lama jika barang penggantinya sudah diterima
        if (receivedDateProvided && received_date && asset.source_item_id) {
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

// ─────────────────────────────────────────────────────────
// PATCH /api/assets/by-source/:sourceId/received
// Bulk-update received_date semua unit dari satu procurement item
// ─────────────────────────────────────────────────────────
exports.setReceivedBySource = async (req, res) => {
    const { sourceId } = req.params;
    const { received_date } = req.body;

    if (!received_date) {
        return res.status(400).json({ message: 'Tanggal diterima wajib diisi.' });
    }

    const connection = await db.getConnection();
    await connection.beginTransaction();
    try {
        // Ambil semua aset dengan source_item_id = sourceId
        const [assets] = await connection.query(
            'SELECT id FROM assets WHERE source_item_id = ?',
            [sourceId]
        );
        if (assets.length === 0) {
            await connection.rollback();
            connection.release();
            return res.status(404).json({ message: 'Tidak ada aset ditemukan untuk procurement item ini.' });
        }

        // Update received_date semua unit
        await connection.query(
            'UPDATE assets SET received_date = ?, updated_at = NOW() WHERE source_item_id = ?',
            [received_date, sourceId]
        );

        // Milestone C: jika ada replaced_asset_id di procurement item ini, pensiunkan
        const [[item]] = await connection.query(
            'SELECT replaced_asset_id FROM procurement_items WHERE id = ?',
            [sourceId]
        );
        if (item && item.replaced_asset_id) {
            await connection.query(
                `UPDATE assets SET status = 'Diganti', condition_status = 'Diganti', updated_at = NOW() WHERE id = ?`,
                [item.replaced_asset_id]
            );
        }

        await connection.commit();
        res.json({ message: `Tanggal diterima berhasil diperbarui untuk ${assets.length} unit.`, updated: assets.length });
    } catch (error) {
        await connection.rollback();
        console.error('setReceivedBySource error:', error);
        res.status(500).json({ message: 'Server error: ' + (error.sqlMessage || error.message) });
    } finally {
        connection.release();
    }
};
