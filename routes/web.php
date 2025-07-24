<?php

use App\Http\Controllers\Admin\AttendanceController as AdminAttendanceController;
use App\Http\Controllers\Admin\ReportsController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// --- Rutas de Usuario Autenticado ---
Route::middleware(['auth', 'verified'])->group(function () {
    // Ruta principal del dashboard del empleado
    Route::get('/dashboard', [AttendanceController::class, 'index'])->name('dashboard');
    
    // Ruta para guardar un registro de asistencia
    Route::post('/attendance', [AttendanceController::class, 'store'])->name('attendance.store');

    // Rutas del perfil de usuario
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// --- Rutas del Panel de Administración ---
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');
    Route::get('/reports', [ReportsController::class, 'index'])->name('reports');
    Route::get('/attendance/{entry}/{exit}/edit', [AdminAttendanceController::class, 'edit'])->name('attendance.edit');
    Route::put('/attendance/{entry}/{exit}', [AdminAttendanceController::class, 'update'])->name('attendance.update');

});


require __DIR__.'/auth.php';
