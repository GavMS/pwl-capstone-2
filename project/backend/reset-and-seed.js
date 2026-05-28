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
        { name: 'Laboratorium Biologi 1',       code: 'LAB-BIO-01', description: 'Laboratorium praktikum biologi umum dan mikrobiologi.' },
        { name: 'Laboratorium Biologi 2',        code: 'LAB-BIO-02', description: 'Laboratorium biologi molekuler dan genetika.' },
        { name: 'Laboratorium Kimia',            code: 'LAB-KIM-01', description: 'Laboratorium kimia dasar dan analitik.' },
        { name: 'Ruang Penyimpanan Reagen',      code: 'STR-01',     description: 'Ruangan penyimpanan bahan habis pakai dan reagen.' },
        { name: 'Gudang Peralatan',              code: 'GDG-01',     description: 'Gudang penyimpanan peralatan dan instrumen lab.' },
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
        { name: 'Dr. Siti Nurhaliza',     email: 'kalab@example.com',     role: 'Kepala Laboratorium' },
        { name: 'Prof. Budi Santoso',     email: 'kaprodi@example.com',   role: 'Ketua Program Studi' },
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
        { name: 'Mikroskop Olympus CX23',       code: 'BIO/MIC/24/001', category: 'Mikroskop',   room: 'LAB-BIO-01', cond: 'Baik',              year: 2024, price: 32500000 },
        { name: 'Sentrifus Hettich EBA 200',    code: 'BIO/SEN/24/002', category: 'Sentrifus',   room: 'LAB-BIO-01', cond: 'Baik',              year: 2024, price: 28900000 },
        { name: 'Mesin PCR Bio-Rad T100',       code: 'BIO/PCR/24/001', category: 'PCR',         room: 'LAB-BIO-01', cond: 'Perlu Maintenance', year: 2023, price: 78500000 },
        { name: 'Inkubator Memmert IN30',       code: 'BIO/INK/22/001', category: 'Inkubator',   room: 'LAB-BIO-02', cond: 'Rusak Ringan',     year: 2022, price: 21500000 },
        { name: 'Autoclave Hirayama HV-110',    code: 'BIO/AUT/23/001', category: 'Autoclave',   room: 'LAB-BIO-02', cond: 'Baik',              year: 2023, price: 45000000 },
        { name: 'Spektrofotometer UV-Vis',      code: 'KIM/SPK/22/001', category: 'Spektrofotometer', room: 'LAB-KIM-01', cond: 'Baik',        year: 2022, price: 62000000 },
        { name: 'Timbangan Analitik Ohaus',     code: 'KIM/TIM/23/001', category: 'Timbangan',   room: 'LAB-KIM-01', cond: 'Baik',              year: 2023, price: 15500000 },
        { name: 'Oven Laboratorium Memmert',    code: 'BIO/OVN/21/001', category: 'Oven',        room: 'LAB-BIO-01', cond: 'Perlu Maintenance', year: 2021, price: 18000000 },
        { name: 'Lemari Asam Lokal',            code: 'KIM/LAS/20/001', category: 'Lemari Asam', room: 'LAB-KIM-01', cond: 'Rusak Berat',      year: 2020, price: 35000000 },
        { name: 'Vortex Mixer IKA MS3',         code: 'BIO/VOR/24/001', category: 'Vortex',      room: 'LAB-BIO-02', cond: 'Baik',              year: 2024, price: 8500000  },
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
        { name: 'Sarung Tangan Nitril ukuran M',    code: 'BHP/STG/001', category: 'APD',     unit: 'box (100 pcs)', stock: 24, min: 10, price: 95000,  location: 'STR-01' },
        { name: 'Masker Bedah 3-ply',               code: 'BHP/MSK/001', category: 'APD',     unit: 'box (50 pcs)',  stock: 18, min:  8, price: 45000,  location: 'STR-01' },
        { name: 'Alkohol 70% Teknis',               code: 'BHP/ALK/001', category: 'Reagen',  unit: 'liter',         stock: 45, min: 20, price: 32000,  location: 'STR-01' },
        { name: 'Aquades',                          code: 'BHP/AQD/001', category: 'Reagen',  unit: 'liter',         stock:120, min: 30, price:  8000,  location: 'STR-01' },
        { name: 'Tabung Reaksi Pyrex 16mm',         code: 'BHP/TBR/001', category: 'Gelas',   unit: 'lusin',         stock: 30, min: 10, price: 75000,  location: 'STR-01' },
        { name: 'Cover Glass 22x22mm',              code: 'BHP/CVG/001', category: 'Gelas',   unit: 'box (100 pcs)', stock:  3, min:  5, price: 28000,  location: 'STR-01' },
        { name: 'Tissue Culture Flask T-25',        code: 'BHP/TCF/001', category: 'Kultur',  unit: 'pcs',           stock: 25, min: 10, price: 42000,  location: 'STR-01' },
        { name: 'Eppendorf Tube 1.5mL',             code: 'BHP/EPP/001', category: 'Plastik', unit: 'bag (500 pcs)', stock: 12, min:  5, price: 95000,  location: 'STR-01' },
        { name: 'Tip Micropipet 1000µL',            code: 'BHP/TIP/001', category: 'Plastik', unit: 'rak (96 pcs)',  stock: 20, min:  8, price: 35000,  location: 'STR-01' },
        { name: 'Filter Membran 0.22µm',            code: 'BHP/FLT/001', category: 'Filter',  unit: 'pcs',           stock:  2, min:  5, price:185000,  location: 'STR-01' },
        { name: 'Kertas Saring Whatman No.1',       code: 'BHP/KSR/001', category: 'Filter',  unit: 'pack (100 pcs)',stock:  8, min:  5, price: 95000,  location: 'STR-01' },
        { name: 'Media Agar NA (Nutrient Agar)',    code: 'BHP/MNA/001', category: 'Media',   unit: 'gram (500g)',   stock:  6, min:  3, price:285000,  location: 'STR-01' },
        { name: 'HCl 37% pro-analysis',             code: 'BHP/HCL/001', category: 'Reagen',  unit: 'liter',         stock:  4, min:  2, price:125000,  location: 'STR-01' },
        { name: 'NaOH Padatan p.a.',                code: 'BHP/NAO/001', category: 'Reagen',  unit: 'kg',            stock:  3, min:  2, price:145000,  location: 'STR-01' },
        { name: 'Botol Kaca Schott 500mL',          code: 'BHP/BTL/001', category: 'Gelas',   unit: 'pcs',           stock: 15, min:  5, price: 75000,  location: 'STR-01' },
    ];
    for (const c of consumables) {
        await db.query(
            'INSERT INTO consumables (name, code, category, unit, stock, min_stock, price, location, room_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)',
            [c.name, c.code, c.category, c.unit, c.stock, c.min, c.price, c.location, roomMap['STR-01']]
        );
    }
    console.log(`  ✓ ${consumables.length} consumables OK`);

    // ── SEED: PROCUREMENT DRAFTS + ITEMS ───────────────────
    console.log('🌱 Seeding procurement drafts...');

    // Draf 1: Finalized/Submitted 2025
    const [dr1] = await db.query(
        `INSERT INTO procurement_drafts (title, year, status, created_by, created_at)
         VALUES (?, ?, ?, ?, ?)`,
        ['Pengadaan Tahunan Lab Biologi 2025', 2025, 'submitted', kalabId, '2025-02-24 08:00:00']
    );
    const draftId1 = dr1.insertId;
    const items1 = [
        { type: 'inventaris', name: 'Mikroskop Olympus CX23',     price: 32500000, qty: 2, link: 'https://www.tokopedia.com/search?st=product&q=mikroskop+olympus+cx23',          replaced: assetMap['BIO/MIC/24/001'] },
        { type: 'inventaris', name: 'Autoclave Hirayama HV-110',  price: 45000000, qty: 1, link: 'https://www.tokopedia.com/search?st=product&q=autoclave+hirayama',              replaced: null },
        { type: 'inventaris', name: 'Inkubator Memmert IN55',     price: 38000000, qty: 1, link: 'https://www.tokopedia.com/search?st=product&q=inkubator+memmert',               replaced: assetMap['BIO/INK/22/001'] },
        { type: 'bhp',        name: 'Sarung Tangan Nitril (S/M/L)', price: 95000, qty: 50, link: 'https://www.tokopedia.com/search?st=product&q=sarung+tangan+nitril',           replaced: null },
        { type: 'bhp',        name: 'Masker Bedah 3-ply',         price: 45000,  qty: 30, link: 'https://www.tokopedia.com/search?st=product&q=masker+bedah',                    replaced: null },
    ];
    for (const it of items1) {
        await db.query(
            `INSERT INTO procurement_items (draft_id, item_type, name, price, quantity, purchase_link, replaced_asset_id)
             VALUES (?, ?, ?, ?, ?, ?, ?)`,
            [draftId1, it.type, it.name, it.price, it.qty, it.link, it.replaced]
        );
    }
    console.log(`  ✓ Draft 1 "Pengadaan Tahunan Lab Biologi 2025" (submitted) — ${items1.length} items`);

    // Draf 2: Draft 2026
    const [dr2] = await db.query(
        `INSERT INTO procurement_drafts (title, year, status, created_by, created_at)
         VALUES (?, ?, ?, ?, ?)`,
        ['Pengadaan Tahunan Lab Biologi 2026', 2026, 'draft', kalabId, '2026-02-12 09:30:00']
    );
    const draftId2 = dr2.insertId;
    const items2 = [
        { type: 'inventaris', name: 'Mesin PCR Bio-Rad T100',          price: 78500000, qty: 1, link: 'https://www.tokopedia.com/search?st=product&q=mesin+pcr+bio-rad',          replaced: assetMap['BIO/PCR/24/001'] },
        { type: 'inventaris', name: 'Spektrofotometer UV-Vis Double Beam', price: 85000000, qty: 1, link: 'https://www.tokopedia.com/search?st=product&q=spektrofotometer+uv-vis', replaced: assetMap['KIM/SPK/22/001'] },
        { type: 'inventaris', name: 'Lemari Asam FRP 1200mm',          price: 65000000, qty: 1, link: 'https://www.tokopedia.com/search?st=product&q=lemari+asam+frp',             replaced: assetMap['KIM/LAS/20/001'] },
        { type: 'bhp',        name: 'Cover Glass 22x22mm',             price: 28000,    qty: 20, link: 'https://www.tokopedia.com/search?st=product&q=cover+glass+laboratorium',   replaced: null },
        { type: 'bhp',        name: 'Filter Membran 0.22µm',           price: 185000,   qty: 10, link: 'https://www.tokopedia.com/search?st=product&q=filter+membran+0.22',         replaced: null },
        { type: 'bhp',        name: 'Media Agar NA (Nutrient Agar)',   price: 285000,   qty:  5, link: 'https://www.tokopedia.com/search?st=product&q=nutrient+agar',               replaced: null },
    ];
    for (const it of items2) {
        await db.query(
            `INSERT INTO procurement_items (draft_id, item_type, name, price, quantity, purchase_link, replaced_asset_id)
             VALUES (?, ?, ?, ?, ?, ?, ?)`,
            [draftId2, it.type, it.name, it.price, it.qty, it.link, it.replaced]
        );
    }
    console.log(`  ✓ Draft 2 "Pengadaan Tahunan Lab Biologi 2026" (draft) — ${items2.length} items`);

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
