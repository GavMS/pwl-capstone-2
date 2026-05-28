const db = require('../config/db');

// GET /api/assets — Daftar lengkap semua aset inventaris beserta ruangan
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
