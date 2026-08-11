<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Company;
use App\Models\Branch;
use App\Models\Category;
use App\Models\Incident;
use App\Models\BillingReport;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Crear Categorías
        $catElectrica = Category::create([
            'nombre' => 'Eléctrica',
            'descripcion' => 'Fallas de iluminación, balastros, contactos y variaciones de voltaje.'
        ]);

        $catPlomeria = Category::create([
            'nombre' => 'Plomería',
            'descripcion' => 'Fugas de agua, drenaje, bombas e hidroneumáticos.'
        ]);

        $catTI = Category::create([
            'nombre' => 'TI & Redes',
            'descripcion' => 'Puntos de red, cableado estructurado y servidores.'
        ]);

        $catHVAC = Category::create([
            'nombre' => 'Climatización (HVAC)',
            'descripcion' => 'Unidades manejadoras de aire, fancoils y aire acondicionado.'
        ]);

        // 2. Crear Empresa y Sucursal Demo
        $company = Company::create([
            'nombre' => 'Grupo Comercial Retail S.A.',
            'rfc_tax_id' => 'GCR900812XYZ',
            'activo' => true
        ]);

        $branchPolanco = Branch::create([
            'company_id' => $company->id,
            'nombre' => 'Sucursal Polanco',
            'codigo_sucursal' => 'SUC-001',
            'direccion' => 'Av. Presidente Masaryk 101, CDMX',
            'latitud' => 19.4326077,
            'longitud' => -99.1947537,
        ]);

        $branchGuadalajara = Branch::create([
            'company_id' => $company->id,
            'nombre' => 'Sucursal Guadalajara Centro',
            'codigo_sucursal' => 'SUC-002',
            'direccion' => 'Av. Juárez 450, Guadalajara, Jal.',
            'latitud' => 20.6736,
            'longitud' => -103.344,
        ]);

        // 3. Crear Usuarios de prueba con roles
        $admin = User::create([
            'name' => 'Administrador General',
            'email' => 'admin@inshidento.com',
            'password' => Hash::make('password'),
            'company_id' => $company->id,
            'rol' => 'admin',
        ]);

        $manager = User::create([
            'name' => 'Supervisora Operativa',
            'email' => 'manager@inshidento.com',
            'password' => Hash::make('password'),
            'company_id' => $company->id,
            'rol' => 'manager',
        ]);

        $notifier = User::create([
            'name' => 'Carlos Mendoza (Reportero)',
            'email' => 'notificador@inshidento.com',
            'password' => Hash::make('password'),
            'company_id' => $company->id,
            'branch_id' => $branchPolanco->id,
            'rol' => 'notifier',
        ]);

        $fixerInterno = User::create([
            'name' => 'Juan Pérez (Técnico Interno)',
            'email' => 'fixer.interno@inshidento.com',
            'password' => Hash::make('password'),
            'company_id' => $company->id,
            'rol' => 'fixer',
            'tipo_fixer' => 'interno',
            'especialidad' => 'Electricista & Mantenimiento',
        ]);

        $fixerExterno = User::create([
            'name' => 'Servimant HVAC S.A. (Contratista)',
            'email' => 'fixer.externo@inshidento.com',
            'password' => Hash::make('password'),
            'company_id' => $company->id,
            'rol' => 'fixer',
            'tipo_fixer' => 'externo',
            'especialidad' => 'Climatización & Plomería Industrial',
        ]);

        // 4. Crear Incidencias Demo
        $inc1 = Incident::create([
            'codigo_ticket' => 'INC-8801',
            'branch_id' => $branchPolanco->id,
            'titulo' => 'Fuga de agua en UMA-04 Piso 3',
            'descripcion' => 'Fuga moderada en tubería de condensados del aire acondicionado central.',
            'categoria_id' => $catHVAC->id,
            'prioridad' => 'alta',
            'estado' => 'resuelta',
            'ubicacion_especifica' => 'Cuarto de Máquinas - Piso 3',
            'notifier_id' => $notifier->id,
            'manager_id' => $manager->id,
            'fixer_id' => $fixerExterno->id,
            'costo_mano_obra' => 140.00,
            'costo_materiales' => 45.00,
            'fecha_resolucion' => now()->subHours(4),
        ]);

        $inc2 = Incident::create([
            'codigo_ticket' => 'INC-8802',
            'branch_id' => $branchPolanco->id,
            'titulo' => 'Falla de alumbrado en pasillo principal',
            'descripcion' => 'Se requiere reemplazo de balastro y 4 tubos LED.',
            'categoria_id' => $catElectrica->id,
            'prioridad' => 'media',
            'estado' => 'en_progreso',
            'ubicacion_especifica' => 'Pasillo Central',
            'notifier_id' => $notifier->id,
            'manager_id' => $manager->id,
            'fixer_id' => $fixerInterno->id,
            'costo_mano_obra' => 50.00,
            'costo_materiales' => 35.00,
        ]);

        // 5. Crear Lote de Facturación Demo
        $billingReport = BillingReport::create([
            'company_id' => $company->id,
            'fixer_id' => $fixerExterno->id,
            'folio_factura' => 'FAC-2026-904',
            'tipo_fixer' => 'externo',
            'total_incidencias' => 1,
            'monto_total' => 185.00,
            'estado' => 'enviado_facturacion',
            'fecha_cierre' => now(),
        ]);

        $inc1->billing_report_id = $billingReport->id;
        $inc1->save();
    }
}
