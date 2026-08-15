<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\Category;
use App\Models\Company;
use App\Models\Incident;
use App\Models\PurchaseOrder;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PurchaseOrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_generate_folio_interno_guarantees_uniqueness(): void
    {
        $company = Company::create(['nombre' => 'Waldo\'s Unique PO', 'rfc_tax_id' => 'WDM555555555']);
        $branch = Branch::create(['company_id' => $company->id, 'nombre' => 'Sucursal Peninsular', 'zona_geografica' => 'Peninsular']);
        $category = Category::create(['nombre' => 'Eléctrica']);
        $user = User::create(['name' => 'FM User', 'email' => 'fm_po@test.com', 'password' => 'password', 'rol' => 'manager']);

        $incident = Incident::create([
            'codigo_ticket' => 'INC-PO-01',
            'branch_id' => $branch->id,
            'titulo' => 'Falla en panel',
            'descripcion' => 'Descripción',
            'categoria_id' => $category->id,
            'prioridad' => 'alta',
            'estado' => 'cotizacion_validada',
            'notifier_id' => $user->id,
        ]);

        // Crear manualmente una OC con el folio OC-INS-2026-00001
        PurchaseOrder::create([
            'folio_interno' => 'OC-INS-' . date('Y') . '-00001',
            'incident_id' => $incident->id,
            'supplier_id' => $user->id,
            'subtotal' => 100,
            'iva' => 16,
            'monto_total' => 116,
            'estado' => 'emitida',
            'fecha_emision' => now(),
        ]);

        // El generador debe retornar OC-INS-2026-00002 sin causar un UniqueConstraintViolationException
        $nextFolio = PurchaseOrder::generateFolioInterno();
        $this->assertEquals('OC-INS-' . date('Y') . '-00002', $nextFolio);

        // Si creamos OC-INS-2026-00002, el siguiente debe ser OC-INS-2026-00003
        PurchaseOrder::create([
            'folio_interno' => $nextFolio,
            'incident_id' => $incident->id,
            'supplier_id' => $user->id,
            'subtotal' => 200,
            'iva' => 32,
            'monto_total' => 232,
            'estado' => 'emitida',
            'fecha_emision' => now(),
        ]);

        $thirdFolio = PurchaseOrder::generateFolioInterno();
        $this->assertEquals('OC-INS-' . date('Y') . '-00003', $thirdFolio);
    }
}
