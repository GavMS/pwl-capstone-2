# AsetLab — Cara Setup & Menjalankan Project

Stack: **Laravel** (Frontend) + **Node.js** (Backend) + **MySQL**

---

## Yang Dibutuhkan Sebelum Mulai

- PHP >= 8.2
- Composer
- Node.js >= 18
- MySQL (XAMPP atau MySQL Installer)

---

## Langkah Setup

### 1. Backend (Node.js)

```bash
cd project/backend
npm install
cp .env.example .env
```

Buka file `project/backend/.env`, sesuaikan dengan MySQL kamu:

```
DB_USER=root
DB_PASSWORD=    ← isi password MySQL kamu, kosongkan jika tidak ada
DB_PORT=3306    ← ganti 3307 kalau pakai XAMPP
```

> Database dan tabel akan dibuat otomatis, tidak perlu buat manual.

---

### 2. Frontend (Laravel)

```bash
cd project/frontend
composer install
cp .env.example .env
php artisan key:generate
```

---

## Cara Menjalankan

Buka **2 terminal**:

**Terminal 1:**
```bash
cd project/backend
npm start
```

**Terminal 2:**
```bash
cd project/frontend
php artisan serve
```

Buka browser: `http://127.0.0.1:8000`

---

## Akun Login (password semua: `password123`)

| Email | Role |
|---|---|
| `admin@example.com` | Administrator |
| `kalab@example.com` | Kepala Laboratorium |
| `kaprodi@example.com` | Ketua Program Studi |
| `stafadmin@example.com` | Staf Administrasi |
| `staflab@example.com` | Staf Laboratorium |

---

## Kalau Ada Error

**`Access denied for user 'root'`** → Cek `DB_PASSWORD` di `project/backend/.env`

**`Unknown database 'capstone'`** → Pastikan MySQL sedang berjalan di XAMPP/MySQL

**Login gagal** → Pastikan kedua server (backend & frontend) sama-sama jalan
