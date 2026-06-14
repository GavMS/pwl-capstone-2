# Progress Perbaikan

Terakhir diperbarui: 2026-06-14

> Semua item fitur di batch ini **sudah selesai**. Sisa hanya 1 item security yang **sengaja ditunda** (menunggu migrasi Sequelize).

---

## ✅ Selesai (batch ini)

| #  | Item | Area |
|----|------|------|
| 1  | Kalab tentukan ruangan + lokasi & min stok BHP saat pengadaan | Kalab |
| 2  | Item BHP disetujui dimaterialisasi ke `consumables` | Backend/Pengadaan |
| 3  | KODE inventaris pakai `label_number` dari Staf Admin | Staf Lab |
| 4  | Kategori dihapus; Ruangan terisi dari pengadaan | Staf Lab |
| 5  | Group barang sejenis + dropdown pilih unit untuk log maintenance | Staf Lab |
| 6  | Filter berdasarkan status (Draf Disetujui & Labeling Inventaris) | Staf Admin |
| 7  | "Jumlah Item" hanya hitung item yang disetujui | Staf Admin/Backend |
| 8  | Kolom "Status Label" → button shortcut ke Labeling | Staf Admin |
| 9  | Labeli hanya bisa setelah tanggal diterima disimpan | Staf Admin |
| 10 | Dashboard admin: jumlah BHP & stok kritis (fix query) | Backend/Dashboard |
| —  | Cleanup: hapus halaman Kalab "Kelola BHP/Inventaris" yang redundan | Kalab |

### Perubahan kunci
- **Form pengadaan** (`kalab/procurement/form.blade.php`): tiap item kini punya **Ruangan** (dropdown); item **BHP** punya **Min Stok** + **Lokasi**.
- **Schema** (`backend/config/db.js`, additive & idempotent): `procurement_items` + `room_id, min_stock, location`; `consumables` + `source_item_id` (FK) untuk idempotensi materialisasi.
- **Finalisasi** (`procurementController.finalizeDraft`): inventaris → `assets.room_id`; BHP approved → baris `consumables` (stok = qty, min_stock, location, room_id) — idempotent via `source_item_id`.
- **Inventaris Staf Lab**: tabel digrup per jenis + dropdown unit; KODE = `label_number`; kolom Kategori dihapus.
- Halaman Kalab "Kelola BHP/Inventaris" + `InventoryController` + route `kalab.bhp.*`/`kalab.inventaris.*` dihapus.

### Catatan deploy
- **Restart `node server.js`** → migrasi schema baru jalan otomatis saat startup.
- Tidak perlu re-seed; perubahan additive, data lama aman.

---

## ⏳ Ditunda — Security (saat migrasi Sequelize)

- **API backend belum cek role.** `middleware/authMiddleware.js` hanya verifikasi JWT + `is_active`. Endpoint admin (mis. `DELETE /api/users/:id`, CRUD `/api/rooms`, `/api/consumables`) bisa dipanggil token role apa pun langsung ke `localhost:5000`, melewati proteksi role yang hanya ada di Laravel.
- **Rencana:** tambah middleware otorisasi role per endpoint saat refactor ke Sequelize.
