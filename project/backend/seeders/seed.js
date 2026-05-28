const mysql = require('mysql2/promise');
const bcrypt = require('bcrypt');
const path = require('path');
const fs = require('fs');

// Load .env manual dari folder parent backend
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
    const [roomRows] = await conn.query('SELECT * FROM rooms');
    const roomMap = Object.fromEntries(roomRows.map(r => [r.code, r.id]));
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
    const [userRows] = await conn.query('SELECT * FROM users');
    const userMap = Object.fromEntries(userRows.map(u => [u.email, u.id]));
    const kalabId = userMap['kalab@example.com'];
    console.log('✓ Users seeded');

    // ── Assets (Inventaris) ──────────────────────────────────
    console.log('🌱 Seeding assets (inventaris)...');
    const assets = [
        { name: 'PC Workstation Asus ROG',      code: 'KOM/PC/24/001', category: 'PC',       room: 'LAB-KOM-01', cond: 'Baik',              year: 2024, price: 25000000 },
        { name: 'Laptop Lenovo ThinkPad L14',   code: 'KOM/LAP/24/002', category: 'Laptop',   room: 'LAB-KOM-01', cond: 'Baik',              year: 2024, price: 15000000 },
        { name: 'Server Dell PowerEdge T150',   code: 'KOM/SRV/24/001', category: 'Server',   room: 'LAB-KOM-02', cond: 'Perlu Maintenance', year: 2023, price: 45000000 },
        { name: 'Switch Cisco Catalyst 2960',   code: 'KOM/SWT/23/001', category: 'Network',  room: 'LAB-KOM-02', cond: 'Baik',              year: 2023, price: 18000000 },
        { name: 'Projector Epson EB-X51',       code: 'MUL/PRJ/22/001', category: 'Projector',room: 'LAB-MUL-01', cond: 'Rusak Ringan',     year: 2022, price: 8500000  },
        { name: 'Camera Canon EOS 80D',         code: 'MUL/CAM/23/001', category: 'Kamera',   room: 'LAB-MUL-01', cond: 'Baik',              year: 2023, price: 16000000 },
        { name: 'UPS APC Easy 1000VA',          code: 'KOM/UPS/24/001', category: 'UPS',      room: 'LAB-KOM-02', cond: 'Baik',              year: 2024, price: 3500000  },
    ];
    for (const a of assets) {
        await conn.query(
            'INSERT INTO assets (name, code, category, room_id, condition_status, year, price, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)',
            [a.name, a.code, a.category, roomMap[a.room], a.cond, a.year, a.price, a.cond]
        );
    }
    const [assetRows] = await conn.query('SELECT * FROM assets');
    const assetIdMap = Object.fromEntries(assetRows.map(a => [a.code, a.id]));
    console.log(`✓ ${assets.length} assets seeded`);

    // ── Consumables (BHP) ────────────────────────────────────
    console.log('🌱 Seeding consumables (BHP)...');
    const consumables = [
        { name: 'Konektor RJ45 Cat6 Belden',    code: 'BHP/RJ45/001', category: 'Jaringan',    unit: 'box (100 pcs)', stock: 15, min: 5, price: 250000,  location: 'GDG-PEN-01' },
        { name: 'Kabel UTP Cat6 Belden',        code: 'BHP/UTP/001',  category: 'Jaringan',    unit: 'roll (305m)',   stock: 8,  min: 2, price: 1850000, location: 'GDG-PEN-01' },
        { name: 'Mouse Logitech B100',          code: 'BHP/MOU/001',  category: 'Aksesoris',   unit: 'pcs',           stock: 35, min: 10,price: 75000,   location: 'GDG-PEN-01' },
        { name: 'Keyboard Logitech K120',       code: 'BHP/KEY/001',  category: 'Aksesoris',   unit: 'pcs',           stock: 20, min: 5, price: 120000,  location: 'GDG-PEN-01' },
        { name: 'Thermal Paste Arctic MX-4',    code: 'BHP/THR/001',  category: 'Perawatan',   unit: 'tube (4g)',     stock: 12, min: 3, price: 95000,   location: 'GDG-PEN-01' },
        { name: 'Flashdisk SanDisk 32GB',       code: 'BHP/FLD/001',  category: 'Penyimpanan', unit: 'pcs',           stock: 25, min: 8, price: 65000,   location: 'GDG-PEN-01' },
        { name: 'Kabel HDMI 3 meter',           code: 'BHP/HDMI/001', category: 'Kabel',       unit: 'pcs',           stock: 18, min: 5, price: 45000,   location: 'GDG-PEN-01' },
        { name: 'Baterai CMOS CR2032 Maxell',   code: 'BHP/CMOS/001', category: 'Suku Cadang', unit: 'strip (5 pcs)', stock: 30, min: 10,price: 25000,   location: 'GDG-PEN-01' },
    ];
    for (const c of consumables) {
        await conn.query(
            'INSERT INTO consumables (name, code, category, unit, stock, min_stock, price, location, room_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)',
            [c.name, c.code, c.category, c.unit, c.stock, c.min, c.price, c.location, roomMap['GDG-PEN-01']]
        );
    }
    console.log(`✓ ${consumables.length} consumables seeded`);

    // ── Procurement Drafts & Items ───────────────────────────
    console.log('🌱 Seeding procurement drafts & items...');

    // 1. Draf Pengadaan Status: draft
    const [dr1] = await conn.query(
        `INSERT INTO procurement_drafts (title, year, status, created_by, created_at)
         VALUES (?, ?, ?, ?, ?)`,
        ['Pengadaan Laptop & Alat Lab Multimedia 2026', 2026, 'draft', kalabId, '2026-05-15 10:00:00']
    );
    const draftId1 = dr1.insertId;
    const items1 = [
        { type: 'inventaris', name: 'Laptop Lenovo ThinkPad L14', price: 15000000, qty: 3, link: 'https://www.tokopedia.com/search?st=product&q=thinkpad+l14', replaced: assetIdMap['KOM/LAP/24/002'] },
        { type: 'inventaris', name: 'Projector Epson EB-X51', price: 8500000, qty: 1, link: 'https://www.tokopedia.com/search?st=product&q=epson+eb-x51', replaced: assetIdMap['MUL/PRJ/22/001'] },
        { type: 'bhp', name: 'Flashdisk SanDisk 32GB', price: 65000, qty: 10, link: 'https://www.tokopedia.com/search?st=product&q=sandisk+32gb', replaced: null },
    ];
    for (const it of items1) {
        await conn.query(
            `INSERT INTO procurement_items (draft_id, item_type, name, price, quantity, purchase_link, replaced_asset_id)
             VALUES (?, ?, ?, ?, ?, ?, ?)`,
            [draftId1, it.type, it.name, it.price, it.qty, it.link, it.replaced]
        );
    }
    console.log('  ✓ Draft "Pengadaan Laptop & Alat Lab Multimedia 2026" (status: draft) seeded.');

    // 2. Draf Pengadaan Status: submitted (Diajukan)
    const [dr2] = await conn.query(
        `INSERT INTO procurement_drafts (title, year, status, created_by, created_at)
         VALUES (?, ?, ?, ?, ?)`,
        ['Pengadaan Jaringan Lab Komputer 2 2025', 2025, 'submitted', kalabId, '2025-11-20 09:30:00']
    );
    const draftId2 = dr2.insertId;
    const items2 = [
        { type: 'inventaris', name: 'Switch Cisco Catalyst 2960', price: 18000000, qty: 2, link: 'https://www.tokopedia.com/search?st=product&q=cisco+catalyst+2960', replaced: assetIdMap['KOM/SWT/23/001'] },
        { type: 'bhp', name: 'Kabel UTP Cat6 Belden', price: 1850000, qty: 3, link: 'https://www.tokopedia.com/search?st=product&q=kabel+utp+cat6+belden', replaced: null },
        { type: 'bhp', name: 'Konektor RJ45 Cat6 Belden', price: 250000, qty: 5, link: 'https://www.tokopedia.com/search?st=product&q=rj45+belden+cat6', replaced: null },
    ];
    for (const it of items2) {
        await conn.query(
            `INSERT INTO procurement_items (draft_id, item_type, name, price, quantity, purchase_link, replaced_asset_id)
             VALUES (?, ?, ?, ?, ?, ?, ?)`,
            [draftId2, it.type, it.name, it.price, it.qty, it.link, it.replaced]
        );
    }
    console.log('  ✓ Draft "Pengadaan Jaringan Lab Komputer 2 2025" (status: submitted / Diajukan) seeded.');

    // 3. Draf Pengadaan Status: approved (Diterima)
    const [dr3] = await conn.query(
        `INSERT INTO procurement_drafts (title, year, status, created_by, created_at)
         VALUES (?, ?, ?, ?, ?)`,
        ['Pengadaan PC & UPS Lab Komputer 1 2024', 2024, 'approved', kalabId, '2024-03-10 14:00:00']
    );
    const draftId3 = dr3.insertId;
    const items3 = [
        { type: 'inventaris', name: 'PC Workstation Asus ROG', price: 25000000, qty: 5, link: 'https://www.tokopedia.com/search?st=product&q=pc+asus+rog', replaced: assetIdMap['KOM/PC/24/001'] },
        { type: 'inventaris', name: 'UPS APC Easy 1000VA', price: 3500000, qty: 5, link: 'https://www.tokopedia.com/search?st=product&q=ups+apc+1000va', replaced: assetIdMap['KOM/UPS/24/001'] },
    ];
    for (const it of items3) {
        await conn.query(
            `INSERT INTO procurement_items (draft_id, item_type, name, price, quantity, purchase_link, replaced_asset_id)
             VALUES (?, ?, ?, ?, ?, ?, ?)`,
            [draftId3, it.type, it.name, it.price, it.qty, it.link, it.replaced]
        );
    }
    console.log('  ✓ Draft "Pengadaan PC & UPS Lab Komputer 1 2024" (status: approved / Diterima) seeded.');

    await conn.end();
    console.log('\n✅ Seeding selesai!');
}

seed().catch(err => {
    console.error('❌ Seeding gagal:', err.message);
    process.exit(1);
});
