<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class PurchaseOrder extends Model
{
    use HasFactory, HasUuids;

    public const ESTADOS = [
        'borrador' => 'Borrador',
        'emitida' => 'Emitida (Esperando Respuesta Cliente)',
        'aprobada' => 'Aprobada (Folio Cliente Registrado)',
        'en_ejecucion' => 'En Ejecución',
        'facturada' => 'Facturada',
        'cancelada' => 'Cancelada',
    ];

    protected $fillable = [
        'folio_interno',
        'folio_cliente',
        'incident_id',
        'supplier_id',
        'subtotal',
        'iva',
        'monto_total',
        'estado',
        'pdf_path',
        'notas',
        'fecha_emision',
        'fecha_aprobacion',
        'aprobado_por_user_id',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'iva' => 'decimal:2',
        'monto_total' => 'decimal:2',
        'fecha_emision' => 'datetime',
        'fecha_aprobacion' => 'datetime',
    ];

    public function incident()
    {
        return $this->belongsTo(Incident::class);
    }

    public function supplier()
    {
        return $this->belongsTo(User::class, 'supplier_id');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'aprobado_por_user_id');
    }

    public function items()
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    public function resolveRouteBinding($value, $field = null)
    {
        return $this->where('id', $value)
            ->orWhere('folio_interno', $value)
            ->orWhere('folio_cliente', $value)
            ->firstOrFail();
    }

    public static function generateFolioInterno(): string
    {
        $year = date('Y');
        $prefix = 'OC-INS-' . $year . '-';

        $folios = static::where('folio_interno', 'like', $prefix . '%')->pluck('folio_interno');

        $maxNumber = 0;
        foreach ($folios as $folio) {
            $numStr = str_replace($prefix, '', $folio);
            $numVal = (int) preg_replace('/[^0-9]/', '', $numStr);
            if ($numVal > $maxNumber) {
                $maxNumber = $numVal;
            }
        }

        $nextNumber = $maxNumber + 1;
        $candidate = $prefix . str_pad((string) $nextNumber, 5, '0', STR_PAD_LEFT);

        while (static::where('folio_interno', $candidate)->exists()) {
            $nextNumber++;
            $candidate = $prefix . str_pad((string) $nextNumber, 5, '0', STR_PAD_LEFT);
        }

        return $candidate;
    }
}
