<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ImpersonationController extends Controller
{
    public function impersonate(Request $request, string $userId)
    {
        $currentUser = Auth::user();

        // Solo permitir que un Super Admin o alguien que tenga rol 'admin' inicie impersonalización
        if (!$currentUser || $currentUser->rol !== 'admin') {
            abort(403, 'No tienes permisos de Super Admin para realizar esta acción.');
        }

        $targetUser = User::findOrFail($userId);

        // Guardar el ID del Super Admin original en la sesión si aún no existe
        if (!session()->has('impersonator_id')) {
            session(['impersonator_id' => $currentUser->id]);
        }

        // Login como el usuario destino
        Auth::login($targetUser);

        return redirect()->to('/admin')->with('status', 'Impersonalizando a ' . $targetUser->name);
    }

    public function leave(Request $request)
    {
        if (session()->has('impersonator_id')) {
            $adminUser = User::find(session('impersonator_id'));
            
            if ($adminUser) {
                Auth::login($adminUser);
            }
            
            session()->forget('impersonator_id');
        }

        return redirect()->to('/admin')->with('status', 'Has regresado a tu cuenta de Super Admin.');
    }
}
