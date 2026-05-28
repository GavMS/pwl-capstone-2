const mysql = require('mysql2/promise');
const bcrypt = require('bcrypt');
const path = require('path');
const fs = require('fs');

// Load .env
const envContent = fs.readFileSync(path.resolve(__dirname, '../.env'), 'utf8');
envContent.split('\n').forEach(line => {
    const [key, ...vals] = line.split('=');
    if (key && key.trim()) process.env[key.trim()] = vals.join('=').trim();
});

async function seed() {
    const conn = await mysql.createConnection({
        host: process.env.DB_HOST || 'localhost',
        user: process.env.DB_USER || 'root',
        password: process.env.DB_PASSWORD || '',
        database: process.env.DB_NAME || 'capstone',
        port: process.env.DB_PORT || 3306,
    });

    console.log('🌱 Seeding database...\n');

    // ── Truncate (urutan terbalik karena FK) ──────────────────
    await conn.query('SET FOREIGN_KEY_CHECKS = 0');
    await conn.query('TRUNCATE TABLE procurement_items');
    await conn.query('TRUNCATE TABLE procurement_drafts');
    await conn.query('TRUNCATE TABLE consumables');
    await conn.query('TRUNCATE TABLE assets');
    await conn.query('TRUNCATE TABLE users');
    await conn.query('TRUNCATE TABLE rooms');
    await conn.query('TRUNCATE TABLE roles');
    await conn.query('SET FOREIGN_KEY_CHECKS = 1');
    console.log('✓ Semua tabel dikosongkan');

    // ── Roles ─────────────────────────────────────────────────
    const roleNames = ['Administrator', 'Kepala Laboratorium', 'Ketua Program Studi', 'Staf Administrasi', 'Staf Laboratorium'];
    for (const name of roleNames) {
        await conn.query('INSERT INTO roles (name) VALUES (?)', [name]);
    }
    const [roles] = await conn.query('SELECT * FROM roles');
    const roleMap = Object.fromEntries(roles.map(r => [r.name, r.id]));
    console.log('✓ Roles seeded');

    // ── Rooms ─────────────────────────────────────────────────
    const rooms = [
        { name: 'Laboratorium Komputer 1', code: 'LAB-KOM-01', description: 'Laboratorium untuk praktikum rekayasa perangkat lunak dan pemrograman dasar.' },
        { name: 'Laboratorium Komputer 2', code: 'LAB-KOM-02', description: 'Laboratorium untuk praktikum jaringan dan keamanan komputer.' },
        { name: 'Laboratorium Multimedia', code: 'LAB-MUL-01', description: 'Laboratorium khusus desain grafis, editing video, dan multimedia.' },
        { name: 'Gudang Penyimpanan Alat', code: 'GDG-PEN-01', description: 'Ruangan penyimpanan untuk aset tidak aktif, hardware, dan bahan habis pakai (BHP).' },
    ];
    for (const r of rooms) {
        await conn.query('INSERT INTO rooms (name, code, description) VALUES (?, ?, ?)', [r.name, r.code, r.description]);
    }
    console.log('✓ Rooms seeded');

    // ── Users ─────────────────────────────────────────────────
    const hashedPwd = await bcrypt.hash('password123', 10);
    const users = [
        { name: 'Admin',            email: 'admin@example.com',     role: 'Administrator' },
        { name: 'Kepala Lab',       email: 'kalab@example.com',     role: 'Kepala Laboratorium' },
        { name: 'Kaprodi',          email: 'kaprodi@example.com',   role: 'Ketua Program Studi' },
        { name: 'Staf Administrasi',email: 'stafadmin@example.com', role: 'Staf Administrasi' },
        { name: 'Staf Laboratorium',email: 'staflab@example.com',   role: 'Staf Laboratorium' },
    ];
    for (const u of users) {
        const rid = roleMap[u.role];
        await conn.query(
            'INSERT INTO users (name, email, password, role_id, roles_id) VALUES (?, ?, ?, ?, ?)',
            [u.name, u.email, hashedPwd, rid, rid]
        );
        console.log(`  → ${u.email} / password123`);
    }
    console.log('✓ Users seeded');

    await conn.end();
    console.log('\n✅ Seeding selesai!');
}

seed().catch(err => {
    console.error('❌ Seeding gagal:', err.message);
    process.exit(1);
});
