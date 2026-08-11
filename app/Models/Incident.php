<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Incident extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'codigo_ticket',
        'branch_id',
        'titulo',
        'descripcion',
        'categoria_id',
        'prioridad',
        'estado',
        'ubicacion_especifica',
        'latitud',
        'longitud',
        'notifier_id',
        'manager_id',
        'fixer_id',
        'billing_report_id',
        'costo_mano_obra',
        'costo_materiales',
        'fecha_resolucion',
    ];

    protected $casts = [
        'costo_mano_obra' => 'decimal:2',
        'costo_materiales' => 'decimal:2',
        'fecha_resolucion' => 'datetime',
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

    public function media()
    {
        return $this->hasMany(IncidentMedia::class);
    }

    public function logs()
    {
        return $this->hasMany(IncidentLog::class);
    }

    public static function generateTicketCode(): string
    {
        return 'INC-' . strtoupper(substr(uniqid(), -6));
    }
}
