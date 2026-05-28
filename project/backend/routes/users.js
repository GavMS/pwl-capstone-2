const express = require('express');
const router = express.Router();
const userController = require('../controllers/userController');
const authMiddleware = require('../middleware/authMiddleware');

// Semua route diproteksi dengan JWT
router.use(authMiddleware);

// GET /api/users          — Daftar semua user
router.get('/', userController.getAll);

// GET /api/users/roles          — Daftar semua role (untuk dropdown form)
router.get('/roles', userController.getRoles);

// GET /api/users/dashboard-stats — Statistik dashboard admin
router.get('/dashboard-stats', userController.getDashboardStats);

// GET /api/users/:id      — Detail 1 user (Smart Edit)
router.get('/:id', userController.getOne);

// GET /api/users/:id/check-delete — Cek keamanan sebelum hapus (Smart Delete)
router.get('/:id/check-delete', userController.checkDelete);

// POST /api/users         — Tambah user baru
router.post('/', userController.create);

// PUT /api/users/:id      — Update user
router.put('/:id', userController.update);

// DELETE /api/users/:id   — Hapus user
router.delete('/:id', userController.destroy);

module.exports = router;
