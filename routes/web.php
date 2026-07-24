<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\OtpVerificationController;
use App\Http\Controllers\Auth\RegisteredUserController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
	return Auth::check()
		? redirect()->route('dashboard')
		: redirect()->route('login');
});

Route::middleware('guest')->group(function () {
	Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
	Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.store');
	Route::get('/otp-verify', [OtpVerificationController::class, 'show'])->name('otp.verify');
	Route::post('/otp-verify', [OtpVerificationController::class, 'verify'])->name('otp.verify.post');
	Route::post('/otp-resend', [OtpVerificationController::class, 'resend'])->name('otp.resend');
	Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
	Route::post('/register', [RegisteredUserController::class, 'store'])->name('register.store');
});

Route::middleware('auth')->group(function () {
	Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
	Route::get('/perfil', [ProfileController::class, 'edit'])->name('profile.edit');
	Route::put('/perfil', [ProfileController::class, 'update'])->name('profile.update');

	Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

	Route::middleware('rol:paciente,administrativo')->group(function () {
		Route::get('/formulas-medicas', 'App\\Http\\Controllers\\FormulaMedicaController@index')->name('formulas.index');
		Route::get('/formulas-medicas/crear', 'App\\Http\\Controllers\\FormulaMedicaController@create')->name('formulas.create');
		Route::post('/formulas-medicas', 'App\\Http\\Controllers\\FormulaMedicaController@store')->name('formulas.store');
		Route::get('/formulas-medicas/{formula}/editar', 'App\\Http\\Controllers\\FormulaMedicaController@edit')->name('formulas.edit');
		Route::put('/formulas-medicas/{formula}', 'App\\Http\\Controllers\\FormulaMedicaController@update')->name('formulas.update');
		Route::delete('/formulas-medicas/{formula}', 'App\\Http\\Controllers\\FormulaMedicaController@destroy')->name('formulas.destroy');

		Route::get('/citas', 'App\\Http\\Controllers\\CitaController@index')->name('citas.index');
		Route::get('/citas/exportar-pdf', 'App\\Http\\Controllers\\CitaController@exportPdf')->name('citas.export-pdf');
		Route::get('/citas/crear', 'App\\Http\\Controllers\\CitaController@create')->name('citas.create');
		Route::post('/citas', 'App\\Http\\Controllers\\CitaController@store')->name('citas.store');
		Route::get('/citas/{cita}/editar', 'App\\Http\\Controllers\\CitaController@edit')->name('citas.edit');
		Route::put('/citas/{cita}', 'App\\Http\\Controllers\\CitaController@update')->name('citas.update');
		Route::delete('/citas/{cita}', 'App\\Http\\Controllers\\CitaController@destroy')->name('citas.destroy');
	});

	Route::middleware('rol:despachador,administrativo')->group(function () {
		Route::get('/medicamentos', 'App\\Http\\Controllers\\MedicamentoController@index')->name('medicamentos.index');
		Route::get('/medicamentos/crear', 'App\\Http\\Controllers\\MedicamentoController@create')->name('medicamentos.create');
		Route::post('/medicamentos', 'App\\Http\\Controllers\\MedicamentoController@store')->name('medicamentos.store');
		Route::get('/medicamentos/{medicamento}/editar', 'App\\Http\\Controllers\\MedicamentoController@edit')->name('medicamentos.edit');
		Route::put('/medicamentos/{medicamento}', 'App\\Http\\Controllers\\MedicamentoController@update')->name('medicamentos.update');
		Route::delete('/medicamentos/{medicamento}', 'App\\Http\\Controllers\\MedicamentoController@destroy')->name('medicamentos.destroy');

		Route::get('/inventario', 'App\\Http\\Controllers\\InventarioController@index')->name('inventarios.index');
		Route::get('/inventario/crear', 'App\\Http\\Controllers\\InventarioController@create')->name('inventarios.create');
		Route::post('/inventario', 'App\\Http\\Controllers\\InventarioController@store')->name('inventarios.store');
		Route::get('/inventario/{inventario}/editar', 'App\\Http\\Controllers\\InventarioController@edit')->name('inventarios.edit');
		Route::put('/inventario/{inventario}', 'App\\Http\\Controllers\\InventarioController@update')->name('inventarios.update');
		Route::delete('/inventario/{inventario}', 'App\\Http\\Controllers\\InventarioController@destroy')->name('inventarios.destroy');

		Route::get('/entregas', 'App\\Http\\Controllers\\EntregaController@index')->name('entregas.index');
		Route::get('/entregas/exportar-pdf', 'App\\Http\\Controllers\\EntregaController@exportPdf')->name('entregas.export-pdf');
		Route::get('/entregas/crear', 'App\\Http\\Controllers\\EntregaController@create')->name('entregas.create');
		Route::post('/entregas', 'App\\Http\\Controllers\\EntregaController@store')->name('entregas.store');
		Route::get('/entregas/{entrega}/editar', 'App\\Http\\Controllers\\EntregaController@edit')->name('entregas.edit');
		Route::put('/entregas/{entrega}', 'App\\Http\\Controllers\\EntregaController@update')->name('entregas.update');
		Route::delete('/entregas/{entrega}', 'App\\Http\\Controllers\\EntregaController@destroy')->name('entregas.destroy');
	});

	Route::middleware('rol:administrativo')->group(function () {
		Route::get('/despachadores', 'App\\Http\\Controllers\\DespachadorController@index')->name('despachadores.index');
		Route::get('/despachadores/crear', 'App\\Http\\Controllers\\DespachadorController@create')->name('despachadores.create');
		Route::post('/despachadores', 'App\\Http\\Controllers\\DespachadorController@store')->name('despachadores.store');
		Route::get('/despachadores/{despachador}/editar', 'App\\Http\\Controllers\\DespachadorController@edit')->name('despachadores.edit');
		Route::put('/despachadores/{despachador}', 'App\\Http\\Controllers\\DespachadorController@update')->name('despachadores.update');
		Route::patch('/despachadores/{despachador}/toggle', 'App\\Http\\Controllers\\DespachadorController@toggle')->name('despachadores.toggle');
		Route::delete('/despachadores/{despachador}', 'App\\Http\\Controllers\\DespachadorController@destroy')->name('despachadores.destroy');

		Route::get('/eps', 'App\\Http\\Controllers\\EpsController@index')->name('eps.index');
		Route::get('/eps/{ep}/editar', 'App\\Http\\Controllers\\EpsController@edit')->name('eps.edit');
		Route::post('/eps', 'App\\Http\\Controllers\\EpsController@store')->name('eps.store');
		Route::put('/eps/{ep}', 'App\\Http\\Controllers\\EpsController@update')->name('eps.update');
		Route::patch('/eps/{ep}/toggle', 'App\\Http\\Controllers\\EpsController@toggle')->name('eps.toggle');
		Route::delete('/eps/{ep}', 'App\\Http\\Controllers\\EpsController@destroy')->name('eps.destroy');

		Route::get('/pacientes', 'App\\Http\\Controllers\\PacienteController@index')->name('pacientes.index');
		Route::get('/pacientes/crear', 'App\\Http\\Controllers\\PacienteController@create')->name('pacientes.create');
		Route::get('/pacientes/importar/modelo', 'App\\Http\\Controllers\\PacienteController@downloadTemplate')->name('pacientes.import.template');
		Route::post('/pacientes/importar', 'App\\Http\\Controllers\\PacienteController@import')->name('pacientes.import');
		Route::post('/pacientes', 'App\\Http\\Controllers\\PacienteController@store')->name('pacientes.store');
		Route::get('/pacientes/{paciente}/editar', 'App\\Http\\Controllers\\PacienteController@edit')->name('pacientes.edit');
		Route::put('/pacientes/{paciente}', 'App\\Http\\Controllers\\PacienteController@update')->name('pacientes.update');
		Route::patch('/pacientes/{paciente}/toggle', 'App\\Http\\Controllers\\PacienteController@toggle')->name('pacientes.toggle');
		Route::delete('/pacientes/{paciente}', 'App\\Http\\Controllers\\PacienteController@destroy')->name('pacientes.destroy');

	});
});
