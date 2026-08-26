<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Category;
use App\Models\Incident;
use App\Models\PurchaseOrder;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Dashboard general de reportes ejecutivos y métricas regionales
     */
    public function dashboard(Request $request)
    {
        $user = auth()->user();
        if ($user && !$user->canViewReports()) {
            return redirect()->route('incidents.index')->with('error', 'Acceso restringido: El rol Usuario solo puede registrar incidencias y no tiene acceso a reportes o dashboards.');
        }

        $allowedBranchIds = $user ? $user->assignedBranchIds() : null;

        $poQuery = PurchaseOrder::query();
        $incidentsQueryBase = Incident::query();

        if ($allowedBranchIds !== null) {
            $incidentsQueryBase->whereIn('branch_id', $allowedBranchIds);
            $poQuery->whereHas('incident', function ($q) use ($allowedBranchIds) {
                $q->whereIn('branch_id', $allowedBranchIds);
            });
        }

        // 1. Resumen Financiero y de Ruta de Emergencia
        $resumenFinanciero = [
            'total_oc_emitidas' => (clone $poQuery)->count(),
            'monto_comprometido' => (clone $poQuery)->whereIn('estado', ['emitida', 'aprobada', 'en_ejecucion'])->sum('monto_total'),
            'monto_facturado' => (clone $poQuery)->where('estado', 'facturada')->sum('monto_total'),
            'promedio_ticket' => (clone $poQuery)->avg('monto_total') ?? 0.00,
            'total_emergencias' => (clone $incidentsQueryBase)->where('es_emergencia', true)->count(),
            'cotizaciones_pendientes' => (clone $incidentsQueryBase)->whereIn('estado', ['cotizacion_propuesta', 'cotizacion_validada'])->count(),
        ];

        // 2. Reporte por Zonas Geográficas (Las 9-10 zonas de Waldo's)
        $zonasReporte = [];
        foreach (Branch::ZONAS_GEOGRAFICAS as $zona) {
            $branchQuery = Branch::where('zona_geografica', $zona);
            if ($allowedBranchIds !== null) {
                $branchQuery->whereIn('id', $allowedBranchIds);
            }
            $branchIdsInZone = $branchQuery->pluck('id');
            $totalSucursales = $branchIdsInZone->count();

            $incidentsInZone = Incident::whereIn('branch_id', $branchIdsInZone);

            $totalIncidencias = (clone $incidentsInZone)->count();
            $cerradas = (clone $incidentsInZone)->where('estado', 'cerrada')->count();
            $enProceso = (clone $incidentsInZone)->whereNotIn('estado', ['registrada', 'cerrada'])->count();
            $emergencias = (clone $incidentsInZone)->where('es_emergencia', true)->count();

            $montoInvertido = PurchaseOrder::whereHas('incident', function ($q) use ($branchIdsInZone) {
                $q->whereIn('branch_id', $branchIdsInZone);
            })->sum('monto_total');

            $zonasReporte[] = [
                'zona' => $zona,
                'total_sucursales' => $totalSucursales,
                'total_incidencias' => $totalIncidencias,
                'en_proceso' => $enProceso,
                'cerradas' => $cerradas,
                'emergencias' => $emergencias,
                'monto_invertido' => $montoInvertido,
            ];
        }

        // 3. Reporte por Disciplina / Categoría
        $categoriasReporte = Category::all()->map(function ($cat) use ($allowedBranchIds) {
            $incQuery = Incident::where('categoria_id', $cat->id);
            if ($allowedBranchIds !== null) {
                $incQuery->whereIn('branch_id', $allowedBranchIds);
            }
            $totalTickets = $incQuery->count();

            $poCatQuery = PurchaseOrder::whereHas('incident', function ($q) use ($cat, $allowedBranchIds) {
                $q->where('categoria_id', $cat->id);
                if ($allowedBranchIds !== null) {
                    $q->whereIn('branch_id', $allowedBranchIds);
                }
            });
            $monto = $poCatQuery->sum('monto_total');

            return [
                'nombre' => $cat->nombre,
                'total_tickets' => $totalTickets,
                'monto_total' => $monto,
            ];
        });

        // 4. Reporte por Estado del Ciclo de Vida (10 Pasos)
        $incidenciasPorPaso = [];
        foreach (Incident::LIFECYCLE_STEPS as $num => $step) {
            $stepQuery = (clone $incidentsQueryBase)->where('estado', $step['key']);
            $incidenciasPorPaso[] = [
                'paso' => $num,
                'nombre' => $step['name'],
                'key' => $step['key'],
                'role' => $step['role'],
                'rol' => $step['role'],
                'total' => $stepQuery->count(),
            ];
        }

        // 5. Estado de Órdenes de Compra
        $ocPorEstado = (clone $poQuery)
            ->select('estado', DB::raw('count(*) as count'), DB::raw('sum(monto_total) as total'))
            ->groupBy('estado')
            ->get();

        return view('reports.dashboard', compact(
            'resumenFinanciero',
            'zonasReporte',
            'categoriasReporte',
            'incidenciasPorPaso',
            'ocPorEstado'
        ));
    }
}
