const db = require('./config/db');

async function test() {
    console.log('Starting insert test...');
    const connection = await db.getConnection();
    await connection.beginTransaction();
    try {
        // Cek user ID pertama yang ada di database
        const [users] = await connection.query('SELECT id FROM users LIMIT 1');
        const userId = users.length > 0 ? users[0].id : null;
        console.log('Using User ID:', userId);

        // 1. Uji Insert Header Draf
        const [draftResult] = await connection.query(
            'INSERT INTO procurement_drafts (title, year, status, created_by) VALUES (?, ?, ?, ?)',
            ['Test Draft Anggaran 2026', 2026, 'draft', userId]
        );
        const draftId = draftResult.insertId;
        console.log('1. Draft Header inserted successfully. ID:', draftId);

        // 2. Uji Insert Item Detail
        await connection.query(
            `INSERT INTO procurement_items 
             (draft_id, item_type, name, price, quantity, purchase_link, replaced_asset_id, notes) 
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)`,
            [draftId, 'inventaris', 'Smart TV 50 inch', 8500000.00, 1, 'https://tokopedia.com', null, 'Untuk ruang Kalab']
        );
        console.log('2. Draft Item inserted successfully.');

        await connection.commit();
        console.log('SUCCESS: Transaction committed successfully!');
    } catch (err) {
        await connection.rollback();
        console.error('FAIL: Transaction failed with error:', err);
    } finally {
        connection.release();
        process.exit(0);
    }
}
test();
