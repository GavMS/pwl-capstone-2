const db = require('../config/db');

// GET /api/consumables — Daftar lengkap semua bahan habis pakai (BHP) beserta ruangan
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
