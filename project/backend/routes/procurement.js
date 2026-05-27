const express = require('express');
const router  = express.Router();
const procurementController = require('../controllers/procurementController');
const authMiddleware = require('../middleware/authMiddleware');

// Proteksi semua rute procurement dengan JWT authMiddleware
router.use(authMiddleware);

// Rute khusus untuk dropdown daftar aset (untuk pencarian barang pengganti)
router.get('/assets/list', procurementController.getAssetsList);

// Rute CRUD Utama Draf Pengadaan
router.get('/',            procurementController.getAllDrafts);
router.get('/:id',        procurementController.getDraftById);
router.post('/',          procurementController.createDraft);
router.put('/:id',        procurementController.updateDraft);
router.delete('/:id',     procurementController.deleteDraft);

module.exports = router;
