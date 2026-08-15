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
                @if($incident->es_emergencia)
                    <span class="px-3 py-1 rounded-full text-xs font-extrabold bg-rose-500/20 text-rose-400 border border-rose-500/40 uppercase animate-pulse">
                        🚨 Ruta Alterna de Emergencia Crítica
                    </span>
                @endif
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

    <!-- Emergency Critical Warning Banner if Active -->
    @if($incident->es_emergencia)
        <div class="p-4 bg-rose-500/10 border border-rose-500/30 rounded-2xl space-y-2">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <span class="text-xl">🚨</span>
                    <div>
                        <h4 class="font-extrabold text-rose-400 text-sm">Falla Marcada como Emergencia Crítica</h4>
                        <p class="text-xs text-slate-300">Justificación: {{ $incident->motivo_emergencia ?? 'Fuerza mayor / riesgo inminente de operación' }}</p>
                    </div>
                </div>
                @if(!in_array($incident->estado, ['en_ejecucion', 'entrega_validada', 'proceso_administrativo', 'cerrada']))
                    <form method="POST" action="{{ route('incidents.advance', $incident) }}">
                        @csrf
                        <input type="hidden" name="next_state" value="en_ejecucion">
                        <button type="submit" class="px-4 py-2 bg-rose-600 hover:bg-rose-500 text-white font-black text-xs rounded-xl transition shadow-lg shadow-rose-600/30">
                            ⚡ Bypass Emergencia: Pasar Directo a Ejecución (Paso 7)
                        </button>
                    </form>
                @endif
            </div>
        </div>
    @endif

    <!-- 10-Step Interactive Lifecycle Stepper -->
    <div class="glass-card rounded-2xl p-6 shadow-2xl border border-slate-800 space-y-4">
        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Ciclo de Vida de la Incidencia (Flujo de 10 Pasos)</h3>
        
        <div class="grid grid-cols-2 sm:grid-cols-5 lg:grid-cols-10 gap-2">
            @php $currentStepNum = $incident->getCurrentStepNumber(); @endphp
            @foreach(\App\Models\Incident::LIFECYCLE_STEPS as $num => $step)
                @php
                    $isCompleted = $num < $currentStepNum;
                    $isCurrent = $num === $currentStepNum;
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

                <!-- PASO 1 -> PASO 2: Asignar Proveedor por Ubicación Geográfica -->
                @if($incident->estado === 'registrada')
                    <form method="POST" action="{{ route('incidents.advance', $incident) }}" class="space-y-4">
                        @csrf
                        <input type="hidden" name="next_state" value="proveedor_asignado">
                        <div>
                            <label class="block text-xs font-bold text-slate-300 mb-1">Seleccionar Proveedor / Contratista (Filtrado por Zona {{ $incident->branch->zona_geografica }})</label>
                            <select name="fixer_id" class="w-full bg-slate-900 border border-slate-700 text-sm text-white px-4 py-2.5 rounded-xl focus:outline-none focus:border-amber-500">
                                <optgroup label="Sugeridos para la Zona {{ $incident->branch->zona_geografica }}">
                                    @foreach($zoneSuppliers as $fixer)
                                        <option value="{{ $fixer->id }}">{{ $fixer->name }} - {{ $fixer->especialidad ?? 'General' }} (Zona {{ $fixer->zona_cobertura ?? 'Local' }})</option>
                                    @endforeach
                                </optgroup>
                                <optgroup label="Otras Zonas (Asignación de Contingencia / Emergencia)">
                                    @foreach($otherZoneSuppliers as $fixer)
                                        <option value="{{ $fixer->id }}">{{ $fixer->name }} - {{ $fixer->especialidad ?? 'General' }} (Zona {{ $fixer->zona_cobertura }})</option>
                                    @endforeach
                                </optgroup>
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

                <!-- PASO 3 -> PASO 4: Propuesta Técnica -->
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

                <!-- PASO 6 -> PASO 7: Iniciar Ejecución (Bloqueado si no hay OC, excepto en emergencias) -->
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

                <!-- PASO 8 -> PASO 9: Carga de Documentos Fiscales -->
                @if($incident->estado === 'entrega_validada' || $incident->estado === 'proceso_administrativo')
                    <div class="space-y-6">
                        <div class="space-y-2">
                            <h4 class="text-sm font-extrabold text-white uppercase">Carga de Documentos de Cumplimiento (Paso 9)</h4>
                            <p class="text-xs text-slate-400">Suba los archivos PDF/XML para Facturación, REPSE y Opinión de Cumplimiento IMSS.</p>
                        </div>

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

            <!-- SECTION FOR MEDIA & EXTERNAL CLOUD LINKS (Google Drive, MS 365, Dropbox) -->
            <div class="glass-card rounded-2xl p-6 shadow-xl border border-slate-800 space-y-6">
                <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                    <h3 class="text-sm font-extrabold text-white uppercase tracking-wider">Evidencia Multimedia & Enlaces Externos (Google Drive / MS 365)</h3>
                    <span class="text-xs text-slate-400">Validación de Peso (Máx 4 MB)</span>
                </div>

                <!-- Dual Tabs Forms: Direct File Upload vs External Link -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Form 1: Cargar Archivo (Máx 4MB) -->
                    <form method="POST" action="{{ route('incidents.upload-media', $incident) }}" enctype="multipart/form-data" class="p-4 bg-slate-900/90 rounded-xl border border-slate-800 space-y-3">
                        @csrf
                        <input type="hidden" name="origen" value="upload">
                        <span class="text-xs font-bold text-amber-400 uppercase block">📁 Subir Archivo Directo (Foto/Video)</span>
                        <div>
                            <label class="block text-[11px] text-slate-400 mb-1">Seleccionar Archivo (Máx 4 MB / Videos cortas 15-20s)</label>
                            <input type="file" name="archivo" required accept="image/*,video/mp4,video/webm,application/pdf" class="w-full text-xs text-slate-300 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-amber-500 file:text-slate-950 hover:file:bg-amber-400">
                        </div>
                        <div>
                            <input type="text" name="titulo" placeholder="Título / Descripción de la prueba" class="w-full bg-slate-950 border border-slate-700 text-xs text-white px-3 py-2 rounded-xl">
                        </div>
                        <button type="submit" class="w-full py-2 bg-amber-500 text-slate-950 font-bold rounded-xl text-xs hover:bg-amber-400">
                            Subir Archivo
                        </button>
                    </form>

                    <!-- Form 2: Adjuntar Enlace Externo (Google Drive / MS 365) -->
                    <form method="POST" action="{{ route('incidents.upload-media', $incident) }}" class="p-4 bg-slate-900/90 rounded-xl border border-slate-800 space-y-3">
                        @csrf
                        <input type="hidden" name="origen" value="external_link">
                        <span class="text-xs font-bold text-blue-400 uppercase block">🔗 Adjuntar Enlace a la Nube</span>
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <select name="plataforma" required class="w-full bg-slate-950 border border-slate-700 text-xs text-white px-2 py-2 rounded-xl">
                                    <option value="Google Drive">Google Drive</option>
                                    <option value="Microsoft 365 / OneDrive">Microsoft 365 / OneDrive</option>
                                    <option value="Dropbox">Dropbox</option>
                                    <option value="Google Sheets">Google Sheets</option>
                                    <option value="Google Docs / Slides">Google Docs / Slides</option>
                                    <option value="Otro">Otro Enlace Externo</option>
                                </select>
                            </div>
                            <div>
                                <input type="text" name="titulo" required placeholder="Nombre del Recurso" class="w-full bg-slate-950 border border-slate-700 text-xs text-white px-3 py-2 rounded-xl">
                            </div>
                        </div>
                        <div>
                            <input type="url" name="url_archivo" required placeholder="https://drive.google.com/file/d/..." class="w-full bg-slate-950 border border-slate-700 text-xs text-white px-3 py-2 rounded-xl">
                        </div>
                        <button type="submit" class="w-full py-2 bg-blue-600 text-white font-bold rounded-xl text-xs hover:bg-blue-500">
                            Adjuntar Enlace Externo
                        </button>
                    </form>
                </div>

                <!-- Gallery of Uploaded Media & Links -->
                @if($incident->media && $incident->media->count() > 0)
                    <div class="space-y-3">
                        <h4 class="text-xs font-bold text-slate-400 uppercase">Galería de Evidencias & Enlaces Registrados</h4>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($incident->media as $m)
                                <div class="p-3 bg-slate-900 rounded-xl border border-slate-800 space-y-2 text-xs">
                                    <div class="flex items-center justify-between">
                                        <span class="font-bold text-white truncate block max-w-[160px]">{{ $m->titulo ?? 'Evidencia' }}</span>
                                        @if($m->isExternalLink())
                                            <span class="px-2 py-0.5 rounded bg-blue-500/20 text-blue-300 font-extrabold text-[10px] border border-blue-500/30">
                                                {{ $m->plataforma ?? 'Nube' }}
                                            </span>
                                        @else
                                            <span class="px-2 py-0.5 rounded bg-amber-500/20 text-amber-300 font-extrabold text-[10px] border border-amber-500/30">
                                                {{ strtoupper($m->tipo) }} &bull; {{ $m->formatted_size }}
                                            </span>
                                        @endif
                                    </div>

                                    <!-- Content Preview -->
                                    @if($m->tipo === 'image')
                                        <a href="{{ $m->url_archivo }}" target="_blank" class="block">
                                            <img src="{{ $m->url_archivo }}" alt="Evidencia" class="w-full h-32 object-cover rounded-lg border border-slate-700">
                                        </a>
                                    @elseif($m->tipo === 'video')
                                        <video controls class="w-full h-32 bg-black rounded-lg border border-slate-700">
                                            <source src="{{ $m->url_archivo }}" type="video/mp4">
                                            Tu navegador no soporta reproducción de video.
                                        </video>
                                    @elseif($m->isExternalLink())
                                        <div class="p-4 bg-slate-950 rounded-lg border border-slate-800 text-center space-y-2">
                                            <div class="text-2xl">🔗</div>
                                            <a href="{{ $m->url_archivo }}" target="_blank" class="inline-block px-3 py-1.5 bg-blue-600 text-white font-bold rounded-lg text-xs hover:bg-blue-500">
                                                Abrir en {{ $m->plataforma ?? 'Nube' }} &rarr;
                                            </a>
                                        </div>
                                    @else
                                        <div class="p-4 bg-slate-950 rounded-lg border border-slate-800 text-center">
                                            <a href="{{ $m->url_archivo }}" target="_blank" class="text-amber-400 font-bold hover:underline">
                                                Ver Documento PDF/Adjunto &rarr;
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
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
