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
            <a href="{{ route('incidents.create') }}" class="px-4 py-2.5 bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold rounded-xl shadow-lg shadow-amber-500/20 text-sm transition flex items-center space-x-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                <span>Nuevo Ticket de Incidencia</span>
            </a>
        </div>
    </div>

    <!-- KPI Metric Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <div class="glass-card rounded-2xl p-6 border-l-4 border-amber-500 shadow-xl">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Órdenes de Compra Emitidas</span>
                <div class="p-2.5 rounded-xl bg-amber-500/10 text-amber-400">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                </div>
            </div>
            <div class="mt-4">
                <span class="text-3xl font-black text-white">{{ number_format($resumenFinanciero['total_oc_emitidas']) }}</span>
                <span class="text-xs text-slate-400 block mt-1">OCs en sistema</span>
            </div>
        </div>

        <div class="glass-card rounded-2xl p-6 border-l-4 border-blue-500 shadow-xl">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Monto Comprometido (OCs)</span>
                <div class="p-2.5 rounded-xl bg-blue-500/10 text-blue-400">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
            <div class="mt-4">
                <span class="text-3xl font-black text-white">${{ number_format($resumenFinanciero['monto_comprometido'], 2) }}</span>
                <span class="text-xs text-slate-400 block mt-1">En emisión / ejecución</span>
            </div>
        </div>

        <div class="glass-card rounded-2xl p-6 border-l-4 border-emerald-500 shadow-xl">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Monto Facturado</span>
                <div class="p-2.5 rounded-xl bg-emerald-500/10 text-emerald-400">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
            <div class="mt-4">
                <span class="text-3xl font-black text-white">${{ number_format($resumenFinanciero['monto_facturado'], 2) }}</span>
                <span class="text-xs text-slate-400 block mt-1">Documentos fiscalmente liquidados</span>
            </div>
        </div>

        <div class="glass-card rounded-2xl p-6 border-l-4 border-purple-500 shadow-xl">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Costo Promedio por Ticket</span>
                <div class="p-2.5 rounded-xl bg-purple-500/10 text-purple-400">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                </div>
            </div>
            <div class="mt-4">
                <span class="text-3xl font-black text-white">${{ number_format($resumenFinanciero['promedio_ticket'], 2) }}</span>
                <span class="text-xs text-slate-400 block mt-1">Basado en catálogo de precios unitarios</span>
            </div>
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
                        <th class="px-6 py-4 text-center">En Proceso (Pasos 1-9)</th>
                        <th class="px-6 py-4 text-center">Cerradas (Paso 10)</th>
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
                            <span class="text-slate-400">Actor: <strong class="text-amber-400">{{ $paso['rol'] }}</strong> &bull; <strong class="text-white">{{ $paso['total'] }} tickets</strong></span>
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
