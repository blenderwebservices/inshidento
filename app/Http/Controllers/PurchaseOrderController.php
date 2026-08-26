<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use App\Models\Incident;
use App\Services\IncidentLifecycleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PurchaseOrderController extends Controller
{
    protected IncidentLifecycleService $lifecycleService;

    public function __construct(IncidentLifecycleService $lifecycleService)
    {
        $this->lifecycleService = $lifecycleService;
    }

    /**
     * Módulo principal de Órdenes de Compra (OCs)
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        if ($user && !$user->canViewPurchaseOrders()) {
            return redirect()->route('incidents.index')->with('error', 'Acceso restringido: Tu rol no tiene permisos para ver Órdenes de Compra.');
        }

        $query = PurchaseOrder::with(['incident.branch', 'supplier', 'approvedBy']);

        $allowedBranchIds = $user ? $user->assignedBranchIds() : null;
        if ($allowedBranchIds !== null) {
            $query->whereHas('incident', function ($q) use ($allowedBranchIds) {
                $q->whereIn('branch_id', $allowedBranchIds);
            });
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('folio_interno', 'like', "%{$search}%")
                  ->orWhere('folio_cliente', 'like', "%{$search}%");
            });
        }

        $purchaseOrders = $query->latest()->paginate(15);

        $metrics = [
            'total_oc' => PurchaseOrder::count(),
            'monto_total_comprometido' => PurchaseOrder::whereIn('estado', ['emitida', 'aprobada', 'en_ejecucion'])->sum('monto_total'),
            'monto_facturado' => PurchaseOrder::where('estado', 'facturada')->sum('monto_total'),
            'pendientes_folio_cliente' => PurchaseOrder::whereNull('folio_cliente')->where('estado', 'emitida')->count(),
        ];

        return view('purchase_orders.index', compact('purchaseOrders', 'metrics'));
    }

    /**
     * Ver detalle de la Orden de Compra y plantilla oficial
     */
    public function show(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load(['incident.branch', 'supplier', 'items.catalogItem', 'approvedBy']);
        return view('purchase_orders.show', compact('purchaseOrder'));
    }

    /**
     * Registrar respuesta del cliente asignando el folio de cliente (ej. Waldo's)
     */
    public function registerClientFolio(Request $request, PurchaseOrder $purchaseOrder)
    {
        $user = Auth::user() ?? \App\Models\User::first();
        if ($user && !$user->canManagePurchaseOrders()) {
            return redirect()->back()->with('error', 'Acceso denegado: El rol Stakeholder / Usuario no puede autorizar Órdenes de Compra.');
        }

        $request->validate([
            'folio_cliente' => 'required|string|max:100',
        ]);

        $this->lifecycleService->registerClientFolio($purchaseOrder, $request->folio_cliente, $user);

        return redirect()->back()->with('success', "Folio del cliente registrado exitosamente ({$request->folio_cliente}). La Orden de Compra ha sido autorizada.");
    }
}
