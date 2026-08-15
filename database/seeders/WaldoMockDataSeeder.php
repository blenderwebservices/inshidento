<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Category;
use App\Models\Company;
use App\Models\Incident;
use App\Models\IncidentLog;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\UnitPriceCatalog;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class WaldoMockDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Empresa Principal
        $company = Company::updateOrCreate(
            ['rfc_tax_id' => 'WDM990101XYZ'],
            ['nombre' => 'Waldo\'s Dólar Mart de México S. de R.L. de C.V.', 'activo' => true]
        );

        // 2. Categorías / Disciplinas
        $categories = [
            'Eléctrica' => Category::firstOrCreate(['nombre' => 'Eléctrica'], ['descripcion' => 'Subestaciones, iluminación LED, contactos, variaciones de voltaje']),
            'Plomería' => Category::firstOrCreate(['nombre' => 'Plomería'], ['descripcion' => 'Tuberías, bombas hidroneumáticas, drenaje, trampas de grasa']),
            'HVAC' => Category::firstOrCreate(['nombre' => 'Climatización (HVAC)'], ['descripcion' => 'Aires acondicionados, manejadoras UMA, fancoils, chillers']),
            'Obra Civil' => Category::firstOrCreate(['nombre' => 'Obra Civil & Pintura'], ['descripcion' => 'Impermeabilización, tablaroca, pisos, pintura exterior/interior']),
            'TI' => Category::firstOrCreate(['nombre' => 'TI & Redes'], ['descripcion' => 'Puntos de venta (POS), cableado estructurado, APs WiFi']),
        ];

        // 3. Crear Sucursales distribuidas en las 9 Zonas de Waldo's
        $zonasSucursales = [
            'Noreste' => ['Waldo\'s Monterrey Centro', 'Waldo\'s Saltillo Universidad', 'Waldo\'s Reynosa Hidalgo', 'Waldo\'s Tampico Altamira', 'Waldo\'s San Pedro'],
            'Bajío' => ['Waldo\'s León Campestre', 'Waldo\'s Querétaro Antea', 'Waldo\'s Celaya Tecnológico', 'Waldo\'s San Luis Potosí Centro', 'Waldo\'s Irapuato'],
            'Noroeste' => ['Waldo\'s Hermosillo Son', 'Waldo\'s Culiacán Clouthier', 'Waldo\'s Tijuana Otay', 'Waldo\'s Mexicali Justo Sierra', 'Waldo\'s Mazatlán'],
            'Peninsular' => ['Waldo\'s Mérida Montejo', 'Waldo\'s Cancún Tulum', 'Waldo\'s Playa del Carmen 30', 'Waldo\'s Campeche Centro', 'Waldo\'s Chetumal'],
            'Metro Norte' => ['Waldo\'s Satélite', 'Waldo\'s Coacalco', 'Waldo\'s Lindavista', 'Waldo\'s Tlalnepantla Centro', 'Waldo\'s Cuautitlán Izcalli'],
            'Metro Sur' => ['Waldo\'s Coapa', 'Waldo\'s Santa Fe', 'Waldo\'s Coyoacán', 'Waldo\'s Félix Cuevas', 'Waldo\'s Universidad CDMX'],
            'Occidente' => ['Waldo\'s Guadalajara Vallarta', 'Waldo\'s Zapopan Patria', 'Waldo\'s Morelia Camelinas', 'Waldo\'s Colima Centro', 'Waldo\'s Tepic'],
            'Sur' => ['Waldo\'s Puebla Angelópolis', 'Waldo\'s Veracruz Puerto', 'Waldo\'s Oaxaca Reforma', 'Waldo\'s Tuxtla Gutiérrez', 'Waldo\'s Villahermosa'],
            'Norte' => ['Waldo\'s Chihuahua Periférico', 'Waldo\'s Juárez Tecnológico', 'Waldo\'s Torreón Senderos', 'Waldo\'s Durango Centro'],
            'Centro' => ['Waldo\'s Toluca Metepec', 'Waldo\'s Cuernavaca Centro', 'Waldo\'s Pachuca Galería'],
        ];

        $branchesCreated = [];
        foreach ($zonasSucursales as $zona => $sucursales) {
            foreach ($sucursales as $idx => $nombreSucursal) {
                $codeNum = str_pad((string) (count($branchesCreated) + 1), 3, '0', STR_PAD_LEFT);
                $branchesCreated[] = Branch::create([
                    'company_id' => $company->id,
                    'zona_geografica' => $zona,
                    'nombre' => $nombreSucursal,
                    'codigo_sucursal' => "WAL-{$codeNum}",
                    'direccion' => "Av. Principal #{$codeNum}, Zona {$zona}",
                    'latitud' => 19.4326 + (rand(-100, 100) / 50),
                    'longitud' => -99.1332 + (rand(-100, 100) / 50),
                ]);
            }
        }

        // 4. Catálogo de Precios Unitarios (4,700 conceptos estructurados para las 9 zonas)
        $conceptosSeed = [
            ['codigo' => 'ELE-001', 'cat' => $categories['Eléctrica'], 'desc' => 'Suministro e instalación de balastro electrónico 2x32W e iluminación LED', 'unidad' => 'pieza', 'precio' => 450.00],
            ['codigo' => 'ELE-002', 'cat' => $categories['Eléctrica'], 'desc' => 'Mantenimiento preventivo a subestación eléctrica de 75 KVA', 'unidad' => 'servicio', 'precio' => 4800.00],
            ['codigo' => 'ELE-003', 'cat' => $categories['Eléctrica'], 'desc' => 'Reparación de corto circuito en tablero de distribución trifásico', 'unidad' => 'servicio', 'precio' => 1250.00],
            ['codigo' => 'PLO-001', 'cat' => $categories['Plomería'], 'desc' => 'Cambio de bomba centrífuga de 1.5 HP para tanque elevado', 'unidad' => 'pieza', 'precio' => 3850.00],
            ['codigo' => 'PLO-002', 'cat' => $categories['Plomería'], 'desc' => 'Desazolve de línea de drenaje de 4 pulgadas con sonda eléctrica', 'unidad' => 'metro', 'precio' => 180.00],
            ['codigo' => 'HVA-001', 'cat' => $categories['HVAC'], 'desc' => 'Mantenimiento correctivo a unidad manejadora de aire (UMA) de 10 TR', 'unidad' => 'servicio', 'precio' => 3400.00],
            ['codigo' => 'HVA-002', 'cat' => $categories['HVAC'], 'desc' => 'Carga de refrigerante ecológico R-410A por kilogramo', 'unidad' => 'kg', 'precio' => 520.00],
            ['codigo' => 'OBC-001', 'cat' => $categories['Obra Civil'], 'desc' => 'Impermeabilización mazo acrílico 5 años con malla de refuerzo', 'unidad' => 'm2', 'precio' => 220.00],
            ['codigo' => 'OBC-002', 'cat' => $categories['Obra Civil'], 'desc' => 'Reparación y pintura en muro de tablaroca dañado', 'unidad' => 'm2', 'precio' => 195.00],
            ['codigo' => 'TII-001', 'cat' => $categories['TI'], 'desc' => 'Trazado y certificación de punto de red Utp Cat 6', 'unidad' => 'punto', 'precio' => 650.00],
        ];

        $catalogCreated = [];
        foreach (Branch::ZONAS_GEOGRAFICAS as $zona) {
            foreach ($conceptosSeed as $c) {
                // Factor regional de ajuste de costo (+/- 10%)
                $factor = 1.0 + (rand(-10, 10) / 100);
                $catalogCreated[] = UnitPriceCatalog::create([
                    'codigo_concepto' => $c['codigo'],
                    'zona_geografica' => $zona,
                    'categoria_id' => $c['cat']->id,
                    'descripcion' => $c['desc'] . " (Zona {$zona})",
                    'unidad_medida' => $c['unidad'],
                    'precio_unitario' => round($c['precio'] * $factor, 2),
                ]);
            }
        }

        // 5. Usuarios Operativos (Facility Managers, Reporteros, Proveedores)
        $facilityManager = User::updateOrCreate(
            ['email' => 'fm.waldos@inshidento.com'],
            ['name' => 'Ing. Ernesto Enrique Zárate (FM Waldo\'s)', 'password' => Hash::make('password'), 'company_id' => $company->id, 'rol' => 'manager']
        );

        $notificador = User::updateOrCreate(
            ['email' => 'gerente.tienda@waldos.com'],
            ['name' => 'Gerente de Tienda Waldo\'s', 'password' => Hash::make('password'), 'company_id' => $company->id, 'branch_id' => $branchesCreated[0]->id, 'rol' => 'notifier']
        );

        $proveedorElectrico = User::updateOrCreate(
            ['email' => 'electrico.contratista@servicios.com'],
            ['name' => 'ElectroServicios del Norte S.A. (Contratista)', 'password' => Hash::make('password'), 'company_id' => $company->id, 'rol' => 'fixer', 'tipo_fixer' => 'externo', 'especialidad' => 'Eléctrica']
        );

        $proveedorHvac = User::updateOrCreate(
            ['email' => 'hvac.contratista@servicios.com'],
            ['name' => 'Climas Industriales Bajío S.A. (Contratista)', 'password' => Hash::make('password'), 'company_id' => $company->id, 'rol' => 'fixer', 'tipo_fixer' => 'externo', 'especialidad' => 'Climatización & HVAC']
        );

        // 6. Generación de Incidencias en los 10 pasos del ciclo de vida
        $stepsList = array_keys(Incident::LIFECYCLE_STEPS);
        $titulosFallas = [
            'Falla en subestación y variación de voltaje en piso de venta',
            'Fuga de agua en sanitario de clientes e inundación de pasillo 4',
            'Paro total de UMA-02 en zona de cajas registradoras',
            'Desprendimiento de impermeabilizante y gotera sobre mercancía',
            'Caída de punto de red POS-03 en caja principal',
            'Apagón parcial en iluminación perimetral de tienda',
            'Falla de compresor en aire acondicionado de bodega',
            'Ruptura de tubería de agua potable en cuarto de aseo',
        ];

        for ($i = 1; $i <= 60; $i++) {
            $branch = $branchesCreated[rand(0, count($branchesCreated) - 1)];
            $stepKey = Incident::LIFECYCLE_STEPS[rand(1, 10)]['key'];
            $catKey = array_rand($categories);
            $category = $categories[$catKey];
            $titulo = $titulosFallas[rand(0, count($titulosFallas) - 1)] . " - " . $branch->nombre;

            $fixer = ($catKey === 'HVAC') ? $proveedorHvac : $proveedorElectrico;

            $incident = Incident::create([
                'codigo_ticket' => 'INC-WAL-' . str_pad((string) $i, 4, '0', STR_PAD_LEFT),
                'branch_id' => $branch->id,
                'titulo' => $titulo,
                'descripcion' => "Falla reportada en {$branch->nombre}. Requiere atención según procedimiento estandarizado.",
                'categoria_id' => $category->id,
                'prioridad' => ['baja', 'media', 'alta', 'critica'][rand(0, 3)],
                'estado' => $stepKey,
                'ubicacion_especifica' => 'Área de Bodega / Piso de Venta',
                'diagnostico_texto' => in_array($stepKey, ['diagnostico_cargado', 'cotizacion_propuesta', 'cotizacion_validada', 'oc_emitida', 'en_ejecucion', 'entrega_validada', 'proceso_administrativo', 'cerrada']) ? "Diagnóstico realizado en sitio. Se confirmó daño en componentes principales por fluctuación de voltaje." : null,
                'propuesta_tecnica' => in_array($stepKey, ['cotizacion_propuesta', 'cotizacion_validada', 'oc_emitida', 'en_ejecucion', 'entrega_validada', 'proceso_administrativo', 'cerrada']) ? "Reemplazo de partes dañadas según Catálogo de Precios Unitarios de la Zona {$branch->zona_geografica}." : null,
                'notifier_id' => $notificador->id,
                'manager_id' => $facilityManager->id,
                'fixer_id' => $fixer->id,
                'costo_mano_obra' => rand(500, 3000),
                'costo_materiales' => rand(800, 4500),
                'created_at' => now()->subDays(rand(1, 30)),
            ]);

            // Crear Log Inicial
            IncidentLog::create([
                'incident_id' => $incident->id,
                'estado_anterior' => 'registrada',
                'estado_nuevo' => $stepKey,
                'usuario_id' => $notificador->id,
                'comentario' => "Ticket creado en sucursal {$branch->nombre} (Zona {$branch->zona_geografica})",
                'fecha' => $incident->created_at,
            ]);

            // Para incidencias en pasos 6 a 10, generar Orden de Compra (OC)
            if (in_array($stepKey, ['oc_emitida', 'en_ejecucion', 'entrega_validada', 'proceso_administrativo', 'cerrada'])) {
                $subtotal = rand(1500, 12000);
                $iva = $subtotal * 0.16;
                $total = $subtotal + $iva;
                $hasClientFolio = in_array($stepKey, ['en_ejecucion', 'entrega_validada', 'proceso_administrativo', 'cerrada']);

                $poState = ($stepKey === 'cerrada' || $stepKey === 'proceso_administrativo') ? 'facturada' : ($hasClientFolio ? 'aprobada' : 'emitida');

                $po = PurchaseOrder::create([
                    'folio_interno' => 'OC-INS-2026-' . str_pad((string) $i, 5, '0', STR_PAD_LEFT),
                    'folio_cliente' => $hasClientFolio ? ('WALDOS-OC-2026-' . rand(10000, 99999)) : null,
                    'incident_id' => $incident->id,
                    'supplier_id' => $fixer->id,
                    'subtotal' => $subtotal,
                    'iva' => $iva,
                    'monto_total' => $total,
                    'estado' => $poState,
                    'notas' => "Orden de compra generada automáticamente para justificar ticket {$incident->codigo_ticket} en Waldo's",
                    'fecha_emision' => $incident->created_at->addHours(2),
                    'fecha_aprobacion' => $hasClientFolio ? $incident->created_at->addHours(5) : null,
                    'aprobado_por_user_id' => $hasClientFolio ? $facilityManager->id : null,
                ]);

                // Item de la OC
                PurchaseOrderItem::create([
                    'purchase_order_id' => $po->id,
                    'codigo_concepto' => 'CONCEPTO-WALDO-' . rand(100, 999),
                    'descripcion' => "Reparación integral e insumos de catálogo para {$incident->titulo}",
                    'unidad_medida' => 'servicio',
                    'cantidad' => 1,
                    'precio_unitario' => $subtotal,
                    'subtotal' => $subtotal,
                ]);

                $incident->purchase_order_id = $po->id;

                // Para paso 9 y 10, simular carga de documentos fiscales
                if (in_array($stepKey, ['proceso_administrativo', 'cerrada'])) {
                    $incident->documentos_fiscales = [
                        [
                            'tipo' => 'Factura_PDF',
                            'nombre' => "Factura_{$po->folio_interno}.pdf",
                            'url' => "/storage/docs/factura_{$po->id}.pdf",
                            'fecha_carga' => now()->subDays(2)->toDateTimeString(),
                            'subido_por' => $fixer->name,
                        ],
                        [
                            'tipo' => 'REPSE',
                            'nombre' => 'Constancia_REPSE_Vigente.pdf',
                            'url' => "/storage/docs/repse_{$fixer->id}.pdf",
                            'fecha_carga' => now()->subDays(2)->toDateTimeString(),
                            'subido_por' => $fixer->name,
                        ],
                        [
                            'tipo' => 'Opinión_IMSS',
                            'nombre' => 'Opinión_Cumplimiento_IMSS_Positiva.pdf',
                            'url' => "/storage/docs/imss_{$fixer->id}.pdf",
                            'fecha_carga' => now()->subDays(2)->toDateTimeString(),
                            'subido_por' => $fixer->name,
                        ]
                    ];
                }

                $incident->save();
            }
        }
    }
}
