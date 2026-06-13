const mysql = require('mysql2/promise');
const path = require('path');
const fs = require('fs');

// Force read .env manually to bypass any dotenvx interference
const envPath = path.resolve(__dirname, '../.env');
const envContent = fs.readFileSync(envPath, 'utf8');
envContent.split('\n').forEach(line => {
    const [key, ...vals] = line.split('=');
    if (key && key.trim()) {
        process.env[key.trim()] = vals.join('=').trim();
    }
});

const pool = mysql.createPool({
    host: process.env.DB_HOST || 'localhost',
    user: process.env.DB_USER || 'root',
    password: process.env.DB_PASSWORD || '',
    database: process.env.DB_NAME || 'capstone',
    port: process.env.DB_PORT || 3306,
    waitForConnections: true,
    connectionLimit: 10,
    queueLimit: 0
});

// Test connection and auto-create users table for testing
const testConnection = async () => {
    try {
        // Create database if not exists using a temporary connection
        const tempConn = await mysql.createConnection({
            host: process.env.DB_HOST || 'localhost',
            user: process.env.DB_USER || 'root',
            password: process.env.DB_PASSWORD || '',
            port: process.env.DB_PORT || 3306,
        });
        await tempConn.query(`CREATE DATABASE IF NOT EXISTS \`${process.env.DB_NAME || 'capstone'}\``);
        await tempConn.end();

        const connection = await pool.getConnection();
        console.log('Connected to MySQL Database.');
        
        // Auto-create roles and users table exactly based on MWB schema
        await connection.query(`
            CREATE TABLE IF NOT EXISTS roles (
                id BIGINT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(255) NOT NULL
            )
        `);

        await connection.query(`
            CREATE TABLE IF NOT EXISTS users (
                id BIGINT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(255) NOT NULL,
                email VARCHAR(255) NOT NULL UNIQUE,
                password VARCHAR(255) NOT NULL,
                role_id BIGINT,
                room_id BIGINT NULL,
                roles_id BIGINT NULL,
                is_active TINYINT(1) NOT NULL DEFAULT 1,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE SET NULL
            )
        `);
        
        // Insert dummy roles if not exist
        const [rolesCheck] = await connection.query('SELECT * FROM roles');
        if (rolesCheck.length === 0) {
            const roleNames = ['Administrator', 'Kepala Laboratorium', 'Ketua Program Studi', 'Staf Administrasi', 'Staf Laboratorium'];
            for (const rName of roleNames) {
                await connection.query('INSERT INTO roles (name) VALUES (?)', [rName]);
            }
            console.log('Dummy roles inserted.');
        }

        // Ensure users.roles_id column exists (safe migration)
        const [rolesIdCols] = await connection.query(`
            SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS
            WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'roles_id'
        `);
        if (rolesIdCols.length === 0) {
            await connection.query(`ALTER TABLE users ADD COLUMN roles_id BIGINT NULL`);
            console.log('Added roles_id column to users table.');
        }

        // Ensure users.is_active column exists (safe migration)
        const [isActiveCols] = await connection.query(`
            SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS
            WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'is_active'
        `);
        if (isActiveCols.length === 0) {
            await connection.query(`ALTER TABLE users ADD COLUMN is_active TINYINT(1) NOT NULL DEFAULT 1`);
            console.log('Added is_active column to users table.');
        }

        // Insert dummy users for all roles
        const [usersCheck] = await connection.query('SELECT * FROM users');
        if (usersCheck.length === 0) {
            const bcrypt = require('bcrypt');
            const hashedPwd = await bcrypt.hash('password123', 10);
            
            // Get roles map
            const [rolesRows] = await connection.query('SELECT * FROM roles');
            const roleMap = {};
            rolesRows.forEach(r => roleMap[r.name] = r.id);

            const usersToInsert = [
                { email: 'admin@example.com', name: 'Admin', role_id: roleMap['Administrator'] },
                { email: 'kalab@example.com', name: 'Kepala Lab', role_id: roleMap['Kepala Laboratorium'] },
                { email: 'kaprodi@example.com', name: 'Kaprodi', role_id: roleMap['Ketua Program Studi'] },
                { email: 'stafadmin@example.com', name: 'Staf Administrasi', role_id: roleMap['Staf Administrasi'] },
                { email: 'staflab@example.com', name: 'Staf Laboratorium', role_id: roleMap['Staf Laboratorium'] }
            ];

            for (const user of usersToInsert) {
                await connection.query('INSERT INTO users (name, email, password, role_id, roles_id) VALUES (?, ?, ?, ?, ?)', [
                    user.name, user.email, hashedPwd, user.role_id, user.role_id
                ]);
                console.log(`Dummy user created: ${user.email} / password123`);
            }
        }

        // Auto-create rooms table
        await connection.query(`
            CREATE TABLE IF NOT EXISTS rooms (
                id BIGINT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(255) NOT NULL UNIQUE,
                code VARCHAR(255) NOT NULL UNIQUE,
                description TEXT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            )
        `);

        // Safe migration: tambah UNIQUE pada rooms.name jika belum ada
        const [roomNameIdx] = await connection.query(`
            SELECT INDEX_NAME FROM INFORMATION_SCHEMA.STATISTICS
            WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'rooms'
            AND COLUMN_NAME = 'name' AND NON_UNIQUE = 0
        `);
        if (roomNameIdx.length === 0) {
            await connection.query(`ALTER TABLE rooms ADD UNIQUE INDEX idx_rooms_name (name)`);
            console.log('Added UNIQUE constraint on rooms.name.');
        }

        // Insert dummy rooms if not exist
        const [roomsCheck] = await connection.query('SELECT * FROM rooms');
        if (roomsCheck.length === 0) {
            const roomsToInsert = [
                { name: 'Laboratorium Komputer 1', code: 'LAB-KOM-01', description: 'Laboratorium untuk praktikum rekayasa perangkat lunak dan pemrograman dasar.' },
                { name: 'Laboratorium Komputer 2', code: 'LAB-KOM-02', description: 'Laboratorium untuk praktikum jaringan dan keamanan komputer.' },
                { name: 'Laboratorium Multimedia', code: 'LAB-MM-01', description: 'Laboratorium khusus desain grafis, editing video, dan multimedia.' },
                { name: 'Gudang Penyimpanan Alat', code: 'GDG-ALAT', description: 'Ruangan penyimpanan untuk aset tidak aktif, hardware, dan bahan habis pakai (BHP).' }
            ];

            for (const room of roomsToInsert) {
                await connection.query('INSERT INTO rooms (name, code, description) VALUES (?, ?, ?)', [
                    room.name, room.code, room.description
                ]);
                console.log(`Dummy room created: ${room.name} (${room.code})`);
            }
        }

        // Auto-create assets table
        await connection.query(`
            CREATE TABLE IF NOT EXISTS assets (
                id BIGINT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(255) NOT NULL,
                code VARCHAR(255) NULL UNIQUE,
                category VARCHAR(100) NULL,
                room_id BIGINT NULL,
                condition_status VARCHAR(100) NULL DEFAULT 'Baik',
                year INT NULL,
                price DECIMAL(15,2) NULL DEFAULT 0,
                description TEXT NULL,
                status VARCHAR(100) NULL DEFAULT 'Baik',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE SET NULL
            )
        `);

        // Auto-create consumables (BHP) table
        await connection.query(`
            CREATE TABLE IF NOT EXISTS consumables (
                id BIGINT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(255) NOT NULL,
                code VARCHAR(255) NULL UNIQUE,
                category VARCHAR(100) NULL,
                unit VARCHAR(100) NULL,
                stock INT NULL DEFAULT 0,
                min_stock INT NULL DEFAULT 0,
                price DECIMAL(15,2) NULL DEFAULT 0,
                location VARCHAR(100) NULL,
                room_id BIGINT NULL,
                description TEXT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE SET NULL
            )
        `);

        // Auto-create procurement_drafts table
        await connection.query(`
            CREATE TABLE IF NOT EXISTS procurement_drafts (
                id BIGINT AUTO_INCREMENT PRIMARY KEY,
                title VARCHAR(255) NOT NULL,
                year INT NOT NULL,
                status VARCHAR(100) NOT NULL DEFAULT 'draft',
                created_by BIGINT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
            )
        `);

        // Auto-create procurement_items table
        await connection.query(`
            CREATE TABLE IF NOT EXISTS procurement_items (
                id BIGINT AUTO_INCREMENT PRIMARY KEY,
                draft_id BIGINT NOT NULL,
                item_type VARCHAR(100) NOT NULL,
                name VARCHAR(255) NOT NULL,
                price DECIMAL(15,2) NOT NULL,
                quantity INT NOT NULL,
                purchase_link TEXT NULL,
                replaced_asset_id BIGINT NULL,
                notes TEXT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (draft_id) REFERENCES procurement_drafts(id) ON DELETE CASCADE,
                FOREIGN KEY (replaced_asset_id) REFERENCES assets(id) ON DELETE SET NULL
            )
        `);

        // Ensure procurement_items.notes column exists (safe migration)
        const [notesCols] = await connection.query(`
            SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS
            WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'procurement_items' AND COLUMN_NAME = 'notes'
        `);
        if (notesCols.length === 0) {
            await connection.query(`ALTER TABLE procurement_items ADD COLUMN notes TEXT NULL`);
            console.log('Added notes column to procurement_items table.');
        }

        // Ensure procurement_items.review_status column exists (safe migration)
        const [reviewCols] = await connection.query(`
            SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS
            WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'procurement_items' AND COLUMN_NAME = 'review_status'
        `);
        if (reviewCols.length === 0) {
            await connection.query(`ALTER TABLE procurement_items ADD COLUMN review_status VARCHAR(50) NOT NULL DEFAULT 'pending'`);
            console.log('Added review_status column to procurement_items table.');
        }

        // ── Safe migration: kolom inventaris baru di assets (label / QR / penerimaan / asal pengadaan) ──
        // assets.label_number — nomor label fisik (UNIQUE; NULL ganda diizinkan MySQL)
        const [labelCols] = await connection.query(`
            SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS
            WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'assets' AND COLUMN_NAME = 'label_number'
        `);
        if (labelCols.length === 0) {
            await connection.query(`ALTER TABLE assets ADD COLUMN label_number VARCHAR(255) NULL UNIQUE`);
            console.log('Added label_number column to assets table.');
        }

        // assets.qr_path — path file PNG QR di storage Laravel
        const [qrCols] = await connection.query(`
            SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS
            WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'assets' AND COLUMN_NAME = 'qr_path'
        `);
        if (qrCols.length === 0) {
            await connection.query(`ALTER TABLE assets ADD COLUMN qr_path VARCHAR(500) NULL`);
            console.log('Added qr_path column to assets table.');
        }

        // assets.univ_qr_path — path file PNG QR universitas
        const [univQrCols] = await connection.query(`
            SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS
            WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'assets' AND COLUMN_NAME = 'univ_qr_path'
        `);
        if (univQrCols.length === 0) {
            await connection.query(`ALTER TABLE assets ADD COLUMN univ_qr_path VARCHAR(500) NULL`);
            console.log('Added univ_qr_path column to assets table.');
        }

        // assets.received_date — tanggal penerimaan (NULL = belum diterima / menunggu penerimaan)
        const [recvCols] = await connection.query(`
            SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS
            WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'assets' AND COLUMN_NAME = 'received_date'
        `);
        if (recvCols.length === 0) {
            await connection.query(`ALTER TABLE assets ADD COLUMN received_date DATE NULL`);
            console.log('Added received_date column to assets table.');
        }

        // assets.source_item_id — jejak asal pengadaan + cegah materialisasi ganda (FK ke procurement_items)
        const [srcCols] = await connection.query(`
            SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS
            WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'assets' AND COLUMN_NAME = 'source_item_id'
        `);
        if (srcCols.length === 0) {
            await connection.query(`ALTER TABLE assets ADD COLUMN source_item_id BIGINT NULL`);
            await connection.query(`ALTER TABLE assets ADD CONSTRAINT fk_assets_source_item FOREIGN KEY (source_item_id) REFERENCES procurement_items(id) ON DELETE SET NULL`);
            console.log('Added source_item_id column + FK to assets table.');
        }

        // Ensure users.room_id column exists (safe migration)
        const [uCols] = await connection.query(`
            SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS
            WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'room_id'
        `);
        if (uCols.length === 0) {
            await connection.query(`ALTER TABLE users ADD COLUMN room_id BIGINT NULL`);
            console.log('Added room_id column to users table.');
        }

        // Auto-create maintenance_logs table
        await connection.query(`
            CREATE TABLE IF NOT EXISTS maintenance_logs (
                id                     BIGINT AUTO_INCREMENT PRIMARY KEY,
                asset_id               BIGINT NOT NULL,
                performed_by           BIGINT NULL,
                maintenance_date       DATE NOT NULL,
                description            TEXT NOT NULL,
                condition_before       VARCHAR(100) NULL,
                condition_after        VARCHAR(100) NOT NULL DEFAULT 'Baik',
                cost                   DECIMAL(15,2) NULL DEFAULT 0,
                notes                  TEXT NULL,
                created_at             TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at             TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (asset_id)     REFERENCES assets(id) ON DELETE CASCADE,
                FOREIGN KEY (performed_by) REFERENCES users(id) ON DELETE SET NULL
            )
        `);

        // Auto-create maintenance_consumables table (BHP digunakan per log)
        await connection.query(`
            CREATE TABLE IF NOT EXISTS maintenance_consumables (
                id             BIGINT AUTO_INCREMENT PRIMARY KEY,
                log_id         BIGINT NOT NULL,
                consumable_id  BIGINT NOT NULL,
                quantity_used  INT NOT NULL DEFAULT 1,
                created_at     TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (log_id)        REFERENCES maintenance_logs(id) ON DELETE CASCADE,
                FOREIGN KEY (consumable_id) REFERENCES consumables(id) ON DELETE CASCADE
            )
        `);

        console.log('maintenance_logs & maintenance_consumables tables ensured.');

        connection.release();
    } catch (error) {
        console.error('Error connecting to MySQL:', error);
    }
};

testConnection();

module.exports = pool;
