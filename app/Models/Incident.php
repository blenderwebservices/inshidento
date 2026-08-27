<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Incident extends Model
{
    use HasFactory, HasUuids;

    // Mapa completo del Ciclo de Vida de 10 Pasos
    public const LIFECYCLE_STEPS = [
        1 => ['key' => 'registrada', 'name' => '1. Registro Inicial', 'role' => 'Notificador'],
        2 => ['key' => 'proveedor_asignado', 'name' => '2. Asignación de Proveedor', 'role' => 'Gestor'],
        3 => ['key' => 'diagnostico_cargado', 'name' => '3. Levantamiento y Diagnóstico', 'role' => 'Proveedor'],
        4 => ['key' => 'cotizacion_propuesta', 'name' => '4. Propuesta Técnica/Económica', 'role' => 'Proveedor'],
        5 => ['key' => 'cotizacion_validada', 'name' => '5. Validación de Presupuesto', 'role' => 'Waldo\'s / FM'],
        6 => ['key' => 'oc_emitida', 'name' => '6. Emisión de Orden de Compra', 'role' => 'Sistema / Admin'],
        7 => ['key' => 'en_ejecucion', 'name' => '7. Ejecución y Generadores', 'role' => 'Proveedor'],
        8 => ['key' => 'entrega_validada', 'name' => '8. Validación de Entrega', 'role' => 'Waldo\'s / FM'],
        9 => ['key' => 'proceso_administrativo', 'name' => '9. Proceso Admin y Facturación', 'role' => 'Finanzas / Admin'],
        10 => ['key' => 'cerrada', 'name' => '10. Cierre de Ticket', 'role' => 'Sistema'],
    ];

    protected $fillable = [
        'codigo_ticket',
        'branch_id',
        'titulo',
        'descripcion',
        'categoria_id',
        'prioridad',
        'es_emergencia',
        'motivo_emergencia',
        'estado',
        'ubicacion_especifica',
        'latitud',
        'longitud',
        'notifier_id',
        'manager_id',
        'fixer_id',
        'billing_report_id',
        'purchase_order_id',
        'diagnostico_texto',
        'propuesta_tecnica',
        'documentos_fiscales',
        'costo_mano_obra',
        'costo_materiales',
        'fecha_resolucion',
    ];

    protected $casts = [
        'es_emergencia' => 'boolean',
        'costo_mano_obra' => 'decimal:2',
        'costo_materiales' => 'decimal:2',
        'fecha_resolucion' => 'datetime',
        'documentos_fiscales' => 'array',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'categoria_id');
    }

    public function notifier()
    {
        return $this->belongsTo(User::class, 'notifier_id');
    }

    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function fixer()
    {
        return $this->belongsTo(User::class, 'fixer_id');
    }

    public function billingReport()
    {
        return $this->belongsTo(BillingReport::class);
    }

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class, 'purchase_order_id');
    }

    public function media()
    {
        return $this->hasMany(IncidentMedia::class);
    }

    public function logs()
    {
        return $this->hasMany(IncidentLog::class);
    }

    public function getCurrentStepNumber(): int
    {
        foreach (static::LIFECYCLE_STEPS as $stepNum => $stepData) {
            if ($stepData['key'] === $this->estado) {
                return $stepNum;
            }
        }
        return 1;
    }

    public function canStartExecution(): bool
    {
        // En Ruta Alterna de Emergencia Crítica, se permite inicio inmediato de ejecución sin OC previa
        if ($this->es_emergencia) {
            return true;
        }

        // La ejecución regular exige la existencia de una Orden de Compra aprobada/emitida con folio
        if (!$this->purchase_order_id) {
            return false;
        }

        $po = $this->purchaseOrder;
        if (!$po) {
            return false;
        }

        return in_array($po->estado, ['emitida', 'aprobada', 'en_ejecucion']);
    }

    /**
     * Determina si la incidencia fue ejecutada por Bypass de Emergencia y requiere
     * proceso de regularización administrativa posterior por parte del FM.
     */
    public function isPendingFmRegularization(): bool
    {
        return $this->es_emergencia && !$this->purchase_order_id;
    }

    /**
     * Determina si una incidencia de emergencia crítica ya fue regularizada administrativamente con su OC.
     */
    public function isRegularized(): bool
    {
        return $this->es_emergencia && (bool) $this->purchase_order_id;
    }

    public function resolveRouteBinding($value, $field = null)
    {
        return $this->where('id', $value)
            ->orWhere('codigo_ticket', $value)
            ->firstOrFail();
    }

    public static function generateTicketCode(): string
    {
        return 'INC-' . strtoupper(substr(uniqid(), -6));
    }
}
