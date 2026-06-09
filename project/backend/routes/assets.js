const express = require('express');
const router = express.Router();
const assetController = require('../controllers/assetController');
const authMiddleware = require('../middleware/authMiddleware');

// Rute spesifik sebelum /:id
router.get('/procurement', authMiddleware, assetController.getProcurementAssets);

router.get('/', authMiddleware, assetController.getAllAssets);

// Update label / QR / tanggal terima (Staf Admin)
router.patch('/:id', authMiddleware, assetController.updateAsset);

module.exports = router;
