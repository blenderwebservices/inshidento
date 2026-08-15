<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnitPriceCatalog extends Model
{
    use HasFactory;

    protected $fillable = [
        'codigo_concepto',
        'zona_geografica',
        'categoria_id',
        'descripcion',
        'unidad_medida',
        'precio_unitario',
    ];

    protected $casts = [
        'precio_unitario' => 'decimal:2',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'categoria_id');
    }
}
