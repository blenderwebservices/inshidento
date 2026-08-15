<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\Category;
use App\Models\Company;
use App\Models\Incident;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class Aug15EnhancementsTest extends TestCase
{
    use RefreshDatabase;

    public function test_emergency_route_allows_bypass_to_execution(): void
    {
        $company = Company::create(['nombre' => 'Waldo\'s Test', 'rfc_tax_id' => 'WDM123456789']);
        $branch = Branch::create(['company_id' => $company->id, 'nombre' => 'Sucursal Bajío', 'zona_geografica' => 'Bajío']);
        $category = Category::create(['nombre' => 'Eléctrica']);
        $user = User::create(['name' => 'FM Test', 'email' => 'fm_test@test.com', 'password' => 'password', 'rol' => 'manager']);

        $incident = Incident::create([
            'codigo_ticket' => 'INC-EMG-01',
            'branch_id' => $branch->id,
            'titulo' => 'Robo de interruptor termomagnético',
            'descripcion' => 'Riesgo de paro',
            'categoria_id' => $category->id,
            'prioridad' => 'critica',
            'es_emergencia' => true,
            'motivo_emergencia' => 'Riesgo de paro de tienda',
            'estado' => 'registrada',
            'notifier_id' => $user->id,
        ]);

        // Verificar que canStartExecution sea true sin tener OC previa
        $this->assertTrue($incident->canStartExecution());

        // Avanzar directamente a en_ejecucion a través del controlador
        $response = $this->actingAs($user)->post(route('incidents.advance', $incident), [
            'next_state' => 'en_ejecucion',
            'comentario' => 'Bypass de emergencia ejecutado',
        ]);

        $response->assertRedirect();
        $this->assertEquals('en_ejecucion', $incident->fresh()->estado);
    }

    public function test_supplier_directory_filters_by_zone(): void
    {
        $company = Company::create(['nombre' => 'Waldo\'s Test', 'rfc_tax_id' => 'WDM123456780']);

        User::create([
            'name' => 'Proveedor Norte',
            'email' => 'norte@test.com',
            'password' => 'password',
            'company_id' => $company->id,
            'rol' => 'fixer',
            'zona_cobertura' => 'Noreste',
        ]);

        User::create([
            'name' => 'Proveedor Bajío',
            'email' => 'bajio@test.com',
            'password' => 'password',
            'company_id' => $company->id,
            'rol' => 'fixer',
            'zona_cobertura' => 'Bajío',
        ]);

        $response = $this->get(route('suppliers.index', ['zona' => 'Bajío']));
        $response->assertStatus(200);
        $response->assertSee('Proveedor Bajío');
    }

    public function test_upload_media_validates_max_file_size_and_supports_external_links(): void
    {
        Storage::fake('public');

        $company = Company::create(['nombre' => 'Waldo\'s Test', 'rfc_tax_id' => 'WDM123456781']);
        $branch = Branch::create(['company_id' => $company->id, 'nombre' => 'Sucursal Centro', 'zona_geografica' => 'Centro']);
        $category = Category::create(['nombre' => 'HVAC']);
        $user = User::create(['name' => 'User Test', 'email' => 'user_test@test.com', 'password' => 'password', 'rol' => 'manager']);

        $incident = Incident::create([
            'codigo_ticket' => 'INC-MED-01',
            'branch_id' => $branch->id,
            'titulo' => 'Falla en UMA',
            'descripcion' => 'Revisión técnica',
            'categoria_id' => $category->id,
            'prioridad' => 'media',
            'estado' => 'registrada',
            'notifier_id' => $user->id,
        ]);

        // 1. Probar adjunto de Enlace Externo (Google Drive)
        $responseLink = $this->actingAs($user)->post(route('incidents.upload-media', $incident), [
            'origen' => 'external_link',
            'plataforma' => 'Google Drive',
            'titulo' => 'Carpeta Evidencia Google Drive',
            'url_archivo' => 'https://drive.google.com/drive/folders/123456789Demo',
        ]);

        $responseLink->assertRedirect();
        $this->assertDatabaseHas('incident_media', [
            'incident_id' => $incident->id,
            'plataforma' => 'Google Drive',
            'titulo' => 'Carpeta Evidencia Google Drive',
        ]);

        // 2. Probar subida de archivo válido (3 MB)
        $validFile = UploadedFile::fake()->create('evidencia_video.mp4', 3000, 'video/mp4');
        $responseFile = $this->actingAs($user)->post(route('incidents.upload-media', $incident), [
            'origen' => 'upload',
            'archivo' => $validFile,
            'titulo' => 'Video de Prueba UMA',
        ]);

        $responseFile->assertRedirect();
        $this->assertDatabaseHas('incident_media', [
            'incident_id' => $incident->id,
            'titulo' => 'Video de Prueba UMA',
            'tipo' => 'video',
        ]);
    }
}
