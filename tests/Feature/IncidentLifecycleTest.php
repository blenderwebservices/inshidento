<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\Category;
use App\Models\Company;
use App\Models\Incident;
use App\Models\PurchaseOrder;
use App\Models\User;
use App\Services\IncidentLifecycleService;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IncidentLifecycleTest extends TestCase
{
    use RefreshDatabase;

    protected IncidentLifecycleService $lifecycleService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->lifecycleService = new IncidentLifecycleService();
    }

    public function test_can_create_incident_and_advance_steps(): void
    {
        $company = Company::create(['nombre' => 'Waldo\'s Demo', 'rfc_tax_id' => 'WDM123456789']);
        $branch = Branch::create(['company_id' => $company->id, 'nombre' => 'Sucursal Bajío 1', 'zona_geografica' => 'Bajío']);
        $category = Category::create(['nombre' => 'Eléctrica']);
        $user = User::create(['name' => 'FM User', 'email' => 'fm@test.com', 'password' => 'password', 'rol' => 'manager']);

        $incident = Incident::create([
            'codigo_ticket' => 'INC-TEST-001',
            'branch_id' => $branch->id,
            'titulo' => 'Falla de transformador',
            'descripcion' => 'Descripción corta',
            'categoria_id' => $category->id,
            'prioridad' => 'alta',
            'estado' => 'registrada',
            'notifier_id' => $user->id,
        ]);

        $this->assertEquals('registrada', $incident->estado);
        $this->assertEquals(1, $incident->getCurrentStepNumber());

        // Paso 2: Asignar Proveedor
        $this->lifecycleService->transitionTo($incident, 'proveedor_asignado', $user);
        $this->assertEquals('proveedor_asignado', $incident->estado);

        // Paso 3: Diagnóstico
        $this->lifecycleService->transitionTo($incident, 'diagnostico_cargado', $user, null, ['diagnostico_texto' => 'Falla en bobina']);
        $this->assertEquals('diagnostico_cargado', $incident->estado);
        $this->assertEquals('Falla en bobina', $incident->diagnostico_texto);
    }

    public function test_blocks_execution_without_purchase_order(): void
    {
        $company = Company::create(['nombre' => 'Waldo\'s Demo', 'rfc_tax_id' => 'WDM999999999']);
        $branch = Branch::create(['company_id' => $company->id, 'nombre' => 'Sucursal Norte', 'zona_geografica' => 'Norte']);
        $category = Category::create(['nombre' => 'HVAC']);
        $user = User::create(['name' => 'Manager User', 'email' => 'manager@test.com', 'password' => 'password', 'rol' => 'manager']);

        $incident = Incident::create([
            'codigo_ticket' => 'INC-TEST-002',
            'branch_id' => $branch->id,
            'titulo' => 'Falla en clima',
            'descripcion' => 'Sin aire frío',
            'categoria_id' => $category->id,
            'prioridad' => 'media',
            'estado' => 'cotizacion_validada',
            'notifier_id' => $user->id,
        ]);

        // Intentar pasar a 'en_ejecucion' sin OC debe lanzar una excepción
        $this->expectException(Exception::class);
        $this->lifecycleService->transitionTo($incident, 'en_ejecucion', $user);
    }

    public function test_allows_execution_when_purchase_order_is_generated(): void
    {
        $company = Company::create(['nombre' => 'Waldo\'s Demo', 'rfc_tax_id' => 'WDM888888888']);
        $branch = Branch::create(['company_id' => $company->id, 'nombre' => 'Sucursal Centro 1', 'zona_geografica' => 'Centro']);
        $category = Category::create(['nombre' => 'Plomería']);
        $user = User::create(['name' => 'Admin User', 'email' => 'admin@test.com', 'password' => 'password', 'rol' => 'admin']);

        $incident = Incident::create([
            'codigo_ticket' => 'INC-TEST-003',
            'branch_id' => $branch->id,
            'titulo' => 'Fuga de agua potable',
            'descripcion' => 'Tubería rota',
            'categoria_id' => $category->id,
            'prioridad' => 'critica',
            'estado' => 'cotizacion_validada',
            'notifier_id' => $user->id,
            'fixer_id' => $user->id,
        ]);

        // Generar OC
        $items = [
            [
                'codigo_concepto' => 'PLO-001',
                'descripcion' => 'Cambio de bomba',
                'unidad_medida' => 'pieza',
                'cantidad' => 1,
                'precio_unitario' => 1000.00,
            ]
        ];

        $po = $this->lifecycleService->generatePurchaseOrder($incident, $user, $items, 'Nota de prueba');

        $this->assertEquals('oc_emitida', $incident->fresh()->estado);
        $this->assertEquals(1160.00, $po->monto_total);

        // Registrar Folio Cliente
        $this->lifecycleService->registerClientFolio($po, 'WALDOS-OC-99001', $user);
        $this->assertEquals('WALDOS-OC-99001', $po->fresh()->folio_cliente);

        // Ahora avanzar a 'en_ejecucion' debe funcionar sin excepción
        $incidentUpdated = $this->lifecycleService->transitionTo($incident->fresh(), 'en_ejecucion', $user);
        $this->assertEquals('en_ejecucion', $incidentUpdated->estado);
    }

    public function test_can_access_incident_by_id_or_ticket_code(): void
    {
        $company = Company::create(['nombre' => 'Waldo\'s Demo', 'rfc_tax_id' => 'WDM777777777']);
        $branch = Branch::create(['company_id' => $company->id, 'nombre' => 'Sucursal Sur 1', 'zona_geografica' => 'Sur']);
        $category = Category::create(['nombre' => 'Obra Civil']);
        $user = User::create(['name' => 'User 1', 'email' => 'user1@test.com', 'password' => 'password', 'rol' => 'notifier']);

        $incident = Incident::create([
            'codigo_ticket' => 'INC-CODE-99',
            'branch_id' => $branch->id,
            'titulo' => 'Grieta en muro',
            'descripcion' => 'Daño estructural',
            'categoria_id' => $category->id,
            'prioridad' => 'media',
            'estado' => 'registrada',
            'notifier_id' => $user->id,
        ]);

        // Acceso por UUID (id)
        $responseById = $this->get(route('incidents.show', $incident->id));
        $responseById->assertStatus(200);

        // Acceso por Código de Ticket (codigo_ticket)
        $responseByCode = $this->get(route('incidents.show', 'INC-CODE-99'));
        $responseByCode->assertStatus(200);
    }
}
