/**
 * reset-and-seed.js
 * Reset database, recreate semua tabel dengan schema terbaru,
 * dan isi dengan data contoh yang lengkap.
 *
 * Jalankan: node reset-and-seed.js
 */

const mysql  = require('mysql2/promise');
const bcrypt = require('bcrypt');
const path   = require('path');
const fs     = require('fs');

// ── Load .env ────────────────────────────────────────────────
const envContent = fs.readFileSync(path.resolve(__dirname, '.env'), 'utf8');
envContent.split('\n').forEach(line => {
    const [key, ...vals] = line.split('=');
    if (key && key.trim()) process.env[key.trim()] = vals.join('=').trim();
});

const DB_NAME = process.env.DB_NAME || 'capstone';

async function run() {
    // Koneksi awal tanpa pilih database (untuk DROP/CREATE db)
    const root = await mysql.createConnection({
        host    : process.env.DB_HOST     || 'localhost',
        user    : process.env.DB_USER     || 'root',
        password: process.env.DB_PASSWORD || '',
        port    : process.env.DB_PORT     || 3306,
    });

    console.log(`\n🗑️  Menghapus database "${DB_NAME}"...`);
    await root.query(`DROP DATABASE IF EXISTS \`${DB_NAME}\``);
    await root.query(`CREATE DATABASE \`${DB_NAME}\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci`);
    console.log(`✅ Database "${DB_NAME}" dibuat ulang.\n`);
    await root.end();

    // Koneksi ke database yang baru
    const db = await mysql.createConnection({
        host    : process.env.DB_HOST     || 'localhost',
        user    : process.env.DB_USER     || 'root',
        password: process.env.DB_PASSWORD || '',
        database: DB_NAME,
        port    : process.env.DB_PORT     || 3306,
    });

    // ── SCHEMA ──────────────────────────────────────────────
    console.log('📐 Membuat tabel...');

    await db.query(`
        CREATE TABLE roles (
            id   BIGINT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL
        )
    `);

    await db.query(`
        CREATE TABLE rooms (
            id          BIGINT AUTO_INCREMENT PRIMARY KEY,
            name        VARCHAR(255) NOT NULL UNIQUE,
            code        VARCHAR(255) NOT NULL UNIQUE,
            description TEXT NULL,
            created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )
    `);

    await db.query(`
        CREATE TABLE users (
            id         BIGINT AUTO_INCREMENT PRIMARY KEY,
            name       VARCHAR(255) NOT NULL,
            email      VARCHAR(255) NOT NULL UNIQUE,
            password   VARCHAR(255) NOT NULL,
            role_id    BIGINT NULL,
            roles_id   BIGINT NULL,
            room_id    BIGINT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE SET NULL
        )
    `);

    await db.query(`
        CREATE TABLE assets (
            id          BIGINT AUTO_INCREMENT PRIMARY KEY,
            name        VARCHAR(255) NOT NULL,
            code        VARCHAR(255) NULL UNIQUE,
            category    VARCHAR(100) NULL,
            room_id     BIGINT NULL,
            condition_status VARCHAR(100) NULL DEFAULT 'Baik',
            year        INT NULL,
            price       DECIMAL(15,2) NULL DEFAULT 0,
            description TEXT NULL,
            status      VARCHAR(100) NULL DEFAULT 'Baik',
            created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE SET NULL
        )
    `);

    await db.query(`
        CREATE TABLE consumables (
            id          BIGINT AUTO_INCREMENT PRIMARY KEY,
            name        VARCHAR(255) NOT NULL,
            code        VARCHAR(255) NULL UNIQUE,
            category    VARCHAR(100) NULL,
            unit        VARCHAR(100) NULL,
            stock       INT NULL DEFAULT 0,
            min_stock   INT NULL DEFAULT 0,
            price       DECIMAL(15,2) NULL DEFAULT 0,
            location    VARCHAR(100) NULL,
            room_id     BIGINT NULL,
            description TEXT NULL,
            created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE SET NULL
        )
    `);

    await db.query(`
        CREATE TABLE procurement_drafts (
            id         BIGINT AUTO_INCREMENT PRIMARY KEY,
            title      VARCHAR(255) NOT NULL,
            year       INT NOT NULL,
            status     VARCHAR(100) NOT NULL DEFAULT 'draft',
            created_by BIGINT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
        )
    `);

    await db.query(`
        CREATE TABLE procurement_items (
            id                 BIGINT AUTO_INCREMENT PRIMARY KEY,
            draft_id           BIGINT NOT NULL,
            item_type          VARCHAR(100) NOT NULL DEFAULT 'inventaris',
            name               VARCHAR(255) NOT NULL,
            price              DECIMAL(15,2) NOT NULL DEFAULT 0,
            quantity           INT NOT NULL DEFAULT 1,
            purchase_link      TEXT NULL,
            replaced_asset_id  BIGINT NULL,
            notes              TEXT NULL,
            created_at         TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at         TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (draft_id)          REFERENCES procurement_drafts(id) ON DELETE CASCADE,
            FOREIGN KEY (replaced_asset_id) REFERENCES assets(id) ON DELETE SET NULL
        )
    `);

    await db.query(`
        CREATE TABLE maintenance_logs (
            id                   BIGINT AUTO_INCREMENT PRIMARY KEY,
            asset_id             BIGINT NOT NULL,
            performed_by         BIGINT NULL,
            maintenance_date     DATE NOT NULL,
            description          TEXT NOT NULL,
            condition_before     VARCHAR(100) NULL,
            condition_after      VARCHAR(100) NOT NULL DEFAULT 'Baik',
            cost                 DECIMAL(15,2) NULL DEFAULT 0,
            notes                TEXT NULL,
            created_at           TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at           TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (asset_id)     REFERENCES assets(id) ON DELETE CASCADE,
            FOREIGN KEY (performed_by) REFERENCES users(id) ON DELETE SET NULL
        )
    `);

    await db.query(`
        CREATE TABLE maintenance_consumables (
            id              BIGINT AUTO_INCREMENT PRIMARY KEY,
            log_id          BIGINT NOT NULL,
            consumable_id   BIGINT NOT NULL,
            quantity_used   INT NOT NULL DEFAULT 1,
            created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (log_id)        REFERENCES maintenance_logs(id) ON DELETE CASCADE,
            FOREIGN KEY (consumable_id) REFERENCES consumables(id) ON DELETE CASCADE
        )
    `);

    console.log('✅ Semua tabel berhasil dibuat.\n');


    // ── SEED: ROLES ─────────────────────────────────────────
    console.log('🌱 Seeding roles...');
    const roleNames = [
        'Administrator', 'Kepala Laboratorium', 'Ketua Program Studi',
        'Staf Administrasi', 'Staf Laboratorium'
    ];
    for (const name of roleNames) {
        await db.query('INSERT INTO roles (name) VALUES (?)', [name]);
    }
    const [roles] = await db.query('SELECT * FROM roles');
    const roleMap = Object.fromEntries(roles.map(r => [r.name, r.id]));
    console.log('  ✓ Roles OK');

    // ── SEED: ROOMS ─────────────────────────────────────────
    console.log('🌱 Seeding rooms...');
    const rooms = [
        { name: 'Laboratorium Komputer 1',       code: 'LAB-KOM-01', description: 'Laboratorium untuk praktikum pemrograman dasar dan algoritma.' },
        { name: 'Laboratorium Jaringan Komputer',code: 'LAB-JAR-01', description: 'Laboratorium untuk praktikum jaringan dan keamanan komputer.' },
        { name: 'Ruang Server Utama',            code: 'SRV-01',     description: 'Ruang server dan penyimpanan data center Teknik Informatika.' },
        { name: 'Gudang Peralatan IT',           code: 'GDG-IT-01',  description: 'Gudang penyimpanan hardware, komponen komputer, dan kabel.' },
    ];
    for (const r of rooms) {
        await db.query(
            'INSERT INTO rooms (name, code, description) VALUES (?, ?, ?)',
            [r.name, r.code, r.description]
        );
    }
    const [roomRows] = await db.query('SELECT * FROM rooms');
    const roomMap = Object.fromEntries(roomRows.map(r => [r.code, r.id]));
    console.log('  ✓ Rooms OK');

    // ── SEED: USERS ─────────────────────────────────────────
    console.log('🌱 Seeding users...');
    const hashedPwd = await bcrypt.hash('password123', 10);
    const users = [
        { name: 'Administrator',          email: 'admin@example.com',     role: 'Administrator' },
        { name: 'Dr. Andreas, S.Kom., M.T.', email: 'kalab@example.com',  role: 'Kepala Laboratorium' },
        { name: 'Mewati Ayub, S.Kom., M.T.', email: 'kaprodi@example.com',role: 'Ketua Program Studi' },
        { name: 'Staf Administrasi',      email: 'stafadmin@example.com', role: 'Staf Administrasi' },
        { name: 'Staf Laboratorium',      email: 'staflab@example.com',   role: 'Staf Laboratorium' },
    ];
    for (const u of users) {
        const rid = roleMap[u.role];
        await db.query(
            'INSERT INTO users (name, email, password, role_id, roles_id) VALUES (?, ?, ?, ?, ?)',
            [u.name, u.email, hashedPwd, rid, rid]
        );
        console.log(`  → ${u.email}  /  password123`);
    }
    const [userRows] = await db.query('SELECT * FROM users');
    const userMap   = Object.fromEntries(userRows.map(u => [u.email, u.id]));
    const kalabId   = userMap['kalab@example.com'];
    console.log('  ✓ Users OK');

    // ── SEED: ASSETS (Inventaris) ────────────────────────────
    console.log('🌱 Seeding assets (inventaris)...');
    const assets = [
        { name: 'PC Desktop Dell Optiplex 7090', code: 'IT/PC/23/001', category: 'Komputer',  room: 'LAB-KOM-01', cond: 'Baik',              year: 2023, price: 15500000 },
        { name: 'Switch Cisco Catalyst 2960',    code: 'IT/SW/22/001', category: 'Jaringan',  room: 'LAB-JAR-01', cond: 'Perlu Maintenance', year: 2022, price: 12000000 },
        { name: 'Router MikroTik CCR1009',       code: 'IT/RT/23/001', category: 'Jaringan',  room: 'LAB-JAR-01', cond: 'Baik',              year: 2023, price: 8500000 },
        { name: 'Server HP ProLiant DL380',      code: 'IT/SRV/21/001',category: 'Server',    room: 'SRV-01',     cond: 'Baik',              year: 2021, price: 120000000 },
    ];
    for (const a of assets) {
        await db.query(
            'INSERT INTO assets (name, code, category, room_id, condition_status, year, price, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)',
            [a.name, a.code, a.category, roomMap[a.room], a.cond, a.year, a.price, a.cond]
        );
    }
    const [assetRows] = await db.query('SELECT * FROM assets');
    const assetMap   = Object.fromEntries(assetRows.map(a => [a.code, a.id]));
    console.log(`  ✓ ${assets.length} assets OK`);

    // ── SEED: CONSUMABLES (BHP) ──────────────────────────────
    console.log('🌱 Seeding consumables (BHP)...');
    const consumables = [
        { name: 'Kabel UTP Cat6 Belden (Roll)',     code: 'BHP/KBL/001', category: 'Kabel',    unit: 'roll (305m)', stock: 3,  min: 1,  price: 1850000, location: 'GDG-IT-01' },
        { name: 'Konektor RJ45 CommScope',          code: 'BHP/RJ/001',  category: 'Konektor', unit: 'box (100 pcs)', stock: 5,  min: 2,  price: 350000,  location: 'GDG-IT-01' },
        { name: 'Thermal Paste Arctic MX-4',        code: 'BHP/TP/001',  category: 'Hardware', unit: 'tube (4g)',   stock: 12, min: 5,  price: 120000,  location: 'GDG-IT-01' },
        { name: 'Baterai CMOS CR2032',              code: 'BHP/BTR/001', category: 'Hardware', unit: 'pcs',         stock: 50, min: 20, price: 15000,   location: 'GDG-IT-01' },
    ];
    for (const c of consumables) {
        await db.query(
            'INSERT INTO consumables (name, code, category, unit, stock, min_stock, price, location, room_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)',
            [c.name, c.code, c.category, c.unit, c.stock, c.min, c.price, c.location, roomMap['GDG-IT-01']]
        );
    }
    console.log(`  ✓ ${consumables.length} consumables OK`);

    // ── SEED: PROCUREMENT DRAFTS + ITEMS ───────────────────
    console.log('🌱 Seeding procurement drafts...');

    // Draf 1: Finalized/Submitted 2025
    const [dr1] = await db.query(
        `INSERT INTO procurement_drafts (title, year, status, created_by, created_at)
         VALUES (?, ?, ?, ?, ?)`,
        ['Pengadaan Lab Jaringan Komputer 2025', 2025, 'submitted', kalabId, '2025-02-24 08:00:00']
    );
    const draftId1 = dr1.insertId;
    const items1 = [
        { type: 'inventaris', name: 'Switch Cisco Catalyst 2960', price: 12000000, qty: 2, link: 'https://www.bhinneka.com/cisco-catalyst-2960', replaced: assetMap['IT/SW/22/001'] },
        { type: 'bhp',        name: 'Kabel UTP Cat6 Belden',      price: 1850000,  qty: 4, link: 'https://www.bhinneka.com/belden-cat6',       replaced: null },
    ];
    for (const it of items1) {
        await db.query(
            `INSERT INTO procurement_items (draft_id, item_type, name, price, quantity, purchase_link, replaced_asset_id)
             VALUES (?, ?, ?, ?, ?, ?, ?)`,
            [draftId1, it.type, it.name, it.price, it.qty, it.link, it.replaced]
        );
    }
    console.log(`  ✓ Draft 1 "Pengadaan Lab Jaringan Komputer 2025" (submitted) — ${items1.length} items`);

    // Draf 2: Draft 2026
    const [dr2] = await db.query(
        `INSERT INTO procurement_drafts (title, year, status, created_by, created_at)
         VALUES (?, ?, ?, ?, ?)`,
        ['Pengadaan Lab Komputer 2026', 2026, 'draft', kalabId, '2026-02-12 09:30:00']
    );
    const draftId2 = dr2.insertId;
    const items2 = [
        { type: 'inventaris', name: 'PC Desktop Dell Optiplex 7090', price: 15500000, qty: 10, link: 'https://www.bhinneka.com/dell-optiplex-7090', replaced: null },
        { type: 'bhp',        name: 'Thermal Paste Arctic MX-4',     price: 120000,   qty: 5,  link: 'https://www.tokopedia.com/arctic-mx-4',   replaced: null },
    ];
    for (const it of items2) {
        await db.query(
            `INSERT INTO procurement_items (draft_id, item_type, name, price, quantity, purchase_link, replaced_asset_id)
             VALUES (?, ?, ?, ?, ?, ?, ?)`,
            [draftId2, it.type, it.name, it.price, it.qty, it.link, it.replaced]
        );
    }
    console.log(`  ✓ Draft 2 "Pengadaan Lab Komputer 2026" (draft) — ${items2.length} items`);

    // ── SEED: MAINTENANCE LOGS ──────────────────────────────
    console.log('🌱 Seeding maintenance logs...');

    const stafLabId = userMap['staflab@example.com'];

    // Ambil ID consumables dari DB setelah seed
    const [consRows] = await db.query('SELECT * FROM consumables');
    const consMap = Object.fromEntries(consRows.map(c => [c.code, { id: c.id, stock: c.stock }]));

    // Helper untuk insert log dan catat BHP yang digunakan
    async function seedLog({ assetCode, date, desc, condBefore, condAfter, cost, notes, bhpUsed }) {
        const assetId = assetMap[assetCode];
        if (!assetId) { console.log(`  ⚠ Aset ${assetCode} tidak ditemukan, skip.`); return; }

        const [logRes] = await db.query(
            `INSERT INTO maintenance_logs
             (asset_id, performed_by, maintenance_date, description, condition_before, condition_after, cost, notes, created_at)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)`,
            [assetId, stafLabId, date, desc, condBefore, condAfter, cost, notes, `${date} 08:00:00`]
        );
        const logId = logRes.insertId;

        // Update kondisi aset
        await db.query(
            'UPDATE assets SET condition_status=?, status=?, updated_at=NOW() WHERE id=?',
            [condAfter, condAfter, assetId]
        );

        // Catat BHP yang digunakan & kurangi stok
        for (const { code, qty } of (bhpUsed || [])) {
            const cons = consMap[code];
            if (!cons) { console.log(`  ⚠ BHP ${code} tidak ditemukan, skip.`); continue; }
            await db.query(
                'INSERT INTO maintenance_consumables (log_id, consumable_id, quantity_used) VALUES (?, ?, ?)',
                [logId, cons.id, qty]
            );
            await db.query(
                'UPDATE consumables SET stock = stock - ?, updated_at=NOW() WHERE id=?',
                [qty, cons.id]
            );
            cons.stock -= qty; // update local cache
        }
    }

    await seedLog({
        assetCode  : 'IT/PC/23/001',
        date       : '2026-04-10',
        desc       : 'Pembersihan debu pada heatsink dan penggantian thermal paste CPU',
        condBefore : 'Baik',
        condAfter  : 'Baik',
        cost       : 0,
        notes      : 'Suhu CPU kembali normal, di bawah 40C saat idle.',
        bhpUsed    : [
            { code: 'BHP/TP/001', qty: 1 },
        ],
    });

    await seedLog({
        assetCode  : 'IT/SW/22/001',
        date       : '2026-04-22',
        desc       : 'Crimping ulang kabel uplink switch karena koneksi tidak stabil',
        condBefore : 'Perlu Maintenance',
        condAfter  : 'Perlu Maintenance',
        cost       : 50000,
        notes      : 'Konektor RJ45 diganti baru. Port switch nomor 4 masih bermasalah, perlu dianalisis lebih lanjut.',
        bhpUsed    : [
            { code: 'BHP/RJ/001', qty: 2 },
        ],
    });

    await seedLog({
        assetCode  : 'IT/SRV/21/001',
        date       : '2026-05-05',
        desc       : 'Penggantian baterai CMOS motherboard server',
        condBefore : 'Perlu Maintenance',
        condAfter  : 'Baik',
        cost       : 0,
        notes      : 'Tanggal dan waktu BIOS sering reset, baterai CMOS berhasil diganti.',
        bhpUsed    : [
            { code: 'BHP/BTR/001', qty: 1 },
        ],
    });

    console.log('  ✓ 3 maintenance logs OK');

    await db.end();


    console.log('\n════════════════════════════════════════════════');
    console.log('✅  Database reset & seed selesai!');
    console.log('────────────────────────────────────────────────');
    console.log('📋  Akun Login:');
    console.log('    admin@example.com      → Administrator');
    console.log('    kalab@example.com      → Kepala Laboratorium');
    console.log('    kaprodi@example.com    → Ketua Program Studi');
    console.log('    stafadmin@example.com  → Staf Administrasi');
    console.log('    staflab@example.com    → Staf Laboratorium');
    console.log('    Password semua: password123');
    console.log('════════════════════════════════════════════════\n');
}

run().catch(err => {
    console.error('\n❌ Error:', err.message || err);
    process.exit(1);
});
