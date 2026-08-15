@extends('layouts.app')

@section('title', 'Ticket ' . $incident->codigo_ticket . ' - Waldo\'s')

@section('content')
<div class="space-y-8">
    <!-- Header Page Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <a href="{{ route('incidents.index') }}" class="text-xs font-bold text-slate-400 hover:text-white flex items-center space-x-1 mb-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                <span>Volver al Tablero</span>
            </a>
            <div class="flex items-center space-x-3">
                <h1 class="text-3xl font-black text-white tracking-tight">{{ $incident->codigo_ticket }}</h1>
                <span class="px-3 py-1 rounded-full text-xs font-extrabold bg-amber-500/10 text-amber-400 border border-amber-500/30 uppercase">
                    {{ $incident->prioridad }}
                </span>
            </div>
            <p class="text-sm text-slate-400 mt-1">{{ $incident->titulo }} &bull; <strong class="text-white">{{ $incident->branch->nombre }}</strong> (Zona {{ $incident->branch->zona_geografica }})</p>
        </div>

        @if($incident->purchaseOrder)
            <div class="glass-card p-4 rounded-2xl border border-emerald-500/30 bg-emerald-500/5 space-y-1">
                <span class="text-xs font-bold text-slate-400 uppercase block">Orden de Compra Emitida</span>
                <a href="{{ route('purchase-orders.show', $incident->purchaseOrder) }}" class="text-lg font-black text-emerald-400 hover:underline block">
                    {{ $incident->purchaseOrder->folio_interno }}
                </a>
                <span class="text-xs text-slate-400 block">
                    Folio Cliente: <strong class="text-white">{{ $incident->purchaseOrder->folio_cliente ?? 'Pendiente' }}</strong>
                </span>
            </div>
        @endif
    </div>

    <!-- 10-Step Interactive Lifecycle Stepper -->
    <div class="glass-card rounded-2xl p-6 shadow-2xl border border-slate-800 space-y-4">
        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Ciclo de Vida de la Incidencia (Flujo de 10 Pasos)</h3>
        
        <div class="grid grid-cols-2 sm:grid-cols-5 lg:grid-cols-10 gap-2">
            @php $currentStepNum = $incident->getCurrentStepNumber(); @endphp
            @foreach(\App\Models\Incident::LIFECYCLE_STEPS as $num => $step)
                @php
                    $isCompleted = $num < $currentStepNum;
                    $isCurrent = $num === $currentStepNum;
                    $isPending = $num > $currentStepNum;
                @endphp
                <div class="flex flex-col items-center text-center p-2 rounded-xl border transition {{ $isCurrent ? 'bg-amber-500/20 border-amber-500 text-amber-300 font-extrabold shadow-lg shadow-amber-500/10' : ($isCompleted ? 'bg-emerald-500/10 border-emerald-500/30 text-emerald-400' : 'bg-slate-900/60 border-slate-800 text-slate-500') }}">
                    <div class="h-6 w-6 rounded-full flex items-center justify-center text-xs font-black mb-1 {{ $isCurrent ? 'bg-amber-500 text-slate-950' : ($isCompleted ? 'bg-emerald-500 text-slate-950' : 'bg-slate-800 text-slate-400') }}">
                        {{ $num }}
                    </div>
                    <span class="text-[10px] leading-tight font-medium">{{ $step['name'] }}</span>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Two Columns Layout: Workflow Actions vs Incident Context & History -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Left 2-Cols: Active Step Control Panel -->
        <div class="lg:col-span-2 space-y-6">

            <!-- Dynamic Action Card depending on Current Step -->
            <div class="glass-card rounded-2xl p-6 shadow-xl border border-slate-800 space-y-6">
                <div class="flex items-center justify-between border-b border-slate-800 pb-4">
                    <div>
                        <span class="text-xs font-bold text-amber-400 uppercase tracking-wider">Acción Requerida para Paso {{ $currentStepNum }}</span>
                        <h2 class="text-xl font-black text-white mt-0.5">{{ \App\Models\Incident::LIFECYCLE_STEPS[$currentStepNum]['name'] }}</h2>
                    </div>
                    <span class="px-3 py-1 bg-slate-800 text-slate-300 text-xs font-bold rounded-lg border border-slate-700">
                        Actor: {{ \App\Models\Incident::LIFECYCLE_STEPS[$currentStepNum]['role'] }}
                    </span>
                </div>

                <!-- PASO 1 -> PASO 2: Asignar Proveedor -->
                @if($incident->estado === 'registrada')
                    <form method="POST" action="{{ route('incidents.advance', $incident) }}" class="space-y-4">
                        @csrf
                        <input type="hidden" name="next_state" value="proveedor_asignado">
                        <div>
                            <label class="block text-xs font-bold text-slate-300 mb-1">Seleccionar Proveedor / Contratista Especialista</label>
                            <select name="fixer_id" class="w-full bg-slate-900 border border-slate-700 text-sm text-white px-4 py-2.5 rounded-xl focus:outline-none focus:border-amber-500">
                                @foreach($fixers as $fixer)
                                    <option value="{{ $fixer->id }}">{{ $fixer->name }} - {{ $fixer->especialidad ?? 'General' }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-300 mb-1">Comentario / Instrucción para Asignación</label>
                            <textarea name="comentario" rows="2" class="w-full bg-slate-900 border border-slate-700 text-sm text-white px-4 py-2.5 rounded-xl focus:outline-none focus:border-amber-500" placeholder="Instrucción previa a la visita técnica..."></textarea>
                        </div>
                        <button type="submit" class="w-full py-3 bg-amber-500 hover:bg-amber-400 text-slate-950 font-black rounded-xl text-sm transition shadow-lg shadow-amber-500/20">
                            Avanzar a Paso 2: Asignar Proveedor &rarr;
                        </button>
                    </form>
                @endif

                <!-- PASO 2 -> PASO 3: Cargar Diagnóstico -->
                @if($incident->estado === 'proveedor_asignado')
                    <form method="POST" action="{{ route('incidents.advance', $incident) }}" class="space-y-4">
                        @csrf
                        <input type="hidden" name="next_state" value="diagnostico_cargado">
                        <div>
                            <label class="block text-xs font-bold text-slate-300 mb-1">Levantamiento y Reporte de Diagnóstico Técnico</label>
                            <textarea name="diagnostico_texto" rows="3" required class="w-full bg-slate-900 border border-slate-700 text-sm text-white px-4 py-2.5 rounded-xl focus:outline-none focus:border-amber-500" placeholder="Describa la causa raíz encontrada en la inspección física..."></textarea>
                        </div>
                        <button type="submit" class="w-full py-3 bg-amber-500 hover:bg-amber-400 text-slate-950 font-black rounded-xl text-sm transition shadow-lg shadow-amber-500/20">
                            Guardar Diagnóstico y Avanzar a Paso 3 &rarr;
                        </button>
                    </form>
                @endif

                <!-- PASO 3 -> PASO 4: Propuesta Técnica y Cotización de Catálogo -->
                @if($incident->estado === 'diagnostico_cargado')
                    <form method="POST" action="{{ route('incidents.advance', $incident) }}" class="space-y-4">
                        @csrf
                        <input type="hidden" name="next_state" value="cotizacion_propuesta">
                        <div>
                            <label class="block text-xs font-bold text-slate-300 mb-1">Propuesta Técnica</label>
                            <textarea name="propuesta_tecnica" rows="3" required class="w-full bg-slate-900 border border-slate-700 text-sm text-white px-4 py-2.5 rounded-xl focus:outline-none focus:border-amber-500" placeholder="Detalle de solución técnica basada en precios unitarios..."></textarea>
                        </div>
                        <button type="submit" class="w-full py-3 bg-amber-500 hover:bg-amber-400 text-slate-950 font-black rounded-xl text-sm transition shadow-lg shadow-amber-500/20">
                            Presentar Propuesta & Cotización (Avanzar a Paso 4) &rarr;
                        </button>
                    </form>
                @endif

                <!-- PASO 4 -> PASO 5: Validación del Presupuesto por Waldo's FM -->
                @if($incident->estado === 'cotizacion_propuesta')
                    <div class="space-y-4">
                        <div class="p-4 bg-slate-900 rounded-xl border border-slate-800 space-y-2">
                            <span class="text-xs font-bold text-slate-400 uppercase">Propuesta Presentada</span>
                            <p class="text-sm text-white font-medium">{{ $incident->propuesta_tecnica }}</p>
                        </div>

                        <form method="POST" action="{{ route('incidents.advance', $incident) }}">
                            @csrf
                            <input type="hidden" name="next_state" value="cotizacion_validada">
                            <button type="submit" class="w-full py-3 bg-amber-500 hover:bg-amber-400 text-slate-950 font-black rounded-xl text-sm transition shadow-lg shadow-amber-500/20">
                                Aprobar Presupuesto (Avanzar a Paso 5: Listo para Emitir OC) &rarr;
                            </button>
                        </form>
                    </div>
                @endif

                <!-- PASO 5 -> PASO 6: Emisión Obligatoria de Orden de Compra (OC) -->
                @if($incident->estado === 'cotizacion_validada' || ($incident->estado === 'oc_emitida' && !$incident->purchaseOrder))
                    <div class="space-y-4">
                        <div class="bg-amber-500/10 border border-amber-500/30 p-4 rounded-xl text-amber-300 text-xs font-medium">
                            <strong class="font-bold block mb-1">Regla de Negocio Obligatoria (Pasos 5-6)</strong>
                            Se debe emitir formalmente la Orden de Compra (OC) basada en el Catálogo de Precios Unitarios para autorizar la ejecución de cualquier trabajo.
                        </div>

                        <form method="POST" action="{{ route('incidents.generate-po', $incident) }}" class="space-y-4">
                            @csrf
                            <h4 class="text-sm font-bold text-white uppercase">Agregar Conceptos de Catálogo (Zona {{ $incident->branch->zona_geografica }})</h4>

                            @if($catalogItems->count() > 0)
                                <div class="p-3 bg-slate-900 rounded-xl border border-slate-800 text-xs">
                                    <span class="font-bold text-slate-400 block mb-2">Concepto Predeterminado de Catálogo:</span>
                                    <div class="flex items-center justify-between text-white font-mono">
                                        <span>{{ $catalogItems->first()->codigo_concepto }} &bull; {{ $catalogItems->first()->descripcion }}</span>
                                        <span class="font-black text-amber-400">${{ number_format($catalogItems->first()->precio_unitario, 2) }}</span>
                                    </div>
                                    <input type="hidden" name="items[0][unit_price_catalog_id]" value="{{ $catalogItems->first()->id }}">
                                    <input type="hidden" name="items[0][codigo_concepto]" value="{{ $catalogItems->first()->codigo_concepto }}">
                                    <input type="hidden" name="items[0][descripcion]" value="{{ $catalogItems->first()->descripcion }}">
                                    <input type="hidden" name="items[0][unidad_medida]" value="{{ $catalogItems->first()->unidad_medida }}">
                                    <input type="hidden" name="items[0][precio_unitario]" value="{{ $catalogItems->first()->precio_unitario }}">
                                </div>
                            @else
                                <div class="grid grid-cols-2 gap-3">
                                    <input type="text" name="items[0][codigo_concepto]" value="CONCEPTO-GEN-01" placeholder="Código" class="bg-slate-900 border border-slate-700 text-xs text-white p-2.5 rounded-xl">
                                    <input type="text" name="items[0][descripcion]" value="Mantenimiento correctivo de falla" placeholder="Descripción" class="bg-slate-900 border border-slate-700 text-xs text-white p-2.5 rounded-xl">
                                    <input type="text" name="items[0][unidad_medida]" value="servicio" placeholder="Unidad" class="bg-slate-900 border border-slate-700 text-xs text-white p-2.5 rounded-xl">
                                    <input type="number" step="0.01" name="items[0][precio_unitario]" value="2500.00" placeholder="Precio" class="bg-slate-900 border border-slate-700 text-xs text-white p-2.5 rounded-xl">
                                </div>
                            @endif

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-slate-400 mb-1">Cantidad</label>
                                    <input type="number" step="0.01" name="items[0][cantidad]" value="1.00" class="w-full bg-slate-900 border border-slate-700 text-sm text-white px-3 py-2 rounded-xl">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-400 mb-1">Notas de Orden de Compra</label>
                                    <input type="text" name="notas" value="Autorizado por Facility Management Waldo's" class="w-full bg-slate-900 border border-slate-700 text-sm text-white px-3 py-2 rounded-xl">
                                </div>
                            </div>

                            <button type="submit" class="w-full py-3 bg-amber-500 hover:bg-amber-400 text-slate-950 font-black rounded-xl text-sm transition shadow-lg shadow-amber-500/20">
                                Emitir Orden de Compra (Paso 6) &rarr;
                            </button>
                        </form>
                    </div>
                @endif

                <!-- PASO 6 -> PASO 7: Iniciar Ejecución (Bloqueado si no hay OC) -->
                @if($incident->estado === 'oc_emitida' && $incident->purchaseOrder)
                    <div class="space-y-4">
                        <div class="p-4 bg-emerald-500/10 border border-emerald-500/30 rounded-xl text-xs space-y-2">
                            <span class="font-extrabold text-emerald-400 block text-sm">Orden de Compra Emitida: {{ $incident->purchaseOrder->folio_interno }}</span>
                            <div class="text-slate-300">Total Autorizado: <strong>${{ number_format($incident->purchaseOrder->monto_total, 2) }} MXN</strong></div>
                            <div class="text-slate-300">Folio Cliente: <strong class="text-amber-400">{{ $incident->purchaseOrder->folio_cliente ?? 'Respuesta pendiente' }}</strong></div>
                        </div>

                        <!-- Form para registrar folio cliente si aún no está cargado -->
                        @if(!$incident->purchaseOrder->folio_cliente)
                            <form method="POST" action="{{ route('purchase-orders.register-client-folio', $incident->purchaseOrder) }}" class="p-4 bg-slate-900 rounded-xl border border-amber-500/30 space-y-2">
                                @csrf
                                <label class="block text-xs font-bold text-amber-300">Registrar Folio Emitido por Cliente (Waldo's)</label>
                                <div class="flex items-center space-x-2">
                                    <input type="text" name="folio_cliente" placeholder="ej. WALDOS-OC-2026-981" required class="flex-1 bg-slate-950 border border-slate-700 text-xs text-white px-3 py-2 rounded-xl">
                                    <button type="submit" class="px-4 py-2 bg-amber-500 text-slate-950 font-bold text-xs rounded-xl hover:bg-amber-400">
                                        Registrar Folio
                                    </button>
                                </div>
                            </form>
                        @endif

                        <form method="POST" action="{{ route('incidents.advance', $incident) }}">
                            @csrf
                            <input type="hidden" name="next_state" value="en_ejecucion">
                            <button type="submit" class="w-full py-3 bg-purple-600 hover:bg-purple-500 text-white font-black rounded-xl text-sm transition shadow-lg shadow-purple-600/20">
                                Iniciar Trabajos de Ejecución (Avanzar a Paso 7) &rarr;
                            </button>
                        </form>
                    </div>
                @endif

                <!-- PASO 7 -> PASO 8: Validación de Entrega -->
                @if($incident->estado === 'en_ejecucion')
                    <form method="POST" action="{{ route('incidents.advance', $incident) }}" class="space-y-4">
                        @csrf
                        <input type="hidden" name="next_state" value="entrega_validada">
                        <div>
                            <label class="block text-xs font-bold text-slate-300 mb-1">Evidencia de Entrega / Generadores de Obra</label>
                            <textarea name="comentario" rows="3" required class="w-full bg-slate-900 border border-slate-700 text-sm text-white px-4 py-2.5 rounded-xl focus:outline-none focus:border-amber-500" placeholder="Detalle de trabajos concluidos y conformidad física de la tienda..."></textarea>
                        </div>
                        <button type="submit" class="w-full py-3 bg-amber-500 hover:bg-amber-400 text-slate-950 font-black rounded-xl text-sm transition shadow-lg shadow-amber-500/20">
                            Validar Entrega de Trabajos (Avanzar a Paso 8) &rarr;
                        </button>
                    </form>
                @endif

                <!-- PASO 8 -> PASO 9: Carga de Documentos Fiscales (Factura, REPSE, IMSS) -->
                @if($incident->estado === 'entrega_validada' || $incident->estado === 'proceso_administrativo')
                    <div class="space-y-6">
                        <div class="space-y-2">
                            <h4 class="text-sm font-extrabold text-white uppercase">Carga de Documentos de Cumplimiento (Paso 9)</h4>
                            <p class="text-xs text-slate-400">Suba los archivos PDF/XML para Facturación, REPSE y Opinión de Cumplimiento IMSS.</p>
                        </div>

                        <!-- Form para Cargar Documento -->
                        <form method="POST" action="{{ route('incidents.upload-docs', $incident) }}" class="p-4 bg-slate-900 rounded-xl border border-slate-800 space-y-4">
                            @csrf
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-slate-300 mb-1">Tipo de Documento</label>
                                    <select name="tipo_documento" class="w-full bg-slate-950 border border-slate-700 text-xs text-white px-3 py-2 rounded-xl">
                                        <option value="Factura_PDF">Factura Fiscal (PDF)</option>
                                        <option value="Factura_XML">Factura Fiscal Timbrada (XML)</option>
                                        <option value="Constancia_REPSE">Constancia REPSE Vigente</option>
                                        <option value="Opinion_IMSS">Opinión de Cumplimiento IMSS</option>
                                        <option value="Recibo_Pago">Comprobante de Pago de Cuotas</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-300 mb-1">Nombre / Identificación del Archivo</label>
                                    <input type="text" name="nombre_documento" required placeholder="ej. Factura_FAC-9821.pdf" class="w-full bg-slate-950 border border-slate-700 text-xs text-white px-3 py-2 rounded-xl">
                                </div>
                            </div>
                            <button type="submit" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-white font-bold rounded-xl text-xs border border-slate-700 w-full">
                                Cargar Documento Fiscal
                            </button>
                        </form>

                        <!-- Listado de Documentos Fiscales Cargados -->
                        @if($incident->documentos_fiscales && count($incident->documentos_fiscales) > 0)
                            <div class="space-y-2">
                                <h5 class="text-xs font-bold text-slate-400 uppercase">Documentos Cargados en Expediente</h5>
                                <div class="divide-y divide-slate-800 bg-slate-900 rounded-xl border border-slate-800">
                                    @foreach($incident->documentos_fiscales as $doc)
                                        <div class="p-3 flex items-center justify-between text-xs">
                                            <div class="space-y-0.5">
                                                <span class="font-bold text-white block">{{ $doc['nombre'] }}</span>
                                                <span class="text-slate-400">Tipo: <strong class="text-amber-400">{{ $doc['tipo'] }}</strong> &bull; Subido por: {{ $doc['subido_por'] ?? 'Sistema' }}</span>
                                            </div>
                                            <span class="px-2 py-1 rounded bg-emerald-500/10 text-emerald-400 text-[10px] font-bold border border-emerald-500/20">
                                                VERIFICADO
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        @if($incident->estado === 'proceso_administrativo')
                            <form method="POST" action="{{ route('incidents.advance', $incident) }}">
                                @csrf
                                <input type="hidden" name="next_state" value="cerrada">
                                <button type="submit" class="w-full py-3 bg-emerald-600 hover:bg-emerald-500 text-white font-black rounded-xl text-sm transition shadow-lg shadow-emerald-600/20">
                                    Finalizar Cierre Definitivo de Ticket (Paso 10) &rarr;
                                </button>
                            </form>
                        @endif
                    </div>
                @endif

                @if($incident->estado === 'cerrada')
                    <div class="p-6 bg-emerald-500/10 border border-emerald-500/30 rounded-2xl text-center space-y-2">
                        <div class="h-12 w-12 rounded-full bg-emerald-500 text-slate-950 font-black text-xl flex items-center justify-center mx-auto">✓</div>
                        <h4 class="text-lg font-black text-emerald-400">Ticket Cerrado & Finiquitado</h4>
                        <p class="text-xs text-slate-300">Fecha de Cierre: {{ $incident->fecha_resolucion ? $incident->fecha_resolucion->format('d/m/Y H:i') : now()->format('d/m/Y') }}</p>
                    </div>
                @endif
            </div>

            <!-- Details & Technical Diagnosis Panel -->
            <div class="glass-card rounded-2xl p-6 shadow-xl border border-slate-800 space-y-4">
                <h3 class="text-sm font-extrabold text-white uppercase tracking-wider">Información y Diagnóstico Técnico</h3>
                
                <div class="space-y-3 text-xs text-slate-300">
                    <div class="p-3 bg-slate-900 rounded-xl border border-slate-800">
                        <span class="text-slate-500 block font-bold mb-1">Descripción Inicial</span>
                        <p class="text-slate-200">{{ $incident->descripcion }}</p>
                    </div>

                    @if($incident->diagnostico_texto)
                        <div class="p-3 bg-slate-900 rounded-xl border border-slate-800">
                            <span class="text-amber-400 block font-bold mb-1">Diagnóstico de Causa Raíz</span>
                            <p class="text-slate-200">{{ $incident->diagnostico_texto }}</p>
                        </div>
                    @endif

                    @if($incident->propuesta_tecnica)
                        <div class="p-3 bg-slate-900 rounded-xl border border-slate-800">
                            <span class="text-blue-400 block font-bold mb-1">Propuesta Técnica Aprobada</span>
                            <p class="text-slate-200">{{ $incident->propuesta_tecnica }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right 1-Col: Timeline Log of Lifecycle Transitions -->
        <div class="space-y-6">
            <div class="glass-card rounded-2xl p-6 shadow-xl border border-slate-800 space-y-4">
                <h3 class="text-sm font-extrabold text-white uppercase tracking-wider">Historial de Auditoría & Traza</h3>

                <div class="relative border-l-2 border-slate-800 pl-4 space-y-4 text-xs">
                    @foreach($incident->logs as $log)
                        <div class="space-y-1 relative">
                            <div class="absolute -left-[21px] top-1 h-3 w-3 rounded-full bg-amber-500"></div>
                            <div class="font-bold text-white">{{ $log->estado_nuevo }}</div>
                            <p class="text-slate-400">{{ $log->comentario }}</p>
                            <span class="text-[10px] text-slate-500 block">{{ $log->fecha ? \Carbon\Carbon::parse($log->fecha)->diffForHumans() : '' }} &bull; {{ $log->usuario->name ?? 'Usuario' }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
