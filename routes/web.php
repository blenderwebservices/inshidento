<?php

use App\Http\Controllers\IncidentController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SupplierController;
use App\Mail\DemoRequestedMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('landing');
})->name('landing');

Route::get('/landing', function () {
    return view('landing');
});

// Reportes y Métricas Ejecutivas (9 Zonas, Financiero OC)
Route::get('/reports/dashboard', [ReportController::class, 'dashboard'])->name('reports.dashboard');

// Gestor de Incidencias (Flujo 10 pasos)
Route::resource('incidents', IncidentController::class);
Route::post('/incidents/{incident}/advance', [IncidentController::class, 'advanceState'])->name('incidents.advance');
Route::post('/incidents/{incident}/generate-po', [IncidentController::class, 'generatePo'])->name('incidents.generate-po');
Route::post('/incidents/{incident}/upload-docs', [IncidentController::class, 'uploadDocuments'])->name('incidents.upload-docs');
Route::post('/incidents/{incident}/upload-media', [IncidentController::class, 'uploadMedia'])->name('incidents.upload-media');

// Catálogo / Menú de Proveedores por Ubicación Geográfica
Route::get('/suppliers', [SupplierController::class, 'index'])->name('suppliers.index');

// Módulo de Órdenes de Compra (OC)
Route::get('/purchase-orders', [PurchaseOrderController::class, 'index'])->name('purchase-orders.index');
Route::get('/purchase-orders/{purchaseOrder}', [PurchaseOrderController::class, 'show'])->name('purchase-orders.show');
Route::post('/purchase-orders/{purchaseOrder}/register-client-folio', [PurchaseOrderController::class, 'registerClientFolio'])->name('purchase-orders.register-client-folio');

// Solicitud de Demo desde Landing
Route::post('/api/demo-request', function (Request $request) {
    $validated = $request->validate([
        'nombre' => 'required|string|max:255',
        'empresa' => 'required|string|max:255',
        'email' => 'required|email|max:255',
        'sucursales' => 'required|string|max:255',
    ]);

    $destinationEmail = env('DEMO_NOTIFICATION_EMAIL', 'blender.webservices@gmail.com');

    Mail::to($destinationEmail)->send(new DemoRequestedMail($validated));

    return response()->json([
        'success' => true,
        'message' => 'Solicitud enviada con éxito.'
    ]);
});

Route::get('/switch-role/{role}', [\App\Http\Controllers\RoleSwitchController::class, 'switchRole'])->name('switch-role');

Route::middleware(['web', 'auth'])->group(function () {
    Route::get('/impersonate/leave', [\App\Http\Controllers\ImpersonationController::class, 'leave'])->name('impersonate.leave');
    Route::get('/impersonate/{user}', [\App\Http\Controllers\ImpersonationController::class, 'impersonate'])->name('impersonate');
});

