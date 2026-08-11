<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class BillingReport extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'company_id',
        'fixer_id',
        'folio_factura',
        'tipo_fixer',
        'total_incidencias',
        'monto_total',
        'estado',
        'fecha_cierre',
    ];

    protected $casts = [
        'total_incidencias' => 'integer',
        'monto_total' => 'decimal:2',
        'fecha_cierre' => 'datetime',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function fixer()
    {
        return $this->belongsTo(User::class, 'fixer_id');
    }

    public function incidents()
    {
        return $this->hasMany(Incident::class);
    }

    public function recalculateTotals()
    {
        $incidents = $this->incidents;
        $this->total_incidencias = $incidents->count();
        $this->monto_total = $incidents->sum(function ($inc) {
            return $inc->costo_mano_obra + $inc->costo_materiales;
        });
        $this->save();
    }
}
