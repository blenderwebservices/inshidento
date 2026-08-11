<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('billing_reports', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('company_id')->constrained('companies')->onDelete('cascade');
            $table->foreignUuid('fixer_id')->constrained('users')->onDelete('cascade');
            $table->string('folio_factura');
            $table->enum('tipo_fixer', ['interno', 'externo'])->default('externo');
            $table->integer('total_incidencias')->default(0);
            $table->decimal('monto_total', 12, 2)->default(0.00);
            $table->enum('estado', ['borrador', 'enviado_facturacion', 'aprobado', 'pagado'])->default('borrador');
            $table->timestamp('fecha_cierre')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('billing_reports');
    }
};
