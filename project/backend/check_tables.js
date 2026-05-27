const mysql = require('mysql2/promise');
const path = require('path');
const fs = require('fs');

const envPath = path.resolve(__dirname, './.env');
const envContent = fs.readFileSync(envPath, 'utf8');
envContent.split('\n').forEach(line => {
    const [key, ...vals] = line.split('=');
    if (key && key.trim()) {
        process.env[key.trim()] = vals.join('=').trim();
    }
});

async function main() {
    try {
        const connection = await mysql.createConnection({
            host: process.env.DB_HOST || 'localhost',
            user: process.env.DB_USER || 'root',
            password: process.env.DB_PASSWORD || '',
            database: process.env.DB_NAME || 'capstone',
            port: process.env.DB_PORT || 3306,
        });
        const [rows] = await connection.query('SHOW TABLES');
        console.log('--- TABLES IN DATABASE ---');
        console.log(rows.map(r => Object.values(r)[0]));
        await connection.end();
    } catch (err) {
        console.error('Database connection error:', err.message);
    }
}
main();
