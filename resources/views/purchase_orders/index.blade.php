@extends('layouts.app')

@section('title', 'Módulo de Órdenes de Compra (OC) - Waldo\'s')

@section('content')
<div class="space-y-8">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-white tracking-tight">Módulo de Órdenes de Compra (OC)</h1>
            <p class="text-slate-400 text-sm mt-1">Gestión de documentos vinculantes de justificación financiera y asignación de folios de cliente</p>
        </div>
    </div>

    <!-- Summary KPI Header Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
        <div class="glass-card rounded-2xl p-5 border-l-4 border-amber-500 shadow-xl">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Monto Total Comprometido (OCs)</span>
            <span class="text-2xl font-black text-white block mt-2">${{ number_format($metrics['monto_total_comprometido'], 2) }}</span>
            <span class="text-xs text-amber-400 mt-1 block">En OCs emitidas y en ejecución</span>
        </div>

        <div class="glass-card rounded-2xl p-5 border-l-4 border-emerald-500 shadow-xl">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Monto Facturado</span>
            <span class="text-2xl font-black text-white block mt-2">${{ number_format($metrics['monto_facturado'], 2) }}</span>
            <span class="text-xs text-emerald-400 mt-1 block">Con factura y checklist documental</span>
        </div>

        <div class="glass-card rounded-2xl p-5 border-l-4 border-blue-500 shadow-xl">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Pendientes Folio Cliente</span>
            <span class="text-2xl font-black text-white block mt-2">{{ $metrics['pendientes_folio_cliente'] }} OCs</span>
            <span class="text-xs text-blue-400 mt-1 block">Esperando respuesta/confirmación de cliente</span>
        </div>
    </div>

    <!-- Table of Purchase Orders -->
    <div class="glass-card rounded-2xl overflow-hidden shadow-2xl border border-slate-800">
        <div class="px-6 py-5 border-b border-slate-800 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <h2 class="text-lg font-extrabold text-white">Listado General de Órdenes de Compra</h2>
            <form method="GET" action="{{ route('purchase-orders.index') }}" class="flex items-center space-x-3">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar por Folio..." class="bg-slate-900 border border-slate-700 text-xs text-white px-3 py-2 rounded-xl focus:outline-none focus:border-amber-500 w-48 sm:w-64">
                <select name="estado" onchange="this.form.submit()" class="bg-slate-900 border border-slate-700 text-xs text-white px-3 py-2 rounded-xl focus:outline-none focus:border-amber-500">
                    <option value="">Todos los Estados</option>
                    @foreach(\App\Models\PurchaseOrder::ESTADOS as $k => $v)
                        <option value="{{ $k }}" {{ request('estado') === $k ? 'selected' : '' }}>{{ $v }}</option>
                    @endforeach
                </select>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-300">
                <thead class="bg-slate-950/80 text-xs uppercase font-bold text-slate-400 border-b border-slate-800">
                    <tr>
                        <th class="px-6 py-4">Folio Interno / Cliente</th>
                        <th class="px-6 py-4">Sucursal / Zona</th>
                        <th class="px-6 py-4">Proveedor (Fixer)</th>
                        <th class="px-6 py-4 text-center">Estado OC</th>
                        <th class="px-6 py-4 text-right">Monto Total (IVA inc.)</th>
                        <th class="px-6 py-4 text-center">Acciones / Registrar Folio</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60 font-medium">
                    @forelse($purchaseOrders as $po)
                        <tr class="hover:bg-slate-800/40 transition">
                            <td class="px-6 py-4">
                                <span class="font-extrabold text-white text-sm block">{{ $po->folio_interno }}</span>
                                @if($po->folio_cliente)
                                    <span class="inline-block mt-0.5 px-2 py-0.5 rounded bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 text-xs font-semibold">
                                        Cliente: {{ $po->folio_cliente }}
                                    </span>
                                @else
                                    <span class="inline-block mt-0.5 px-2 py-0.5 rounded bg-amber-500/10 text-amber-400 border border-amber-500/20 text-xs font-semibold">
                                        Esperando Folio Cliente
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-white font-bold block">{{ $po->incident->branch->nombre ?? 'N/A' }}</span>
                                <span class="text-xs text-slate-400">Zona {{ $po->incident->branch->zona_geografica ?? 'N/A' }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-slate-200 text-xs font-semibold block">{{ $po->supplier->name ?? 'Sin asignar' }}</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @php
                                    $badgeStyles = [
                                        'borrador' => 'bg-slate-700 text-slate-300',
                                        'emitida' => 'bg-amber-500/10 text-amber-400 border border-amber-500/30',
                                        'aprobada' => 'bg-blue-500/10 text-blue-400 border border-blue-500/30',
                                        'en_ejecucion' => 'bg-purple-500/10 text-purple-400 border border-purple-500/30',
                                        'facturada' => 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/30',
                                        'cancelada' => 'bg-rose-500/10 text-rose-400 border border-rose-500/30',
                                    ];
                                @endphp
                                <span class="px-2.5 py-1 rounded-full text-xs font-extrabold {{ $badgeStyles[$po->estado] ?? 'bg-slate-800 text-slate-300' }}">
                                    {{ \App\Models\PurchaseOrder::ESTADOS[$po->estado] ?? $po->estado }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right font-black text-amber-300 text-base">
                                ${{ number_format($po->monto_total, 2) }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center space-x-2">
                                    <a href="{{ route('purchase-orders.show', $po) }}" class="px-3 py-1.5 bg-slate-800 hover:bg-slate-700 text-white rounded-lg text-xs font-bold transition border border-slate-700">
                                        Ver Documento
                                    </a>

                                    @if(!$po->folio_cliente)
                                        <!-- Form modal inline para capturar folio del cliente -->
                                        <form method="POST" action="{{ route('purchase-orders.register-client-folio', $po) }}" class="flex items-center space-x-1">
                                            @csrf
                                            <input type="text" name="folio_cliente" placeholder="Folio Cliente (ej. WALDOS-OC-98)" required class="bg-slate-900 border border-amber-500/40 text-white text-xs px-2 py-1.5 rounded-lg focus:outline-none w-36">
                                            <button type="submit" title="Registrar respuesta del cliente" class="px-2.5 py-1.5 bg-amber-500 hover:bg-amber-400 text-slate-950 font-black rounded-lg text-xs transition">
                                                ✓
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-slate-500">
                                No se encontraron Órdenes de Compra registradas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t border-slate-800">
            {{ $purchaseOrders->links() }}
        </div>
    </div>
</div>
@endsection
