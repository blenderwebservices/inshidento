<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('folio_interno')->unique();
            $table->string('folio_cliente')->nullable();
            $table->uuid('incident_id');
            $table->foreignUuid('supplier_id')->constrained('users')->onDelete('cascade');
            $table->decimal('subtotal', 10, 2);
            $table->decimal('iva', 10, 2);
            $table->decimal('monto_total', 10, 2);
            $table->enum('estado', ['borrador', 'emitida', 'aprobada', 'en_ejecucion', 'facturada', 'cancelada'])->default('emitida');
            $table->string('pdf_path')->nullable();
            $table->text('notas')->nullable();
            $table->timestamp('fecha_emision');
            $table->timestamp('fecha_aprobacion')->nullable();
            $table->foreignUuid('aprobado_por_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });

        Schema::create('purchase_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('purchase_order_id')->constrained('purchase_orders')->onDelete('cascade');
            $table->foreignId('unit_price_catalog_id')->nullable()->constrained('unit_price_catalogs')->onDelete('set null');
            $table->string('codigo_concepto');
            $table->string('descripcion');
            $table->string('unidad_medida');
            $table->decimal('cantidad', 10, 2);
            $table->decimal('precio_unitario', 10, 2);
            $table->decimal('subtotal', 10, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_order_items');
        Schema::dropIfExists('purchase_orders');
    }
};
