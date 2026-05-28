const mysql = require('mysql2/promise');
const path = require('path');
const fs = require('fs');

// Baca .env manual
const envContent = fs.readFileSync(path.resolve(__dirname, '.env'), 'utf8');
envContent.split('\n').forEach(line => {
    const [key, ...vals] = line.split('=');
    if (key && key.trim()) process.env[key.trim()] = vals.join('=').trim();
});

const DB_NAME = process.env.DB_NAME || 'capstone';

(async () => {
    const conn = await mysql.createConnection({
        host: process.env.DB_HOST || 'localhost',
        user: process.env.DB_USER || 'root',
        password: process.env.DB_PASSWORD || '',
        port: process.env.DB_PORT || 3306,
    });

    console.log(`Dropping database "${DB_NAME}"...`);
    await conn.query(`DROP DATABASE IF EXISTS \`${DB_NAME}\``);
    console.log('Database dropped.');
    await conn.end();

    console.log('Restarting backend to recreate tables and seed data...\n');
    require('./config/db');

    // Tunggu sebentar lalu exit
    setTimeout(() => {
        console.log('\nDatabase reset selesai! Jalankan "npm start" untuk memulai server.');
        process.exit(0);
    }, 3000);
})();
