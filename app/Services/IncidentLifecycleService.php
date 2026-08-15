<?php

namespace App\Services;

use App\Models\Incident;
use App\Models\IncidentLog;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;

class IncidentLifecycleService
{
    /**
     * Avanza el estado de la incidencia al siguiente paso dentro de los 10 definidos.
     */
    public function transitionTo(Incident $incident, string $nextState, User $user, ?string $comment = null, array $extraData = []): Incident
    {
        $currentState = $incident->estado;

        // Regla Crítica: Bloqueo de inicio de ejecución sin Orden de Compra válida
        if ($nextState === 'en_ejecucion') {
            if (!$incident->canStartExecution()) {
                throw new Exception("No es posible pasar a 'En Ejecución': La incidencia requiere contar con una Orden de Compra (OC) autorizada.");
            }
        }

        DB::transaction(function () use ($incident, $currentState, $nextState, $user, $comment, $extraData) {
            $incident->estado = $nextState;

            if (isset($extraData['diagnostico_texto'])) {
                $incident->diagnostico_texto = $extraData['diagnostico_texto'];
            }
            if (isset($extraData['propuesta_tecnica'])) {
                $incident->propuesta_tecnica = $extraData['propuesta_tecnica'];
            }
            if (isset($extraData['documentos_fiscales'])) {
                $incident->documentos_fiscales = array_merge($incident->documentos_fiscales ?? [], $extraData['documentos_fiscales']);
            }
            if ($nextState === 'cerrada') {
                $incident->fecha_resolucion = now();
            }

            $incident->save();

            // Registro de Auditoría
            IncidentLog::create([
                'incident_id' => $incident->id,
                'estado_anterior' => $currentState,
                'estado_nuevo' => $nextState,
                'usuario_id' => $user->id,
                'comentario' => $comment ?? "Transición de estado a {$nextState}",
                'fecha' => now(),
            ]);
        });

        return $incident->refresh();
    }

    /**
     * Emite una Orden de Compra (Paso 6) vinculada a la incidencia.
     */
    public function generatePurchaseOrder(Incident $incident, User $issuer, array $items, ?string $notas = null): PurchaseOrder
    {
        if ($incident->purchase_order_id && $incident->purchaseOrder) {
            return $incident->purchaseOrder;
        }

        return DB::transaction(function () use ($incident, $issuer, $items, $notas) {
            $subtotal = 0;
            foreach ($items as $item) {
                $subtotal += ($item['cantidad'] * $item['precio_unitario']);
            }
            $iva = $subtotal * 0.16;
            $total = $subtotal + $iva;

            $po = PurchaseOrder::create([
                'folio_interno' => PurchaseOrder::generateFolioInterno(),
                'folio_cliente' => null, // Pendiente de respuesta de cliente
                'incident_id' => $incident->id,
                'supplier_id' => $incident->fixer_id ?? $issuer->id,
                'subtotal' => $subtotal,
                'iva' => $iva,
                'monto_total' => $total,
                'estado' => 'emitida',
                'notas' => $notas,
                'fecha_emision' => now(),
            ]);

            foreach ($items as $item) {
                PurchaseOrderItem::create([
                    'purchase_order_id' => $po->id,
                    'unit_price_catalog_id' => $item['unit_price_catalog_id'] ?? null,
                    'codigo_concepto' => $item['codigo_concepto'],
                    'descripcion' => $item['descripcion'],
                    'unidad_medida' => $item['unidad_medida'] ?? 'servicio',
                    'cantidad' => $item['cantidad'],
                    'precio_unitario' => $item['precio_unitario'],
                    'subtotal' => $item['cantidad'] * $item['precio_unitario'],
                ]);
            }

            // Asociar la OC a la incidencia y cambiar estado a 'oc_emitida'
            $incident->purchase_order_id = $po->id;
            $incident->costo_mano_obra = $subtotal * 0.6; // Estimado proporcional
            $incident->costo_materiales = $subtotal * 0.4;
            $incident->estado = 'oc_emitida';
            $incident->save();

            IncidentLog::create([
                'incident_id' => $incident->id,
                'estado_anterior' => 'cotizacion_validada',
                'estado_nuevo' => 'oc_emitida',
                'usuario_id' => $issuer->id,
                'comentario' => "Orden de Compra emitida con folio interno {$po->folio_interno} por un total de $" . number_format($total, 2),
                'fecha' => now(),
            ]);

            return $po;
        });
    }

    /**
     * Registra el folio asignado por el cliente (ej. Waldo's) al responder la OC.
     */
    public function registerClientFolio(PurchaseOrder $purchaseOrder, string $folioCliente, User $approver): PurchaseOrder
    {
        return DB::transaction(function () use ($purchaseOrder, $folioCliente, $approver) {
            $purchaseOrder->folio_cliente = $folioCliente;
            $purchaseOrder->estado = 'aprobada';
            $purchaseOrder->fecha_aprobacion = now();
            $purchaseOrder->aprobado_por_user_id = $approver->id;
            $purchaseOrder->save();

            // Si la incidencia asociada estaba en oc_emitida, la habilitamos para avanzar a ejecución
            $incident = $purchaseOrder->incident;
            if ($incident && $incident->estado === 'oc_emitida') {
                IncidentLog::create([
                    'incident_id' => $incident->id,
                    'estado_anterior' => 'oc_emitida',
                    'estado_nuevo' => 'oc_emitida',
                    'usuario_id' => $approver->id,
                    'comentario' => "Folio de cliente asignado a OC: {$folioCliente}. Trabajo autorizado para ejecución.",
                    'fecha' => now(),
                ]);
            }

            return $purchaseOrder;
        });
    }
}
