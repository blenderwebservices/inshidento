@extends('layouts.app')

@section('title', 'Tablero de Incidencias (Flujo de 10 Pasos) - Waldo\'s')

@section('content')
<div class="space-y-8">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-white tracking-tight">Tablero de Incidencias Operativas</h1>
            <p class="text-slate-400 text-sm mt-1">Seguimiento en tiempo real del ciclo de vida de 10 pasos en sucursales Waldo's</p>
        </div>
        <a href="{{ route('incidents.create') }}" class="px-4 py-2.5 bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold rounded-xl shadow-lg shadow-amber-500/20 text-sm transition flex items-center space-x-2 w-fit">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
            <span>Reportar Nueva Incidencia</span>
        </a>
    </div>

    <!-- Filters Bar -->
    <div class="glass-card rounded-2xl p-4 border border-slate-800">
        <form method="GET" action="{{ route('incidents.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label class="block text-xs font-bold text-slate-400 uppercase mb-1">Zona Geográfica</label>
                <select name="zona" onchange="this.form.submit()" class="w-full bg-slate-900 border border-slate-700 text-xs text-white px-3 py-2 rounded-xl focus:outline-none focus:border-amber-500">
                    <option value="">Todas las 9 Zonas</option>
                    @foreach($zonas as $z)
                        <option value="{{ $z }}" {{ request('zona') === $z ? 'selected' : '' }}>{{ $z }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-400 uppercase mb-1">Disciplina / Categoría</label>
                <select name="categoria_id" onchange="this.form.submit()" class="w-full bg-slate-900 border border-slate-700 text-xs text-white px-3 py-2 rounded-xl focus:outline-none focus:border-amber-500">
                    <option value="">Todas las Disciplinas</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('categoria_id') == $cat->id ? 'selected' : '' }}>{{ $cat->nombre }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-400 uppercase mb-1">Etapa del Ciclo (10 Pasos)</label>
                <select name="estado" onchange="this.form.submit()" class="w-full bg-slate-900 border border-slate-700 text-xs text-white px-3 py-2 rounded-xl focus:outline-none focus:border-amber-500">
                    <option value="">Todas las Etapas</option>
                    @foreach(\App\Models\Incident::LIFECYCLE_STEPS as $num => $step)
                        <option value="{{ $step['key'] }}" {{ request('estado') === $step['key'] ? 'selected' : '' }}>{{ $step['name'] }}</option>
                    @endforeach
                </select>
            </div>
        </form>
    </div>

    <!-- Table of Incidents -->
    <div class="glass-card rounded-2xl overflow-hidden shadow-2xl border border-slate-800">
        <div class="px-6 py-5 border-b border-slate-800 flex items-center justify-between">
            <h2 class="text-lg font-extrabold text-white">Tickets Registrados</h2>
            <span class="text-xs text-slate-400">Total: <strong>{{ $incidents->total() }}</strong></span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-300">
                <thead class="bg-slate-950/80 text-xs uppercase font-bold text-slate-400 border-b border-slate-800">
                    <tr>
                        <th class="px-6 py-4">Ticket / Sucursal</th>
                        <th class="px-6 py-4">Falla & Disciplina</th>
                        <th class="px-6 py-4 text-center">Progreso (10 Pasos)</th>
                        <th class="px-6 py-4 text-center">Orden de Compra</th>
                        <th class="px-6 py-4 text-center">Prioridad</th>
                        <th class="px-6 py-4 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60 font-medium">
                    @forelse($incidents as $inc)
                        @php
                            $stepNum = $inc->getCurrentStepNumber();
                            $stepInfo = \App\Models\Incident::LIFECYCLE_STEPS[$stepNum] ?? ['name' => $inc->estado];
                        @endphp
                        <tr class="hover:bg-slate-800/40 transition">
                            <td class="px-6 py-4">
                                <span class="font-extrabold text-white text-sm block">{{ $inc->codigo_ticket }}</span>
                                <span class="text-xs text-amber-400 font-semibold">{{ $inc->branch->nombre ?? 'N/A' }}</span>
                                <span class="text-xs text-slate-500 block">Zona {{ $inc->branch->zona_geografica ?? 'N/A' }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-slate-200 text-sm font-bold block">{{ $inc->titulo }}</span>
                                <span class="text-xs text-slate-400">{{ $inc->category->nombre ?? 'N/A' }}</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="space-y-1">
                                    <span class="px-2.5 py-1 rounded-full text-xs font-extrabold bg-amber-500/10 text-amber-400 border border-amber-500/20 block">
                                        Paso {{ $stepNum }}: {{ $stepInfo['name'] }}
                                    </span>
                                    <!-- Progress bar -->
                                    <div class="w-full bg-slate-800 h-1.5 rounded-full overflow-hidden">
                                        <div class="bg-amber-400 h-full rounded-full" style="width: {{ ($stepNum / 10) * 100 }}%"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($inc->purchaseOrder)
                                    <a href="{{ route('purchase-orders.show', $inc->purchaseOrder) }}" class="inline-block px-2.5 py-1 rounded bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 text-xs font-bold hover:underline">
                                        {{ $inc->purchaseOrder->folio_interno }}
                                    </a>
                                @else
                                    <span class="text-xs text-slate-500 italic">Sin OC Emitida</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                @php
                                    $prioBadge = [
                                        'baja' => 'bg-slate-700 text-slate-300',
                                        'media' => 'bg-blue-500/10 text-blue-400 border border-blue-500/20',
                                        'alta' => 'bg-amber-500/10 text-amber-400 border border-amber-500/20',
                                        'critica' => 'bg-rose-500/10 text-rose-400 border border-rose-500/20',
                                    ];
                                @endphp
                                <span class="px-2.5 py-0.5 rounded text-xs font-extrabold uppercase {{ $prioBadge[$inc->prioridad] ?? '' }}">
                                    {{ $inc->prioridad }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('incidents.show', $inc) }}" class="px-3 py-1.5 bg-amber-500 hover:bg-amber-400 text-slate-950 rounded-xl text-xs font-bold transition shadow-md shadow-amber-500/20">
                                    Gestionar Ticket &rarr;
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-slate-500">
                                No se encontraron incidencias que coincidan con los filtros.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t border-slate-800">
            {{ $incidents->links() }}
        </div>
    </div>
</div>
@endsection
