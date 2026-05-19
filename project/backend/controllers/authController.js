const bcrypt = require('bcrypt');
const jwt = require('jsonwebtoken');
const db = require('../config/db');

exports.login = async (req, res) => {
    const { email, password } = req.body;

    if (!email || !password) {
        return res.status(400).json({ message: 'Email and password are required' });
    }

    try {
        const query = `
            SELECT users.*, roles.name as role 
            FROM users 
            LEFT JOIN roles ON users.role_id = roles.id 
            WHERE users.email = ?
        `;
        const [rows] = await db.query(query, [email]);
        if (rows.length === 0) {
            return res.status(401).json({ message: 'Invalid credentials' });
        }

        const user = rows[0];
        const isMatch = await bcrypt.compare(password, user.password);

        if (!isMatch) {
            return res.status(401).json({ message: 'Invalid credentials' });
        }

        const token = jwt.sign(
            { id: user.id, role: user.role },
            process.env.JWT_SECRET || 'supersecretkey',
            { expiresIn: '1d' }
        );

        res.json({
            message: 'Login successful',
            token,
            user: {
                id: user.id,
                name: user.name,
                email: user.email,
                role: user.role
            }
        });
    } catch (error) {
        console.error('Login error:', error);
        res.status(500).json({ message: 'Server error' });
    }
};

exports.getMe = async (req, res) => {
    try {
        const [rows] = await db.query('SELECT id, name, email, role FROM users WHERE id = ?', [req.user.id]);
        if (rows.length === 0) {
            return res.status(404).json({ message: 'User not found' });
        }
        res.json({ user: rows[0] });
    } catch (error) {
        console.error('Get user error:', error);
        res.status(500).json({ message: 'Server error' });
    }
};

exports.register = async (req, res) => {
    const { name, email, password } = req.body;

    if (!name || !email || !password) {
        return res.status(400).json({ message: 'Name, email and password are required' });
    }

    try {
        // Check if email already exists
        const [existing] = await db.query('SELECT * FROM users WHERE email = ?', [email]);
        if (existing.length > 0) {
            return res.status(400).json({ message: 'Email sudah terdaftar' });
        }

        // Get default role "Staf Laboratorium"
        const [roleRows] = await db.query('SELECT id FROM roles WHERE name = ?', ['Staf Laboratorium']);
        if (roleRows.length === 0) {
            return res.status(500).json({ message: 'Default role Staf Laboratorium tidak ditemukan' });
        }
        const roleId = roleRows[0].id;

        // Hash password
        const hashedPassword = await bcrypt.hash(password, 10);

        // Insert user
        await db.query(
            'INSERT INTO users (name, email, password, role_id) VALUES (?, ?, ?, ?)',
            [name, email, hashedPassword, roleId]
        );

        res.status(201).json({ message: 'Registrasi berhasil' });
    } catch (error) {
        console.error('Register error:', error);
        res.status(500).json({ message: 'Server error saat registrasi' });
    }
};
