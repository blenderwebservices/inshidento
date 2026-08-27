<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Category;
use App\Models\Incident;
use App\Models\IncidentMedia;
use App\Models\UnitPriceCatalog;
use App\Models\User;
use App\Services\IncidentLifecycleService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IncidentController extends Controller
{
    protected IncidentLifecycleService $lifecycleService;

    public function __construct(IncidentLifecycleService $lifecycleService)
    {
        $this->lifecycleService = $lifecycleService;
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $allowedBranchIds = $user ? $user->assignedBranchIds() : null;

        $query = Incident::with(['branch', 'category', 'notifier', 'fixer', 'purchaseOrder']);

        if ($allowedBranchIds !== null) {
            $query->whereIn('branch_id', $allowedBranchIds);
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('zona')) {
            $query->whereHas('branch', function ($q) use ($request) {
                $q->where('zona_geografica', $request->zona);
            });
        }

        if ($request->filled('categoria_id')) {
            $query->where('categoria_id', $request->categoria_id);
        }

        if ($request->filled('es_emergencia')) {
            $query->where('es_emergencia', true);
        }

        $incidents = $query->latest()->paginate(15);
        $categories = Category::all();
        $zonas = Branch::ZONAS_GEOGRAFICAS;

        return view('incidents.index', compact('incidents', 'categories', 'zonas'));
    }

    public function create()
    {
        $user = Auth::user();
        if ($user && !$user->canCreateIncidents()) {
            return redirect()->route('incidents.index')->with('error', 'El rol Stakeholder sólo tiene permisos de lectura y no puede dar de alta incidencias.');
        }

        $branches = Branch::with('company')->get();
        $categories = Category::all();
        return view('incidents.create', compact('branches', 'categories'));
    }

    public function store(Request $request)
    {
        $user = Auth::user() ?? User::where('rol', 'notifier')->first() ?? User::first();
        if ($user && !$user->canCreateIncidents()) {
            return redirect()->route('incidents.index')->with('error', 'El rol Stakeholder sólo tiene permisos de lectura y no puede dar de alta incidencias.');
        }

        $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'titulo' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'categoria_id' => 'required|exists:categories,id',
            'prioridad' => 'required|in:baja,media,alta,critica',
            'ubicacion_especifica' => 'nullable|string',
            'es_emergencia' => 'nullable|boolean',
            'motivo_emergencia' => 'nullable|string',
        ]);

        $user = Auth::user() ?? User::where('rol', 'notifier')->first() ?? User::first();
        $isEmergency = (bool) $request->es_emergencia;

        $incident = Incident::create([
            'codigo_ticket' => Incident::generateTicketCode(),
            'branch_id' => $request->branch_id,
            'titulo' => $request->titulo,
            'descripcion' => $request->descripcion,
            'categoria_id' => $request->categoria_id,
            'prioridad' => $isEmergency ? 'critica' : $request->prioridad,
            'es_emergencia' => $isEmergency,
            'motivo_emergencia' => $request->motivo_emergencia,
            'estado' => 'registrada',
            'ubicacion_especifica' => $request->ubicacion_especifica,
            'notifier_id' => $user->id,
        ]);

        $msg = $isEmergency
            ? 'Incidencia de Emergencia Crítica registrada. Habilitada Ruta Alterna de Ejecución Inmediata.'
            : 'Incidencia registrada exitosamente.';

        return redirect()->route('incidents.show', $incident)->with('success', $msg);
    }

    public function show(Incident $incident)
    {
        $incident->load(['branch', 'category', 'notifier', 'manager', 'fixer', 'purchaseOrder.items', 'logs.usuario', 'media']);

        $storeZone = $incident->branch->zona_geografica ?? 'Centro';

        // Clasificación de proveedores por ubicación geográfica
        $zoneSuppliers = User::where('rol', 'fixer')
            ->where(function ($q) use ($storeZone) {
                $q->where('zona_cobertura', $storeZone)
                  ->orWhereNull('zona_cobertura');
            })->get();

        $otherZoneSuppliers = User::where('rol', 'fixer')
            ->whereNotNull('zona_cobertura')
            ->where('zona_cobertura', '!=', $storeZone)
            ->get();

        $catalogItems = UnitPriceCatalog::where('zona_geografica', $storeZone)
            ->where('categoria_id', $incident->categoria_id)
            ->get();

        if ($catalogItems->isEmpty()) {
            $catalogItems = UnitPriceCatalog::where('categoria_id', $incident->categoria_id)->get();
        }

        return view('incidents.show', compact('incident', 'zoneSuppliers', 'otherZoneSuppliers', 'catalogItems'));
    }

    /**
     * Avanzar paso en el ciclo de vida
     */
    public function advanceState(Request $request, Incident $incident)
    {
        $user = Auth::user() ?? User::first();
        if ($user && $user->isStakeholder()) {
            return redirect()->back()->with('error', 'El rol Stakeholder sólo tiene permisos de lectura y no puede modificar el estado de incidencias.');
        }

        $request->validate([
            'next_state' => 'required|string',
            'comentario' => 'nullable|string',
        ]);

        try {
            $extraData = [];
            if ($request->filled('diagnostico_texto')) {
                $extraData['diagnostico_texto'] = $request->diagnostico_texto;
            }
            if ($request->filled('propuesta_tecnica')) {
                $extraData['propuesta_tecnica'] = $request->propuesta_tecnica;
            }

            $this->lifecycleService->transitionTo($incident, $request->next_state, $user, $request->comentario, $extraData);
            return redirect()->back()->with('success', "Estado actualizado a '{$request->next_state}' exitosamente.");
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Emitir Orden de Compra desde el detalle de la incidencia (Paso 6)
     */
    public function generatePo(Request $request, Incident $incident)
    {
        $user = Auth::user() ?? User::first();
        if ($user && !$user->canManagePurchaseOrders()) {
            return redirect()->back()->with('error', 'El rol Stakeholder / Usuario no tiene permisos para emitir Órdenes de Compra.');
        }

        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.codigo_concepto' => 'required|string',
            'items.*.descripcion' => 'required|string',
            'items.*.unidad_medida' => 'required|string',
            'items.*.cantidad' => 'required|numeric|min:0.01',
            'items.*.precio_unitario' => 'required|numeric|min:0.01',
        ]);

        $user = Auth::user() ?? User::first();
        $po = $this->lifecycleService->generatePurchaseOrder($incident, $user, $request->items, $request->notas);

        return redirect()->route('purchase-orders.show', $po)->with('success', "Orden de Compra {$po->folio_interno} generada exitosamente.");
    }

    /**
     * Cargar Archivos con validación de peso (Máx 4 MB) o Adjuntar Enlaces Externos (Google Drive / MS 365)
     */
    public function uploadMedia(Request $request, Incident $incident)
    {
        $origen = $request->input('origen', 'upload');

        if ($origen === 'external_link') {
            $request->validate([
                'url_archivo' => 'required|url',
                'plataforma' => 'required|string',
                'titulo' => 'required|string|max:255',
            ]);

            IncidentMedia::create([
                'incident_id' => $incident->id,
                'origen' => 'external_link',
                'plataforma' => $request->plataforma, // Google Drive, Microsoft 365, Dropbox, etc.
                'titulo' => $request->titulo,
                'url_archivo' => $request->url_archivo,
                'tipo' => 'external_link',
                'fecha_carga' => now(),
            ]);

            return redirect()->back()->with('success', "Enlace externo '{$request->titulo}' de {$request->plataforma} agregado exitosamente.");
        } else {
            // Carga de archivo con validación de tipo de imagen (JPG, JPEG, PNG, GIF, TIF, TIFF, WEBP, BMP, SVG) y control de peso (Máx 10 MB = 10240 KB)
            $request->validate([
                'archivo' => 'required|file|mimes:jpg,jpeg,png,gif,tif,tiff,webp,bmp,svg,mp4,webm,pdf,doc,docx,xls,xlsx|max:10240',
                'titulo' => 'nullable|string|max:255',
            ]);

            $file = $request->file('archivo');
            $extension = strtolower($file->getClientOriginalExtension());
            $size = $file->getSize();

            $tipo = 'document';
            if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'tif', 'tiff', 'webp', 'bmp', 'svg'])) {
                $tipo = 'image';
            } elseif (in_array($extension, ['mp4', 'webm', 'mov', 'avi'])) {
                $tipo = 'video';
            }

            // Almacenar archivo en /storage/app/public/media
            $path = $file->store('media', 'public');
            $url = '/storage/' . $path;

            IncidentMedia::create([
                'incident_id' => $incident->id,
                'origen' => 'upload',
                'plataforma' => 'Plataforma Inshidento',
                'titulo' => $request->titulo ?? $file->getClientOriginalName(),
                'url_archivo' => $url,
                'tipo' => $tipo,
                'peso_bytes' => $size,
                'fecha_carga' => now(),
            ]);

            return redirect()->back()->with('success', "Archivo '{$file->getClientOriginalName()}' subido exitosamente (" . round($size / 1024 / 1024, 2) . " MB).");
        }
    }

    /**
     * Cargar documentos de cumplimiento fiscal/administrativo (Paso 9)
     */
    public function uploadDocuments(Request $request, Incident $incident)
    {
        $request->validate([
            'tipo_documento' => 'required|string',
            'nombre_documento' => 'required|string',
            'url_documento' => 'nullable|string',
        ]);

        $user = Auth::user() ?? User::first();

        $nuevoDoc = [
            'tipo' => $request->tipo_documento,
            'nombre' => $request->nombre_documento,
            'url' => $request->url_documento ?? '/storage/docs/' . uniqid() . '.pdf',
            'fecha_carga' => now()->toDateTimeString(),
            'subido_por' => $user->name,
        ];

        $this->lifecycleService->transitionTo($incident, 'proceso_administrativo', $user, "Carga de documento fiscal: {$request->nombre_documento}", [
            'documentos_fiscales' => [$nuevoDoc]
        ]);

        return redirect()->back()->with('success', "Documento '{$request->nombre_documento}' cargado exitosamente.");
    }
}
