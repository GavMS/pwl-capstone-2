<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;

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
