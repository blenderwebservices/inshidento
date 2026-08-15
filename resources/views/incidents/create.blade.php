@extends('layouts.app')

@section('title', 'Reportar Nueva Incidencia - Waldo\'s')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div>
        <a href="{{ route('incidents.index') }}" class="text-xs font-bold text-slate-400 hover:text-white flex items-center space-x-1 mb-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            <span>Volver al Tablero</span>
        </a>
        <h1 class="text-3xl font-extrabold text-white tracking-tight">Reportar Nueva Incidencia</h1>
        <p class="text-slate-400 text-sm mt-1">Levantamiento inicial para sucursales Waldo's (Paso 1 del Ciclo de Vida)</p>
    </div>

    <div class="glass-card rounded-2xl p-6 shadow-2xl border border-slate-800">
        <form method="POST" action="{{ route('incidents.store') }}" class="space-y-4">
            @csrf

            <div>
                <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Sucursal Waldo's</label>
                <select name="branch_id" required class="w-full bg-slate-900 border border-slate-700 text-sm text-white px-4 py-2.5 rounded-xl focus:outline-none focus:border-amber-500">
                    <option value="">Seleccione una sucursal...</option>
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}">{{ $branch->nombre }} (Zona {{ $branch->zona_geografica }}) - {{ $branch->codigo_sucursal }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Título de la Falla / Incidencia</label>
                <input type="text" name="titulo" required placeholder="ej. Fuga de agua en sanitario de clientes" class="w-full bg-slate-900 border border-slate-700 text-sm text-white px-4 py-2.5 rounded-xl focus:outline-none focus:border-amber-500">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Disciplina / Categoría</label>
                    <select name="categoria_id" required class="w-full bg-slate-900 border border-slate-700 text-sm text-white px-4 py-2.5 rounded-xl focus:outline-none focus:border-amber-500">
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Prioridad</label>
                    <select name="prioridad" required class="w-full bg-slate-900 border border-slate-700 text-sm text-white px-4 py-2.5 rounded-xl focus:outline-none focus:border-amber-500">
                        <option value="baja">Baja</option>
                        <option value="media" selected>Media</option>
                        <option value="alta">Alta</option>
                        <option value="critica">Crítica (Emergencia)</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Ubicación Específica en Sucursal</label>
                <input type="text" name="ubicacion_especifica" placeholder="ej. Pasillo 3 / Cuarto de Máquinas" class="w-full bg-slate-900 border border-slate-700 text-sm text-white px-4 py-2.5 rounded-xl focus:outline-none focus:border-amber-500">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Descripción Detallada del Problema</label>
                <textarea name="descripcion" rows="4" required placeholder="Proporcione todos los detalles conocidos sobre la falla..." class="w-full bg-slate-900 border border-slate-700 text-sm text-white px-4 py-2.5 rounded-xl focus:outline-none focus:border-amber-500"></textarea>
            </div>

            <button type="submit" class="w-full py-3 bg-amber-500 hover:bg-amber-400 text-slate-950 font-black rounded-xl text-sm transition shadow-lg shadow-amber-500/20">
                Registrar Incidencia (Paso 1 del Ciclo) &rarr;
            </button>
        </form>
    </div>
</div>
@endsection
