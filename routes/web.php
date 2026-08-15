<?php

use App\Http\Controllers\IncidentController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('reports.dashboard');
});

// Reportes y Métricas Ejecutivas (9 Zonas, Financiero OC)
Route::get('/reports/dashboard', [ReportController::class, 'dashboard'])->name('reports.dashboard');

// Gestor de Incidencias (Flujo 10 pasos)
Route::resource('incidents', IncidentController::class);
Route::post('/incidents/{incident}/advance', [IncidentController::class, 'advanceState'])->name('incidents.advance');
Route::post('/incidents/{incident}/generate-po', [IncidentController::class, 'generatePo'])->name('incidents.generate-po');
Route::post('/incidents/{incident}/upload-docs', [IncidentController::class, 'uploadDocuments'])->name('incidents.upload-docs');

// Módulo de Órdenes de Compra (OC)
Route::get('/purchase-orders', [PurchaseOrderController::class, 'index'])->name('purchase-orders.index');
Route::get('/purchase-orders/{purchaseOrder}', [PurchaseOrderController::class, 'show'])->name('purchase-orders.show');
Route::post('/purchase-orders/{purchaseOrder}/register-client-folio', [PurchaseOrderController::class, 'registerClientFolio'])->name('purchase-orders.register-client-folio');
