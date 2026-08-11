<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\DemoRequestedMail;

Route::get('/', function () {
    return view('landing');
});

Route::post('/api/demo-request', function (Request $request) {
    $validated = $request->validate([
        'nombre' => 'required|string|max:255',
        'empresa' => 'required|string|max:255',
        'email' => 'required|email|max:255',
        'sucursales' => 'required|string|max:255',
    ]);

    $destinationEmail = env('DEMO_NOTIFICATION_EMAIL', 'blender.webservices@gmail.com');

    Mail::to($destinationEmail)->send(new DemoRequestedMail($validated));

    return response()->json([
        'success' => true,
        'message' => 'Solicitud enviada con éxito.'
    ]);
});
