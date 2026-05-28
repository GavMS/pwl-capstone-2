const jwt = require('jsonwebtoken');

module.exports = (req, res, next) => {
    const authHeader = req.headers['authorization'];
    const token = authHeader && authHeader.split(' ')[1];

    if (!token) {
        return res.status(401).json({ message: 'No token provided' });
    }

    jwt.verify(token, process.env.JWT_SECRET || 'supersecretkey', async (err, decoded) => {
        if (err) {
            return res.status(403).json({ message: 'Failed to authenticate token' });
        }
        
        try {
            const db = require('../config/db');
            const [rows] = await db.query('SELECT is_active FROM users WHERE id = ?', [decoded.id]);
            if (rows.length === 0 || rows[0].is_active === 0) {
                return res.status(403).json({ message: 'Akun dinonaktifkan' });
            }
        } catch (dbErr) {
            console.error('Middleware DB Error:', dbErr);
        }

        req.user = decoded;
        next();
    });
};
