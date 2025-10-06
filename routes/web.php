<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\Admin\AttendanceController as AdminAttendanceController;
use App\Http\Controllers\Admin\ProviderController;
use App\Http\Controllers\Admin\ReceiptController;
use App\Http\Controllers\Admin\SupplyController;
use App\Http\Controllers\Admin\ReportsController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\OvertimeRequestController;
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
    // DENTRO de Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/overtime/create', [OvertimeRequestController::class, 'create'])->name('overtime.create');
    Route::post('/overtime', [OvertimeRequestController::class, 'store'])->name('overtime.store');
});

// --- Rutas del Panel de Administración ---
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard de Asistencia
    Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');

    // --- RUTA PARA LOS DATOS DEL CALENDARIO (API) ---
    Route::get('/calendar-data/{user}/{year}/{month}', [AdminController::class, 'getCalendarData'])->name('calendar.data');

    // Reportes
    Route::get('/reports', [ReportsController::class, 'index'])->name('reports');
    Route::get('/reports/export', [ReportsController::class, 'exportPdf'])->name('reports.export');

    // Boletas de Venta
    Route::get('/receipts/create', [ReceiptController::class, 'create'])->name('receipts.create');
    Route::post('/receipts', [ReceiptController::class, 'store'])->name('receipts.store');
    Route::get('/receipts/{receipt}', [ReceiptController::class, 'show'])->name('receipts.show');
    Route::get('/receipts-history', [ReceiptController::class, 'history'])->name('receipts.history');

    // Proveedores e Insumos
    Route::get('/supplies/search', [SupplyController::class, 'search'])->name('supplies.search');
    Route::resource('providers', ProviderController::class);
    Route::post('/providers/{provider}/supplies', [SupplyController::class, 'store'])->name('providers.supplies.store');
    Route::delete('/providers/{provider}/supplies/{supply}', [SupplyController::class, 'destroy'])->name('providers.supplies.destroy');

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

    // -- RUTAS PARA MARCACIÓN MÚLTIPLE

    Route::get('/attendance/multiple', [AdminAttendanceController::class, 'createMultiple'])->name('attendance.createMultiple');
    // Marcación múltiple
    Route::post('/attendance/multiple', [AdminAttendanceController::class, 'storeMultiple'])->name('attendance.storeMultiple');


    // --- RUTAS REGISTRO MANUAL ---
    Route::get('/attendance/create-single', [AdminAttendanceController::class, 'createSingle'])->name('attendance.createSingle');
    Route::post('/attendance', [AdminAttendanceController::class, 'storeSingle'])->name('attendance.storeSingle');

    Route::get('/overtime/create', [AdminController::class, 'createOvertime'])->name('overtime.create');
    Route::post('/overtime', [AdminController::class, 'storeOvertime'])->name('overtime.store');
    Route::get('/overtime-requests', [AdminController::class, 'overtimeRequests'])->name('overtime.requests');
    Route::put('/overtime-requests/{overtime}', [AdminController::class, 'updateOvertimeRequest'])->name('overtime.update');
    Route::delete('/overtime/{overtime}', [AdminController::class, 'destroyOvertime'])->name('overtime.destroy');
});


require __DIR__ . '/auth.php';
