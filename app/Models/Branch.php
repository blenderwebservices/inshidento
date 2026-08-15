<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Branch extends Model
{
    use HasFactory, HasUuids;

    public const ZONAS_GEOGRAFICAS = [
        'Noreste',
        'Bajío',
        'Noroeste',
        'Peninsular',
        'Metro Norte',
        'Metro Sur',
        'Occidente',
        'Sur',
        'Norte',
        'Centro',
    ];

    protected $fillable = [
        'company_id',
        'zona_geografica',
        'nombre',
        'codigo_sucursal',
        'direccion',
        'latitud',
        'longitud',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function incidents()
    {
        return $this->hasMany(Incident::class);
    }
}
