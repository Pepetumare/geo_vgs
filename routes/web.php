<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\Admin\ReceiptController; // <-- Asegúrate de que apunte a la carpeta Admin
use App\Http\Controllers\Admin\ReportsController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// --- Rutas de Usuario Autenticado ---
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [AttendanceController::class, 'index'])->name('dashboard');
    Route::post('/attendance', [AttendanceController::class, 'store'])->name('attendance.store');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// --- Rutas del Panel de Administración ---
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');
    Route::get('/reports', [ReportsController::class, 'index'])->name('reports');
    
    // Rutas para Boletas de Venta
    Route::get('/receipts/create', [ReceiptController::class, 'create'])->name('receipts.create');
    Route::post('/receipts', [ReceiptController::class, 'store'])->name('receipts.store');
    Route::get('/receipts/{receipt}', [ReceiptController::class, 'show'])->name('receipts.show');
    
    // --- RUTA DEL HISTORIAL (VERIFICAR ESTA LÍNEA) ---
    Route::get('/receipts-history', [ReceiptController::class, 'history'])->name('receipts.history');
});


require __DIR__.'/auth.php';