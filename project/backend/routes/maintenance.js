const express = require('express');
const router  = express.Router();
const ctrl    = require('../controllers/maintenanceController');
const auth    = require('../middleware/authMiddleware');

// GET  /api/maintenance                       — semua log maintenance (semua aset)
router.get('/', auth, ctrl.getAllLogs);

// GET  /api/maintenance/asset/:assetId        — log maintenance per aset
router.get('/asset/:assetId', auth, ctrl.getLogsByAsset);

// POST /api/maintenance/asset/:assetId        — tambah log maintenance baru
router.post('/asset/:assetId', auth, ctrl.createLog);

// DELETE /api/maintenance/:logId              — hapus satu log
router.delete('/:logId', auth, ctrl.deleteLog);

module.exports = router;
