@extends('layouts.app')

@section('title', 'Reportar Nueva Incidencia - Waldo\'s')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div>
        <a href="{{ route('incidents.index') }}" class="text-xs font-bold dash-sub hover:text-amber-500 flex items-center space-x-1 mb-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            <span>Volver al Tablero</span>
        </a>
        <h1 class="text-3xl font-extrabold dash-title tracking-tight">Reportar Nueva Incidencia</h1>
        <p class="dash-sub text-sm mt-1">Levantamiento inicial para sucursales Waldo's (Paso 1 del Ciclo de Vida)</p>
    </div>

    <div class="glass-card rounded-2xl p-6 shadow-2xl">
        <form method="POST" action="{{ route('incidents.store') }}" class="space-y-4">
            @csrf

            <!-- Ruta Alterna de Emergencia Crítica Banner -->
            <div class="p-4 bg-rose-500/10 border border-rose-500/30 rounded-xl space-y-2">
                <div class="flex items-center space-x-2">
                    <input type="checkbox" id="es_emergencia" name="es_emergencia" value="1" class="h-4 w-4 rounded bg-slate-900 border-rose-500 text-rose-500 focus:ring-rose-500">
                    <label for="es_emergencia" class="text-sm font-extrabold text-rose-600 dark:text-rose-400 cursor-pointer">
                        🚨 Declarar como Falla de Emergencia Crítica (Ruta Alterna)
                    </label>
                </div>
                <p class="text-xs table-text-sub">
                    Habilita la <strong>Ruta Alterna de Ejecución Inmediata</strong>. Permite iniciar trabajos de reparación sin esperar la emisión previa de la Orden de Compra (OC), exigiendo su regularización post-facto.
                </p>
            </div>

            <div>
                <label class="block text-xs font-bold table-text-sub uppercase mb-1 flex items-center justify-between">
                    <span>Sucursal Waldo's</span>
                    <span class="text-[10px] text-amber-500 font-normal">⚡ Filtro incremental activo</span>
                </label>
                <select id="branch-select" name="branch_id" required placeholder="🔍 Escribe para buscar sucursal por nombre, zona o código..." autocomplete="off">
                    <option value="">🔍 Escribe para buscar sucursal por nombre, zona o código...</option>
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}">{{ $branch->nombre }} (Zona {{ $branch->zona_geografica }}) - {{ $branch->codigo_sucursal }}</option>
                    @endforeach
                </select>
            </div>

            <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css" rel="stylesheet">
            <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
            <style>
                .ts-wrapper {
                    width: 100%;
                }
                .ts-control {
                    border-radius: 0.75rem !important;
                    padding: 0.65rem 1rem !important;
                    font-size: 0.875rem !important;
                    transition: all 0.2s ease;
                }
                html.light .ts-control {
                    background-color: #ffffff !important;
                    border-color: #cbd5e1 !important;
                    color: #0f172a !important;
                }
                html.light .ts-dropdown {
                    background-color: #ffffff !important;
                    border-color: #cbd5e1 !important;
                    color: #0f172a !important;
                    border-radius: 0.75rem !important;
                    box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1) !important;
                }
                html.light .ts-dropdown .option.active, html.light .ts-dropdown .option:hover {
                    background-color: #f1f5f9 !important;
                    color: #0f172a !important;
                }
                html.dark .ts-control {
                    background-color: #0f172a !important;
                    border-color: #334155 !important;
                    color: #ffffff !important;
                }
                html.dark .ts-dropdown {
                    background-color: #0f172a !important;
                    border-color: #334155 !important;
                    color: #ffffff !important;
                    border-radius: 0.75rem !important;
                    box-shadow: 0 10px 25px -5px rgba(0,0,0,0.5) !important;
                }
                html.dark .ts-dropdown .option.active, html.dark .ts-dropdown .option:hover {
                    background-color: #1e293b !important;
                    color: #fbbf24 !important;
                }
                .ts-control input {
                    color: inherit !important;
                }

                .form-input-theme {
                    width: 100%;
                    font-size: 0.875rem;
                    padding: 0.625rem 1rem;
                    border-radius: 0.75rem;
                    outline: none;
                    transition: border-color 0.2s;
                }
                html.light .form-input-theme {
                    background-color: #ffffff;
                    border: 1px solid #cbd5e1;
                    color: #0f172a;
                }
                html.light .form-input-theme:focus {
                    border-color: #f59e0b;
                }
                html.dark .form-input-theme {
                    background-color: #0f172a;
                    border: 1px solid #334155;
                    color: #ffffff;
                }
                html.dark .form-input-theme:focus {
                    border-color: #f59e0b;
                }
            </style>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    if (document.getElementById('branch-select')) {
                        new TomSelect('#branch-select', {
                            create: false,
                            maxOptions: 500,
                            placeholder: "🔍 Escribe para buscar sucursal por nombre, zona o código..."
                        });
                    }
                });
            </script>

            <div>
                <label class="block text-xs font-bold table-text-sub uppercase mb-1">Título de la Falla / Incidencia</label>
                <input type="text" name="titulo" required placeholder="ej. Fuga de agua o robo de interruptor termomagnético" class="form-input-theme">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold table-text-sub uppercase mb-1">Disciplina / Categoría</label>
                    <select name="categoria_id" required class="form-input-theme">
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold table-text-sub uppercase mb-1">Prioridad</label>
                    <select name="prioridad" required class="form-input-theme">
                        <option value="baja">Baja</option>
                        <option value="media" selected>Media</option>
                        <option value="alta">Alta</option>
                        <option value="critica">Crítica (Emergencia)</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold table-text-sub uppercase mb-1">Ubicación Específica en Sucursal</label>
                <input type="text" name="ubicacion_especifica" placeholder="ej. Pasillo 3 / Cuarto de Máquinas / Bodega" class="form-input-theme">
            </div>

            <div>
                <label class="block text-xs font-bold table-text-sub uppercase mb-1">Descripción Detallada del Problema</label>
                <textarea name="descripcion" rows="3" required placeholder="Proporcione todos los detalles conocidos sobre la falla..." class="form-input-theme"></textarea>
            </div>

            <div>
                <label class="block text-xs font-bold table-text-sub uppercase mb-1">Motivo de la Emergencia Crítica (Opcional)</label>
                <input type="text" name="motivo_emergencia" placeholder="ej. Riesgo de paro de tienda / Fuerza mayor" class="form-input-theme">
            </div>

            <button type="submit" class="w-full py-3 bg-amber-500 hover:bg-amber-400 text-slate-950 font-black rounded-xl text-sm transition shadow-lg shadow-amber-500/20 cursor-pointer">
                Registrar Incidencia (Paso 1 del Ciclo) &rarr;
            </button>
        </form>
    </div>
</div>
@endsection
