const express = require('express');
const router = express.Router();
const consumableController = require('../controllers/consumableController');
const authMiddleware = require('../middleware/authMiddleware');

router.get('/', authMiddleware, consumableController.getAllConsumables);

module.exports = router;
