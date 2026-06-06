const express = require('express');
const router  = express.Router();
const ctrl    = require('../controllers/consumableController');
const auth    = require('../middleware/authMiddleware');

// GET    /api/consumables/meta      — metadata untuk dropdown form BHP
router.get('/meta',     auth, ctrl.getMeta);

// GET    /api/consumables           — daftar semua BHP
router.get('/',         auth, ctrl.getAllConsumables);

// POST   /api/consumables           — tambah BHP baru
router.post('/',        auth, ctrl.createConsumable);

// PUT    /api/consumables/:id       — update data BHP
router.put('/:id',      auth, ctrl.updateConsumable);

// DELETE /api/consumables/:id       — hapus BHP
router.delete('/:id',   auth, ctrl.deleteConsumable);

// PATCH  /api/consumables/:id/adjust-stock — tambah/kurangi stok manual
router.patch('/:id/adjust-stock', auth, ctrl.adjustStock);

module.exports = router;
