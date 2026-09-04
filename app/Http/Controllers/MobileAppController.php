<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Category;
use App\Models\Incident;
use App\Models\IncidentMedia;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MobileAppController extends Controller
{
    /**
     * GET /api/app/users
     * Devuelve usuarios con rol user/notifier/store_user con sucursal y empresa.
     */
    public function users()
    {
        $users = User::with(['branch.company'])
            ->whereIn('rol', ['user', 'notifier', 'store_user'])
            ->orderBy('name')
            ->get()
            ->map(fn($u) => [
                'id'       => $u->id,
                'name'     => $u->name,
                'email'    => $u->email,
                'rol'      => $u->rol,
                'branch'   => $u->branch ? [
                    'id'     => $u->branch->id,
                    'nombre' => $u->branch->nombre,
                    'codigo' => $u->branch->codigo_sucursal,
                    'zona'   => $u->branch->zona_geografica,
                ] : null,
                'company'  => $u->branch && $u->branch->company ? [
                    'id'     => $u->branch->company->id,
                    'nombre' => $u->branch->company->nombre,
                ] : null,
                'branch_id' => $u->branch_id,
            ]);

        return response()->json($users);
    }

    /**
     * GET /api/app/branches
     * Devuelve todas las sucursales con empresa.
     */
    public function branches()
    {
        $branches = Branch::with('company')
            ->orderBy('nombre')
            ->get()
            ->map(fn($b) => [
                'id'      => $b->id,
                'nombre'  => $b->nombre,
                'codigo'  => $b->codigo_sucursal,
                'zona'    => $b->zona_geografica,
                'company' => $b->company ? [
                    'id'     => $b->company->id,
                    'nombre' => $b->company->nombre,
                ] : null,
            ]);

        return response()->json($branches);
    }

    /**
     * GET /api/app/categories
     * Devuelve todas las categorías.
     */
    public function categories()
    {
        $cats = Category::orderBy('nombre')->get(['id', 'nombre']);
        return response()->json($cats);
    }

    /**
     * POST /api/app/incidents
     * Crea una incidencia real en BD + sube media (hasta 10 imágenes, audio, video ≤60s).
     */
    public function store(Request $request)
    {
        $request->validate([
            'notifier_id'          => 'required|exists:users,id',
            'branch_id'            => 'required|exists:branches,id',
            'titulo'               => 'required|string|max:255',
            'descripcion'          => 'nullable|string',
            'categoria_id'         => 'required|exists:categories,id',
            'prioridad'            => 'required|in:baja,media,alta,critica',
            'es_emergencia'        => 'nullable|boolean',
            'motivo_emergencia'    => 'nullable|string|max:500',
            'ubicacion_especifica' => 'nullable|string|max:255',

            // Media: hasta 10 imágenes, 1 audio, 1 video
            'imagenes'             => 'nullable|array|max:10',
            'imagenes.*'           => 'file|mimes:jpg,jpeg,png,webp,gif,heic|max:15360',
            'audio'                => 'nullable|file|mimes:webm,ogg,mp3,wav,m4a|max:20480',
            'video'                => 'nullable|file|mimes:mp4,webm,mov|max:51200',
        ]);

        $notifier    = User::findOrFail($request->notifier_id);
        $isEmergency = (bool) $request->es_emergencia;

        // Crear la incidencia
        $incident = Incident::create([
            'codigo_ticket'        => Incident::generateTicketCode(),
            'branch_id'            => $request->branch_id,
            'titulo'               => $request->titulo,
            'descripcion'          => $request->descripcion ?? 'Reportada desde App Móvil',
            'categoria_id'         => $request->categoria_id,
            'prioridad'            => $isEmergency ? 'critica' : $request->prioridad,
            'es_emergencia'        => $isEmergency,
            'motivo_emergencia'    => $request->motivo_emergencia,
            'estado'               => 'registrada',
            'ubicacion_especifica' => $request->ubicacion_especifica,
            'notifier_id'          => $notifier->id,
        ]);

        // Procesar imágenes (hasta 10)
        if ($request->hasFile('imagenes')) {
            foreach ($request->file('imagenes') as $img) {
                $path = $img->store('media/incidents/' . $incident->id, 'public');
                IncidentMedia::create([
                    'incident_id' => $incident->id,
                    'origen'      => 'upload',
                    'plataforma'  => 'Inshidento App Móvil',
                    'titulo'      => $img->getClientOriginalName(),
                    'url_archivo' => '/storage/' . $path,
                    'tipo'        => 'image',
                    'peso_bytes'  => $img->getSize(),
                    'fecha_carga' => now(),
                ]);
            }
        }

        // Procesar audio grabado
        if ($request->hasFile('audio')) {
            $audio = $request->file('audio');
            $path  = $audio->store('media/incidents/' . $incident->id, 'public');
            IncidentMedia::create([
                'incident_id'       => $incident->id,
                'origen'            => 'upload',
                'plataforma'        => 'Inshidento App Móvil — Audio',
                'titulo'            => 'Audio_' . now()->format('Ymd_His') . '.' . $audio->getClientOriginalExtension(),
                'url_archivo'       => '/storage/' . $path,
                'tipo'              => 'audio',
                'peso_bytes'        => $audio->getSize(),
                'fecha_carga'       => now(),
            ]);
        }

        // Procesar video (≤60 segundos validado en cliente; aquí también guardamos)
        if ($request->hasFile('video')) {
            $video = $request->file('video');
            $path  = $video->store('media/incidents/' . $incident->id, 'public');
            IncidentMedia::create([
                'incident_id'       => $incident->id,
                'origen'            => 'upload',
                'plataforma'        => 'Inshidento App Móvil — Video',
                'titulo'            => 'Video_' . now()->format('Ymd_His') . '.' . $video->getClientOriginalExtension(),
                'url_archivo'       => '/storage/' . $path,
                'tipo'              => 'video',
                'duracion_segundos' => (int) $request->input('video_duracion', 0),
                'peso_bytes'        => $video->getSize(),
                'fecha_carga'       => now(),
            ]);
        }

        // Recargar con relaciones para la respuesta
        $incident->load(['branch.company', 'notifier', 'category', 'media']);

        return response()->json([
            'success'       => true,
            'codigo_ticket' => $incident->codigo_ticket,
            'id'            => $incident->id,
            'message'       => "Incidencia {$incident->codigo_ticket} registrada exitosamente.",
            'media_count'   => $incident->media->count(),
        ], 201);
    }

    /**
     * GET /api/app/incidents
     * Historial de incidencias: quién, cuándo, qué, sucursal y empresa.
     */
    public function history(Request $request)
    {
        $query = Incident::with(['notifier.branch.company', 'branch.company', 'category', 'media'])
            ->latest()
            ->limit(60);

        // Filtro por sucursal (obligatorio si se pasa, para limitar al scope del usuario)
        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        // Filtro opcional adicional por usuario notificador
        if ($request->filled('notifier_id')) {
            $query->where('notifier_id', $request->notifier_id);
        }


        $incidents = $query->get()->map(fn($inc) => [
            'id'            => $inc->id,
            'codigo_ticket' => $inc->codigo_ticket,
            'titulo'        => $inc->titulo,
            'descripcion'   => $inc->descripcion,
            'estado'        => $inc->estado,
            'prioridad'     => $inc->prioridad,
            'es_emergencia' => $inc->es_emergencia,
            'created_at'    => $inc->created_at?->toIso8601String(),
            'created_human' => $inc->created_at?->diffForHumans(),
            'categoria'     => $inc->category?->nombre,
            'media_count'   => $inc->media->count(),
            'media_tipos'   => $inc->media->pluck('tipo')->unique()->values(),
            'notifier'      => $inc->notifier ? [
                'id'     => $inc->notifier->id,
                'name'   => $inc->notifier->name,
                'email'  => $inc->notifier->email,
            ] : null,
            'branch'  => $inc->branch ? [
                'id'     => $inc->branch->id,
                'nombre' => $inc->branch->nombre,
                'codigo' => $inc->branch->codigo_sucursal,
                'zona'   => $inc->branch->zona_geografica,
            ] : null,
            'company' => $inc->branch?->company ? [
                'id'     => $inc->branch->company->id,
                'nombre' => $inc->branch->company->nombre,
            ] : null,
        ]);

        return response()->json($incidents);
    }
}
