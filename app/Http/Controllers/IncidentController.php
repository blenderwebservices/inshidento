<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Category;
use App\Models\Incident;
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
        $query = Incident::with(['branch', 'category', 'notifier', 'fixer', 'purchaseOrder']);

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

        $incidents = $query->latest()->paginate(15);
        $categories = Category::all();
        $zonas = Branch::ZONAS_GEOGRAFICAS;

        return view('incidents.index', compact('incidents', 'categories', 'zonas'));
    }

    public function create()
    {
        $branches = Branch::with('company')->get();
        $categories = Category::all();
        return view('incidents.create', compact('branches', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'titulo' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'categoria_id' => 'required|exists:categories,id',
            'prioridad' => 'required|in:baja,media,alta,critica',
            'ubicacion_especifica' => 'nullable|string',
        ]);

        $user = Auth::user() ?? User::where('rol', 'notifier')->first() ?? User::first();

        $incident = Incident::create([
            'codigo_ticket' => Incident::generateTicketCode(),
            'branch_id' => $request->branch_id,
            'titulo' => $request->titulo,
            'descripcion' => $request->descripcion,
            'categoria_id' => $request->categoria_id,
            'prioridad' => $request->prioridad,
            'estado' => 'registrada',
            'ubicacion_especifica' => $request->ubicacion_especifica,
            'notifier_id' => $user->id,
        ]);

        return redirect()->route('incidents.show', $incident)->with('success', 'Incidencia registrada exitosamente.');
    }

    public function show(Incident $incident)
    {
        $incident->load(['branch', 'category', 'notifier', 'manager', 'fixer', 'purchaseOrder.items', 'logs.usuario', 'media']);
        $fixers = User::where('rol', 'fixer')->get();
        $catalogItems = UnitPriceCatalog::where('zona_geografica', $incident->branch->zona_geografica ?? 'Centro')
            ->where('categoria_id', $incident->categoria_id)
            ->get();

        if ($catalogItems->isEmpty()) {
            $catalogItems = UnitPriceCatalog::where('categoria_id', $incident->categoria_id)->get();
        }

        return view('incidents.show', compact('incident', 'fixers', 'catalogItems'));
    }

    /**
     * Avanzar paso en el ciclo de vida
     */
    public function advanceState(Request $request, Incident $incident)
    {
        $request->validate([
            'next_state' => 'required|string',
            'comentario' => 'nullable|string',
        ]);

        $user = Auth::user() ?? User::first();

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

        return redirect()->route('purchase-orders.show', $po)->with('success', "Orden de Compra {$po->folio_interno} generada exitosamente. Se ha actualizado el estado del ticket.");
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
            'tipo' => $request->tipo_documento, // e.g. REPSE, IMSS, XML_Factura, PDF_Factura
            'nombre' => $request->nombre_documento,
            'url' => $request->url_documento ?? '/storage/docs/' . uniqid() . '.pdf',
            'fecha_carga' => now()->toDateTimeString(),
            'subido_por' => $user->name,
        ];

        $docsActuales = $incident->documentos_fiscales ?? [];
        $docsActuales[] = $nuevoDoc;

        $this->lifecycleService->transitionTo($incident, 'proceso_administrativo', $user, "Carga de documento fiscal: {$request->nombre_documento}", [
            'documentos_fiscales' => [$nuevoDoc]
        ]);

        return redirect()->back()->with('success', "Documento '{$request->nombre_documento}' cargado exitosamente.");
    }
}
