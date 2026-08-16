@extends('layouts.app')

@section('title', 'Orden de Compra ' . $purchaseOrder->folio_interno . ' - Waldo\'s')

@section('content')
<div class="space-y-6 max-w-5xl mx-auto">
    <!-- Back and Actions Bar -->
    <div class="flex items-center justify-between">
        <a href="{{ route('purchase-orders.index') }}" class="text-xs font-bold text-slate-400 hover:text-white flex items-center space-x-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            <span>Volver al Listado de OCs</span>
        </a>
        <div class="flex items-center space-x-3">
            <button onclick="window.print()" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-white font-bold rounded-xl text-xs transition border border-slate-700 flex items-center space-x-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                <span>Imprimir / Exportar Documento</span>
            </button>
            <a href="{{ route('incidents.show', $purchaseOrder->incident) }}" class="px-4 py-2 bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold rounded-xl text-xs transition shadow-lg shadow-amber-500/20">
                Ver Ticket {{ $purchaseOrder->incident->codigo_ticket ?? '' }}
            </a>
        </div>
    </div>

    <!-- Main Printable Document Container -->
    <div class="bg-white text-slate-900 rounded-3xl p-8 sm:p-12 shadow-2xl space-y-8 border border-slate-200">
        <!-- Header Document Banner -->
        <div class="flex flex-col sm:flex-row justify-between items-start border-b-2 border-slate-900 pb-6 gap-6">
            <div class="space-y-1">
                <div class="flex items-center space-x-3">
                    <div class="h-10 w-10 rounded-xl bg-amber-500 flex items-center justify-center font-black text-slate-900 text-xl">W</div>
                    <span class="font-extrabold text-2xl tracking-tight text-slate-900">ORDEN DE COMPRA OFICIAL</span>
                </div>
                <p class="text-xs font-semibold text-slate-600">DOCUMENTO VINCULANTE DE JUSTIFICACIÓN DE ATENCIÓN DE INCIDENCIA</p>
                <p class="text-xs text-slate-500">Waldo's Dólar Mart de México S. de R.L. de C.V.</p>
            </div>

            <div class="text-right space-y-1 bg-slate-50 p-4 rounded-2xl border border-slate-200 w-full sm:w-auto">
                <div class="text-xs font-bold text-slate-500 uppercase">Folio Interno Inshidento</div>
                <div class="text-2xl font-black text-amber-600 tracking-tight">{{ $purchaseOrder->folio_interno }}</div>
                <div class="text-xs font-bold text-slate-700 mt-1">
                    Folio Respuesta Cliente: 
                    <span class="text-emerald-700 font-extrabold">{{ $purchaseOrder->folio_cliente ?? 'PENDIENTE DE REGISTRO' }}</span>
                </div>
                <div class="text-xs text-slate-500 mt-1">Fecha Emisión: {{ $purchaseOrder->fecha_emision->format('d/m/Y H:i') }}</div>
            </div>
        </div>

        <!-- Supplier & Store Context Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-slate-50 p-6 rounded-2xl border border-slate-200 text-xs">
            <div class="space-y-2">
                <h4 class="font-bold text-slate-500 uppercase tracking-wider text-[11px]">Datos de la Sucursal & Ubicación</h4>
                <div class="text-sm font-extrabold text-slate-900">{{ $purchaseOrder->incident->branch->nombre ?? 'N/A' }}</div>
                <div class="text-slate-700"><strong>Zona Geográfica:</strong> {{ $purchaseOrder->incident->branch->zona_geografica ?? 'N/A' }}</div>
                <div class="text-slate-700"><strong>Código Sucursal:</strong> {{ $purchaseOrder->incident->branch->codigo_sucursal ?? 'N/A' }}</div>
                <div class="text-slate-700"><strong>Dirección:</strong> {{ $purchaseOrder->incident->branch->direccion ?? 'N/A' }}</div>
                <div class="text-slate-700"><strong>Ticket Justificado:</strong> {{ $purchaseOrder->incident->codigo_ticket ?? 'N/A' }} - {{ $purchaseOrder->incident->titulo ?? '' }}</div>
            </div>

            <div class="space-y-2 border-t md:border-t-0 md:border-l border-slate-200 pt-4 md:pt-0 md:pl-6">
                <h4 class="font-bold text-slate-500 uppercase tracking-wider text-[11px]">Proveedor Asignado / Contratista</h4>
                <div class="text-sm font-extrabold text-slate-900">{{ $purchaseOrder->supplier->name ?? 'N/A' }}</div>
                <div class="text-slate-700"><strong>Contacto:</strong> {{ $purchaseOrder->supplier->email ?? 'N/A' }}</div>
                <div class="text-slate-700"><strong>Especialidad:</strong> {{ $purchaseOrder->supplier->especialidad ?? 'Mantenimiento General' }}</div>
                <div class="text-slate-700"><strong>Estado Autorización:</strong> <span class="uppercase font-bold text-amber-600">{{ $purchaseOrder->estado }}</span></div>
            </div>
        </div>

        <!-- Items Table -->
        <div class="space-y-3">
            <h4 class="font-bold text-slate-900 uppercase tracking-wider text-xs">Desglose de Conceptos (Catálogo de Precios Unitarios)</h4>
            <div class="border border-slate-200 rounded-2xl overflow-hidden">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-100 font-bold text-slate-700 uppercase border-b border-slate-200">
                        <tr>
                            <th class="px-4 py-3">Código Concepto</th>
                            <th class="px-4 py-3">Descripción Técnica</th>
                            <th class="px-4 py-3 text-center">Unidad</th>
                            <th class="px-4 py-3 text-center">Cantidad</th>
                            <th class="px-4 py-3 text-right">Precio Unitario</th>
                            <th class="px-4 py-3 text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @foreach($purchaseOrder->items as $item)
                            <tr>
                                <td class="px-4 py-3 font-mono font-bold text-slate-800">{{ $item->codigo_concepto }}</td>
                                <td class="px-4 py-3 text-slate-800 font-medium">{{ $item->descripcion }}</td>
                                <td class="px-4 py-3 text-center text-slate-600">{{ $item->unidad_medida }}</td>
                                <td class="px-4 py-3 text-center font-bold text-slate-900">{{ number_format($item->cantidad, 2) }}</td>
                                <td class="px-4 py-3 text-right text-slate-700">${{ number_format($item->precio_unitario, 2) }}</td>
                                <td class="px-4 py-3 text-right font-bold text-slate-900">${{ number_format($item->subtotal, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Totals & Financial Summary -->
        <div class="flex flex-col sm:flex-row justify-between items-start gap-6 border-t border-slate-200 pt-6">
            <div class="space-y-2 text-xs text-slate-600 max-w-md">
                <h5 class="font-bold text-slate-900">Notas de Autorización & Términos Fiscales</h5>
                <p>{{ $purchaseOrder->notas ?? 'Esta Orden de Compra constituye la autorización formal de ejecución. Se requiere la entrega de evidencias generadoras y checklist documental (REPSE, IMSS, XML/PDF Factura) para tramitar la liquidación financiera.' }}</p>
            </div>

            <div class="w-full sm:w-64 space-y-2 bg-slate-50 p-4 rounded-2xl border border-slate-200 text-xs text-right">
                <div class="flex justify-between font-medium text-slate-600">
                    <span>Subtotal:</span>
                    <span class="font-bold text-slate-900">${{ number_format($purchaseOrder->subtotal, 2) }}</span>
                </div>
                <div class="flex justify-between font-medium text-slate-600">
                    <span>IVA (16%):</span>
                    <span class="font-bold text-slate-900">${{ number_format($purchaseOrder->iva, 2) }}</span>
                </div>
                <div class="flex justify-between font-extrabold text-sm text-slate-900 border-t border-slate-300 pt-2 mt-2">
                    <span>Total Autorizado:</span>
                    <span class="text-amber-600">${{ number_format($purchaseOrder->monto_total, 2) }} MXN</span>
                </div>
            </div>
        </div>

        <!-- Signatures Block -->
        <div class="grid grid-cols-2 gap-8 border-t border-slate-200 pt-12 text-center text-xs">
            <div class="space-y-8">
                <div class="h-12 border-b border-dashed border-slate-400 max-w-xs mx-auto"></div>
                <div>
                    <div class="font-extrabold text-slate-900">Ing. Enrique Peinbert</div>
                    <div class="text-slate-500">Facility Manager Waldo's (Autorizó)</div>
                </div>
            </div>
            <div class="space-y-8">
                <div class="h-12 border-b border-dashed border-slate-400 max-w-xs mx-auto"></div>
                <div>
                    <div class="font-extrabold text-slate-900">{{ $purchaseOrder->supplier->name ?? 'Proveedor' }}</div>
                    <div class="text-slate-500">Representante Técnico / Contratista</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
