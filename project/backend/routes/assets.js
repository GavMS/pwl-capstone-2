const express = require('express');
const router = express.Router();
const assetController = require('../controllers/assetController');
const authMiddleware = require('../middleware/authMiddleware');

router.get('/', authMiddleware, assetController.getAllAssets);

module.exports = router;
