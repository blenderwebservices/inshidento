<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleSwitchController extends Controller
{
    public function switchRole(string $role)
    {
        $currentUser = Auth::user();

        if ($role === 'admin') {
            // Regresar a Admin General
            if (session()->has('impersonator_id')) {
                $adminUser = User::find(session('impersonator_id'));
                if ($adminUser) {
                    Auth::login($adminUser);
                }
                session()->forget('impersonator_id');
            } else {
                $adminUser = User::where('email', 'admin@waldos.com')->first() 
                    ?? User::where('rol', 'admin')->first();
                if ($adminUser) {
                    Auth::login($adminUser);
                }
            }

            return redirect()->route('reports.dashboard')->with('success', 'Regresaste a tu cuenta de Administrador General (Acceso Total).');
        }

        // Si el usuario actual es Admin o ya está impersonalizando
        if ($currentUser && ($currentUser->isAdmin() || session()->has('impersonator_id'))) {
            if (!session()->has('impersonator_id')) {
                session(['impersonator_id' => $currentUser->id]);
            }
        }

        $emailMap = [
            'fm' => 'fm@waldos.com',
            'stakeholder' => 'stakeholder@waldos.com',
            'user' => 'user@waldos.com',
        ];

        if (!array_key_exists($role, $emailMap)) {
            return redirect()->back()->with('error', 'Rol inválido.');
        }

        $targetUser = User::where('email', $emailMap[$role])->first() 
            ?? User::where('rol', $role)->first();

        if (!$targetUser) {
            return redirect()->back()->with('error', "No se encontró un usuario para el rol '{$role}'.");
        }

        Auth::login($targetUser);

        $roleNames = [
            'fm' => 'Facility Manager (FM)',
            'stakeholder' => 'Stakeholder (Sólo Lectura)',
            'user' => 'Usuario Notificador de Tienda',
        ];

        $targetRoute = ($targetUser->rol === 'user') ? route('incidents.index') : route('reports.dashboard');

        return redirect($targetRoute)->with('success', "⚠️ Impersonalizando como {$roleNames[$role]} ({$targetUser->name}). Estás viendo la plataforma tal como la vería este rol.");
    }
}
