@extends('layouts.app')

@section('title', 'Dashboard de Reportes Ejecutivo & Métricas Regionales - Waldo\'s')

@section('content')
<div class="space-y-8">
    <!-- Header Page Title -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-white tracking-tight">Dashboard de Reportes & Métricas Regionales</h1>
            <p class="text-slate-400 text-sm mt-1">Análisis operativo y financiero para las 970 sucursales en las 9 Zonas Geográficas de Waldo's</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('suppliers.index') }}" class="px-4 py-2.5 bg-slate-800 hover:bg-slate-700 text-white font-bold rounded-xl text-sm transition border border-slate-700">
                👷 Menú Proveedores
            </a>
            <a href="{{ route('incidents.create') }}" class="px-4 py-2.5 bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold rounded-xl shadow-lg shadow-amber-500/20 text-sm transition flex items-center space-x-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                <span>Nuevo Ticket de Incidencia</span>
            </a>
        </div>
    </div>

    <!-- KPI Metric Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-5">
        <div class="glass-card rounded-2xl p-5 border-l-4 border-amber-500 shadow-xl">
            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Órdenes de Compra Emitidas</span>
            <span class="text-2xl font-black text-white block mt-2">{{ number_format($resumenFinanciero['total_oc_emitidas']) }}</span>
            <span class="text-xs text-slate-400 block mt-1">OCs registradas</span>
        </div>

        <div class="glass-card rounded-2xl p-5 border-l-4 border-blue-500 shadow-xl">
            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Monto Comprometido (OCs)</span>
            <span class="text-2xl font-black text-white block mt-2">${{ number_format($resumenFinanciero['monto_comprometido'], 2) }}</span>
            <span class="text-xs text-slate-400 block mt-1">En emisión / ejecución</span>
        </div>

        <div class="glass-card rounded-2xl p-5 border-l-4 border-emerald-500 shadow-xl">
            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Monto Facturado Liquidadas</span>
            <span class="text-2xl font-black text-white block mt-2">${{ number_format($resumenFinanciero['monto_facturado'], 2) }}</span>
            <span class="text-xs text-slate-400 block mt-1">Liquidación fiscal final</span>
        </div>

        <div class="glass-card rounded-2xl p-5 border-l-4 border-rose-500 shadow-xl">
            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Ruta de Emergencia Crítica</span>
            <span class="text-2xl font-black text-rose-400 block mt-2">{{ $resumenFinanciero['total_emergencias'] }} Tickets</span>
            <span class="text-xs text-rose-300 block mt-1">Atención inmediata (Bypass)</span>
        </div>

        <div class="glass-card rounded-2xl p-5 border-l-4 border-purple-500 shadow-xl">
            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Cotizaciones Pendientes</span>
            <span class="text-2xl font-black text-purple-300 block mt-2">{{ $resumenFinanciero['cotizaciones_pendientes'] }} Tickets</span>
            <span class="text-xs text-purple-400 block mt-1">Previsión presupuestal</span>
        </div>
    </div>

    <!-- Table: Reporte por Zonas Geográficas (Las 9 Zonas de Waldo's) -->
    <div class="glass-card rounded-2xl overflow-hidden shadow-2xl border border-slate-800">
        <div class="px-6 py-5 border-b border-slate-800 flex items-center justify-between">
            <div>
                <h2 class="text-lg font-extrabold text-white">Métricas de Operación por Zona Geográfica</h2>
                <p class="text-xs text-slate-400 mt-0.5">Desglose de las 9 zonas operativas supervisadas por Facility Managers (FMs)</p>
            </div>
            <span class="px-3 py-1 bg-amber-500/10 text-amber-400 border border-amber-500/20 text-xs font-bold rounded-full">9 Zonas Cobertura Total</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-300">
                <thead class="bg-slate-950/80 text-xs uppercase font-bold text-slate-400 border-b border-slate-800">
                    <tr>
                        <th class="px-6 py-4">Zona Geográfica</th>
                        <th class="px-6 py-4 text-center">Sucursales Activas</th>
                        <th class="px-6 py-4 text-center">Total Incidencias</th>
                        <th class="px-6 py-4 text-center">En Proceso</th>
                        <th class="px-6 py-4 text-center">Emergencias</th>
                        <th class="px-6 py-4 text-center">Cerradas</th>
                        <th class="px-6 py-4 text-right">Inversión Financiera (OCs)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60 font-medium">
                    @foreach($zonasReporte as $row)
                        <tr class="hover:bg-slate-800/40 transition">
                            <td class="px-6 py-4 font-bold text-white flex items-center space-x-2">
                                <span class="h-2.5 w-2.5 rounded-full bg-amber-400 inline-block"></span>
                                <span>{{ $row['zona'] }}</span>
                            </td>
                            <td class="px-6 py-4 text-center text-slate-300">{{ $row['total_sucursales'] }}</td>
                            <td class="px-6 py-4 text-center font-bold text-white">{{ $row['total_incidencias'] }}</td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-amber-500/10 text-amber-400 border border-amber-500/20">
                                    {{ $row['en_proceso'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-rose-500/10 text-rose-400 border border-rose-500/20">
                                    {{ $row['emergencias'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                    {{ $row['cerradas'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right font-black text-amber-300">
                                ${{ number_format($row['monto_invertido'], 2) }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Two Column Grid: Ciclo de Vida 10 Pasos & Disciplinas -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Ciclo de Vida: Distribución en los 10 Pasos -->
        <div class="glass-card rounded-2xl p-6 shadow-xl border border-slate-800 space-y-4">
            <div>
                <h2 class="text-lg font-extrabold text-white">Tablero del Ciclo de Vida (10 Pasos)</h2>
                <p class="text-xs text-slate-400">Distribución en vivo de tickets según la etapa del flujo</p>
            </div>

            <div class="space-y-3 pt-2">
                @foreach($incidenciasPorPaso as $paso)
                    @php
                        $percentage = $resumenFinanciero['total_oc_emitidas'] > 0 
                            ? min(100, round(($paso['total'] / max(1, array_sum(array_column($incidenciasPorPaso, 'total')))) * 100))
                            : 0;
                    @endphp
                    <div class="p-3 bg-slate-900/90 rounded-xl border border-slate-800">
                        <div class="flex justify-between items-center text-xs mb-1.5">
                            <span class="font-bold text-slate-200">{{ $paso['nombre'] }}</span>
                            <span class="text-slate-400">Actor: <strong class="text-amber-400">{{ $paso['role'] }}</strong> &bull; <strong class="text-white">{{ $paso['total'] }} tickets</strong></span>
                        </div>
                        <div class="w-full bg-slate-800 h-2 rounded-full overflow-hidden">
                            <div class="bg-gradient-to-r from-amber-500 to-amber-300 h-full rounded-full transition-all duration-500" style="width: {{ max(5, $percentage) }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Disciplinas y Categorías de Falla -->
        <div class="glass-card rounded-2xl p-6 shadow-xl border border-slate-800 space-y-4">
            <div>
                <h2 class="text-lg font-extrabold text-white">Análisis por Disciplina / Categoría</h2>
                <p class="text-xs text-slate-400">Volumen de solicitudes y costo acumulado por especialista</p>
            </div>

            <div class="space-y-4 pt-2">
                @foreach($categoriasReporte as $cat)
                    <div class="p-4 bg-slate-900/90 rounded-xl border border-slate-800 flex items-center justify-between">
                        <div class="space-y-1">
                            <h4 class="font-extrabold text-white text-base">{{ $cat['nombre'] }}</h4>
                            <div class="flex items-center space-x-3 text-xs text-slate-400">
                                <span>Volumen: <strong class="text-slate-200">{{ $cat['total_tickets'] }} incidencias</strong></span>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="text-xs text-slate-400 block">Total Financiero OC</span>
                            <span class="font-black text-amber-400 text-lg">${{ number_format($cat['monto_total'], 2) }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
