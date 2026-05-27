const express = require('express');
const router  = express.Router();
const roomController = require('../controllers/roomController');

// List semua ruangan
router.get('/',                   roomController.getAll);

// Rute statis dulu sebelum :id
router.get('/:id/check-delete',   roomController.checkDelete);
router.get('/:id/check-edit',     roomController.checkEdit);

// Rute dengan parameter
router.get('/:id',                roomController.getOne);
router.post('/',                  roomController.create);
router.put('/:id',                roomController.update);
router.delete('/:id',             roomController.destroy);

module.exports = router;
