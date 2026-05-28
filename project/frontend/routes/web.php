<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\RoomManagementController;
use App\Http\Controllers\ProcurementController;
use App\Http\Controllers\InventoryController;

Route::get('/', function () {
    return redirect()->route('login');
});

// Auth routes
Route::get('/login',  [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout',[AuthController::class, 'logout'])->name('logout');

// Dashboard routes — masing-masing diproteksi oleh RoleMiddleware
Route::get('/dashboard/admin',     [DashboardController::class, 'admin'])->middleware('role:Administrator')->name('dashboard.admin');
Route::get('/dashboard/kalab',     [DashboardController::class, 'kalab'])->middleware('role:Kepala Laboratorium')->name('dashboard.kalab');
Route::get('/dashboard/kaprodi',   [DashboardController::class, 'kaprodi'])->middleware('role:Ketua Program Studi')->name('dashboard.kaprodi');
Route::get('/dashboard/stafadmin', [DashboardController::class, 'stafadmin'])->middleware('role:Staf Administrasi')->name('dashboard.stafadmin');
Route::get('/dashboard/staflab',   [DashboardController::class, 'staflab'])->middleware('role:Staf Laboratorium')->name('dashboard.staflab');

// ─────────────────────────────────────────────
// Admin: Manajemen User & Ruangan — hanya Administrator
// ─────────────────────────────────────────────
Route::prefix('admin')->middleware('role:Administrator')->group(function () {

    // Manajemen User
    Route::get('/users',                   [UserManagementController::class, 'index'])->name('admin.users.index');
    Route::get('/users/create',            [UserManagementController::class, 'create'])->name('admin.users.create');
    Route::post('/users',                  [UserManagementController::class, 'store'])->name('admin.users.store');
    Route::get('/users/{id}/edit',         [UserManagementController::class, 'edit'])->name('admin.users.edit');
    Route::put('/users/{id}',              [UserManagementController::class, 'update'])->name('admin.users.update');
    Route::delete('/users/{id}',           [UserManagementController::class, 'destroy'])->name('admin.users.destroy');
    Route::get('/users/{id}/check-delete', [UserManagementController::class, 'checkDelete'])->name('admin.users.check-delete');

    // Manajemen Ruangan
    Route::get('/rooms',                   [RoomManagementController::class, 'index'])->name('admin.rooms.index');
    Route::get('/rooms/create',            [RoomManagementController::class, 'create'])->name('admin.rooms.create');
    Route::post('/rooms',                  [RoomManagementController::class, 'store'])->name('admin.rooms.store');
    Route::get('/rooms/{id}/edit',         [RoomManagementController::class, 'edit'])->name('admin.rooms.edit');
    Route::put('/rooms/{id}',              [RoomManagementController::class, 'update'])->name('admin.rooms.update');
    Route::delete('/rooms/{id}',           [RoomManagementController::class, 'destroy'])->name('admin.rooms.destroy');
    Route::get('/rooms/{id}/check-delete', [RoomManagementController::class, 'checkDelete'])->name('admin.rooms.check-delete');
});

// ─────────────────────────────────────────────
// Kepala Laboratorium: Draf Pengadaan Barang
// ─────────────────────────────────────────────
Route::prefix('kalab')->middleware('role:Kepala Laboratorium')->group(function () {
    Route::get('/procurement',              [ProcurementController::class, 'index'])->name('kalab.procurement.index');
    Route::get('/procurement/create',       [ProcurementController::class, 'create'])->name('kalab.procurement.create');
    Route::post('/procurement',             [ProcurementController::class, 'store'])->name('kalab.procurement.store');
    Route::get('/procurement/{id}',         [ProcurementController::class, 'show'])->name('kalab.procurement.show');
    Route::get('/procurement/{id}/edit',    [ProcurementController::class, 'edit'])->name('kalab.procurement.edit');
    Route::put('/procurement/{id}',         [ProcurementController::class, 'update'])->name('kalab.procurement.update');
    Route::delete('/procurement/{id}',      [ProcurementController::class, 'destroy'])->name('kalab.procurement.destroy');
    
    // Inventaris & BHP
    Route::get('/inventaris',               [InventoryController::class, 'assets'])->name('kalab.inventaris.index');
    Route::get('/bhp',                      [InventoryController::class, 'consumables'])->name('kalab.bhp.index');
});

