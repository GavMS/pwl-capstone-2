const express = require('express');
const router = express.Router();
const assetController = require('../controllers/assetController');
const authMiddleware = require('../middleware/authMiddleware');

// Rute spesifik sebelum /:id
router.get('/procurement', authMiddleware, assetController.getProcurementAssets);

router.get('/', authMiddleware, assetController.getAllAssets);

// Bulk update received_date semua unit dari satu procurement item
router.patch('/by-source/:sourceId/received', authMiddleware, assetController.setReceivedBySource);

// Update label / QR / tanggal terima per-aset (Staf Admin)
router.patch('/:id', authMiddleware, assetController.updateAsset);

module.exports = router;
