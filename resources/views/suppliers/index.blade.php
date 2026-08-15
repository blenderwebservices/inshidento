@extends('layouts.app')

@section('title', 'Directorio de Proveedores por Ubicación Geográfica - Waldo\'s')

@section('content')
<div class="space-y-8">
    <!-- Header Page Title -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-white tracking-tight">Directorio de Proveedores por Ubicación Geográfica</h1>
            <p class="text-slate-400 text-sm mt-1">Catálogo de contratistas y especialistas clasificados por las 9 Zonas de Waldo's</p>
        </div>
    </div>

    <!-- Filters Bar -->
    <div class="glass-card rounded-2xl p-4 border border-slate-800">
        <form method="GET" action="{{ route('suppliers.index') }}" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold text-slate-400 uppercase mb-1">Zona Geográfica de Cobertura</label>
                <select name="zona" onchange="this.form.submit()" class="w-full bg-slate-900 border border-slate-700 text-xs text-white px-3 py-2 rounded-xl focus:outline-none focus:border-amber-500">
                    <option value="">Todas las Zonas Geográficas</option>
                    @foreach($zonas as $z)
                        <option value="{{ $z }}" {{ request('zona') === $z ? 'selected' : '' }}>Zona {{ $z }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-400 uppercase mb-1">Buscar por Especialidad</label>
                <input type="text" name="especialidad" value="{{ request('especialidad') }}" placeholder="ej. Electricista, Climatización, Plomería..." class="w-full bg-slate-900 border border-slate-700 text-xs text-white px-3 py-2 rounded-xl focus:outline-none focus:border-amber-500">
            </div>
        </form>
    </div>

    <!-- Grid of Suppliers -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($suppliers as $supplier)
            <div class="glass-card rounded-2xl p-6 shadow-xl border border-slate-800 space-y-4 hover:border-amber-500/40 transition">
                <div class="flex items-start justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="h-12 w-12 rounded-2xl bg-amber-500/10 border border-amber-500/30 flex items-center justify-center font-black text-amber-400 text-lg">
                            {{ strtoupper(substr($supplier->name, 0, 2)) }}
                        </div>
                        <div>
                            <h3 class="font-extrabold text-white text-base leading-tight">{{ $supplier->name }}</h3>
                            <span class="text-xs text-slate-400 font-medium block">{{ $supplier->email }}</span>
                        </div>
                    </div>
                </div>

                <div class="p-3 bg-slate-900/90 rounded-xl border border-slate-800/80 space-y-2 text-xs">
                    <div class="flex justify-between">
                        <span class="text-slate-400">Especialidad:</span>
                        <span class="font-bold text-amber-400">{{ $supplier->especialidad ?? 'Mantenimiento General' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-400">Zona de Cobertura:</span>
                        <span class="font-bold text-white">Zona {{ $supplier->zona_cobertura ?? 'Nacional / Todas' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-400">Tickets Atendidos:</span>
                        <span class="font-bold text-emerald-400">{{ $supplier->total_tickets }} incidencias</span>
                    </div>
                </div>

                <div class="pt-2 flex items-center justify-between text-xs">
                    <span class="px-2.5 py-1 rounded-full font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                        ✓ Contratista Homologado
                    </span>
                </div>
            </div>
        @empty
            <div class="col-span-full glass-card p-12 text-center text-slate-500 rounded-2xl">
                No se encontraron proveedores que coincidan con la zona o especialidad seleccionada.
            </div>
        @endforelse
    </div>

    <div class="mt-4">
        {{ $suppliers->links() }}
    </div>
</div>
@endsection
