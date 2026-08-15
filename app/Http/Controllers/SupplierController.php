<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    /**
     * Catálogo / Menú de Proveedores filtrables por Ubicación Geográfica
     */
    public function index(Request $request)
    {
        $query = User::where('rol', 'fixer')->withCount(['incidentsAsFixer as total_tickets']);

        if ($request->filled('zona')) {
            $query->where(function ($q) use ($request) {
                $q->where('zona_cobertura', $request->zona)
                  ->orWhereNull('zona_cobertura');
            });
        }

        if ($request->filled('especialidad')) {
            $query->where('especialidad', 'like', "%{$request->especialidad}%");
        }

        $suppliers = $query->paginate(12);
        $zonas = Branch::ZONAS_GEOGRAFICAS;
        $categories = Category::all();

        return view('suppliers.index', compact('suppliers', 'zonas', 'categories'));
    }
}
