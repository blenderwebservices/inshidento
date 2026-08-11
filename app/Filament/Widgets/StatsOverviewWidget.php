<?php

namespace App\Filament\Widgets;

use App\Models\Incident;
use App\Models\BillingReport;
use App\Models\Company;
use App\Models\Branch;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $abiertasCount = Incident::where('estado', 'abierta')->count();
        $enProgresoCount = Incident::where('estado', 'en_progreso')->count();
        $resueltasPendientesFactura = Incident::where('estado', 'resuelta')->whereNull('billing_report_id')->count();
        $montoMantenimientoTotal = Incident::where('estado', 'resuelta')->sum('costo_mano_obra') + Incident::where('estado', 'resuelta')->sum('costo_materiales');

        return [
            Stat::make('Incidencias Abiertas', $abiertasCount)
                ->description('Pendientes de triaje y asignación')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color('warning'),

            Stat::make('En Progreso (Campo)', $enProgresoCount)
                ->description('Técnicos trabajando en sucursal')
                ->descriptionIcon('heroicon-m-wrench')
                ->color('info'),

            Stat::make('Resueltas sin Facturar', $resueltasPendientesFactura)
                ->description('Listas para integrar a cobro')
                ->descriptionIcon('heroicon-m-document-check')
                ->color('primary'),

            Stat::make('Monto Total Reparaciones', '$' . number_format($montoMantenimientoTotal, 2))
                ->description('Costo acumulado mano de obra + insumos')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),
        ];
    }
}
