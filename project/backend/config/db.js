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
                await connection.query('INSERT INTO users (name, email, password, role_id) VALUES (?, ?, ?, ?)', [
                    user.name, user.email, hashedPwd, user.role_id
                ]);
                console.log(`Dummy user created: ${user.email} / password123`);
            }
        }

        connection.release();
    } catch (error) {
        console.error('Error connecting to MySQL:', error);
    }
};

testConnection();

module.exports = pool;
