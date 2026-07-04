<?php

use App\Http\Controllers\CitaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EntregaController;
use App\Http\Controllers\FormulaMedicaController;
use App\Http\Controllers\InventarioController;
use App\Http\Controllers\MedicamentoController;
use App\Http\Controllers\PacienteController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/dashboard', [DashboardController::class, 'index']);
Route::get('/pacientes', [PacienteController::class, 'index'])->name('pacientes.index');
Route::get('/formulas-medicas', [FormulaMedicaController::class, 'index'])->name('formulas.index');
Route::get('/medicamentos', [MedicamentoController::class, 'index'])->name('medicamentos.index');
Route::get('/inventario', [InventarioController::class, 'index'])->name('inventarios.index');
Route::get('/entregas', [EntregaController::class, 'index'])->name('entregas.index');
Route::get('/citas', [CitaController::class, 'index'])->name('citas.index');
