const express = require('express');
const router  = express.Router();
const procurementController = require('../controllers/procurementController');
const authMiddleware = require('../middleware/authMiddleware');

// Proteksi semua rute procurement dengan JWT authMiddleware
router.use(authMiddleware);

// Rute spesifik harus SEBELUM /:id
router.get('/stats',      procurementController.getStats);
router.get('/assets/list', procurementController.getAssetsList);

// Rute CRUD Utama Draf Pengadaan
router.get('/',              procurementController.getAllDrafts);
router.get('/:id',          procurementController.getDraftById);
router.post('/',            procurementController.createDraft);
router.put('/:id',          procurementController.updateDraft);
router.patch('/:id/status',               procurementController.updateStatus);
router.patch('/:id/items/:itemId/review', procurementController.updateItemStatus);
router.post('/:id/finalize',              procurementController.finalizeDraft);
router.delete('/:id',                     procurementController.deleteDraft);

module.exports = router;
