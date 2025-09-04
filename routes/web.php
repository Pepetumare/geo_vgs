<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\Admin\AttendanceController as AdminAttendanceController;
use App\Http\Controllers\Admin\ReceiptController;
use App\Http\Controllers\Admin\ReportsController;
use App\Http\Controllers\Admin\UserController;
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
    // Dashboard de Asistencia
    Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');
    
    // --- RUTA PARA LOS DATOS DEL CALENDARIO (API) ---
    Route::get('/calendar-data/{user}/{year}/{month}', [AdminController::class, 'getCalendarData'])->name('calendar.data');
    
    // Reportes
    Route::get('/reports', [ReportsController::class, 'index'])->name('reports');
    
    // Boletas de Venta
    Route::get('/receipts/create', [ReceiptController::class, 'create'])->name('receipts.create');
    Route::post('/receipts', [ReceiptController::class, 'store'])->name('receipts.store');
    Route::get('/receipts/{receipt}', [ReceiptController::class, 'show'])->name('receipts.show');
    Route::get('/receipts-history', [ReceiptController::class, 'history'])->name('receipts.history');

    // Gestión de Usuarios
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

    
    // Rutas para editar turnos completos (entrada y salida)
    Route::get('/attendance/{entry}/{exit}/edit', [AdminAttendanceController::class, 'edit'])->name('attendance.edit');
    Route::put('/attendance/{entry}/{exit}', [AdminAttendanceController::class, 'update'])->name('attendance.update');

    // --- NUEVAS RUTAS PARA EDITAR UN REGISTRO INDIVIDUAL ---
    Route::get('/attendance/{attendance}/edit-single', [AdminAttendanceController::class, 'editSingle'])->name('attendance.editSingle');
    Route::put('/attendance/{attendance}', [AdminAttendanceController::class, 'updateSingle'])->name('attendance.updateSingle');
    Route::delete('/attendance/{attendance}', [AdminAttendanceController::class, 'destroySingle'])->name('attendance.destroySingle');

    // --- RUTAS REGISTRO MANUAL ---
    Route::get('/attendance/create-single', [AdminAttendanceController::class, 'createSingle'])->name('attendance.createSingle');
    Route::post('/attendance', [AdminAttendanceController::class, 'storeSingle'])->name('attendance.storeSingle');
});


require __DIR__.'/auth.php';