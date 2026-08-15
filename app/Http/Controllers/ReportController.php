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
        // 1. Resumen Financiero y de Ruta de Emergencia
        $resumenFinanciero = [
            'total_oc_emitidas' => PurchaseOrder::count(),
            'monto_comprometido' => PurchaseOrder::whereIn('estado', ['emitida', 'aprobada', 'en_ejecucion'])->sum('monto_total'),
            'monto_facturado' => PurchaseOrder::where('estado', 'facturada')->sum('monto_total'),
            'promedio_ticket' => PurchaseOrder::avg('monto_total') ?? 0.00,
            'total_emergencias' => Incident::where('es_emergencia', true)->count(),
            'cotizaciones_pendientes' => Incident::whereIn('estado', ['cotizacion_propuesta', 'cotizacion_validada'])->count(),
        ];

        // 2. Reporte por Zonas Geográficas (Las 9-10 zonas de Waldo's)
        $zonasReporte = [];
        foreach (Branch::ZONAS_GEOGRAFICAS as $zona) {
            $branchIds = Branch::where('zona_geografica', $zona)->pluck('id');
            $incidentsQuery = Incident::whereIn('branch_id', $branchIds);

            $totalIncidencias = (clone $incidentsQuery)->count();
            $cerradas = (clone $incidentsQuery)->where('estado', 'cerrada')->count();
            $enProceso = (clone $incidentsQuery)->whereNotIn('estado', ['registrada', 'cerrada'])->count();
            $emergencias = (clone $incidentsQuery)->where('es_emergencia', true)->count();

            $montoInvertido = PurchaseOrder::whereHas('incident', function ($q) use ($branchIds) {
                $q->whereIn('branch_id', $branchIds);
            })->sum('monto_total');

            $zonasReporte[] = [
                'zona' => $zona,
                'total_sucursales' => Branch::where('zona_geografica', $zona)->count(),
                'total_incidencias' => $totalIncidencias,
                'en_proceso' => $enProceso,
                'cerradas' => $cerradas,
                'emergencias' => $emergencias,
                'monto_invertido' => $montoInvertido,
            ];
        }

        // 3. Reporte por Disciplina / Categoría
        $categoriasReporte = Category::withCount('incidents')->get()->map(function ($cat) {
            $monto = PurchaseOrder::whereHas('incident', function ($q) use ($cat) {
                $q->where('categoria_id', $cat->id);
            })->sum('monto_total');

            return [
                'nombre' => $cat->nombre,
                'total_tickets' => $cat->incidents_count,
                'monto_total' => $monto,
            ];
        });

        // 4. Reporte por Estado del Ciclo de Vida (10 Pasos)
        $incidenciasPorPaso = [];
        foreach (Incident::LIFECYCLE_STEPS as $num => $step) {
            $incidenciasPorPaso[] = [
                'paso' => $num,
                'nombre' => $step['name'],
                'key' => $step['key'],
                'role' => $step['role'],
                'rol' => $step['role'],
                'total' => Incident::where('estado', $step['key'])->count(),
            ];
        }

        // 5. Estado de Órdenes de Compra
        $ocPorEstado = PurchaseOrder::select('estado', DB::raw('count(*) as count'), DB::raw('sum(monto_total) as total'))
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
