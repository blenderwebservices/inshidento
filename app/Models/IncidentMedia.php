<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class IncidentMedia extends Model
{
    use HasFactory, HasUuids;

    public $timestamps = false;

    protected $table = 'incident_media';

    protected $fillable = [
        'incident_id',
        'url_archivo',
        'tipo',
        'origen',
        'fecha_carga',
    ];

    protected $casts = [
        'fecha_carga' => 'datetime',
    ];

    public function incident()
    {
        return $this->belongsTo(Incident::class);
    }
}
