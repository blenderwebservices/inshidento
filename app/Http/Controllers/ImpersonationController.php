<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ImpersonationController extends Controller
{
    public function impersonate(Request $request, string $userId)
    {
        $currentUser = Auth::user() ?? User::where('rol', 'admin')->first();

        if (!$currentUser || (!$currentUser->isAdmin() && !session()->has('impersonator_id'))) {
            return redirect()->back()->with('error', 'No tienes permisos de Admin para realizar esta acción.');
        }

        $targetUser = User::findOrFail($userId);

        if (!session()->has('impersonator_id')) {
            $adminId = $currentUser->isAdmin() ? $currentUser->id : User::where('rol', 'admin')->first()->id;
            session(['impersonator_id' => $adminId]);
        }

        Auth::login($targetUser);

        $targetRoute = ($targetUser->rol === 'user') ? route('incidents.index') : route('reports.dashboard');

        return redirect($targetRoute)->with('success', "⚠️ Modo Impersonalización activo: Viendo la plataforma como {$targetUser->name} ({$targetUser->rol}).");
    }

    public function leave(Request $request)
    {
        if (session()->has('impersonator_id')) {
            $adminUser = User::find(session('impersonator_id'));
            if ($adminUser) {
                Auth::login($adminUser);
            }
            session()->forget('impersonator_id');
        } else {
            $adminUser = User::where('rol', 'admin')->first();
            if ($adminUser) {
                Auth::login($adminUser);
            }
        }

        return redirect()->route('reports.dashboard')->with('success', 'Has regresado exitosamente a tu cuenta de Administrador General.');
    }
}
