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
        'origen', // 'upload' or 'external_link'
        'plataforma', // 'Google Drive', 'Microsoft 365 / OneDrive', 'Dropbox', 'Otro'
        'titulo',
        'url_archivo',
        'tipo', // 'image', 'video', 'document', 'audio', 'external_link'
        'duracion_segundos',
        'peso_bytes',
        'fecha_carga',
    ];

    protected $casts = [
        'fecha_carga' => 'datetime',
        'duracion_segundos' => 'integer',
        'peso_bytes' => 'integer',
    ];

    public function incident()
    {
        return $this->belongsTo(Incident::class);
    }

    public function isExternalLink(): bool
    {
        return $this->origen === 'external_link' || filter_var($this->url_archivo, FILTER_VALIDATE_URL) && !str_contains($this->url_archivo, '/storage/');
    }

    public function getFormattedSizeAttribute(): string
    {
        if (!$this->peso_bytes) return 'N/A';
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($this->peso_bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        return round($bytes, 2) . ' ' . $units[$pow];
    }
}
