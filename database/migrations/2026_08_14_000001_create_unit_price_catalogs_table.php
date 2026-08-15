<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('unit_price_catalogs', function (Blueprint $table) {
            $table->id();
            $table->string('codigo_concepto');
            $table->string('zona_geografica');
            $table->foreignId('categoria_id')->constrained('categories')->onDelete('cascade');
            $table->text('descripcion');
            $table->string('unidad_medida')->default('servicio');
            $table->decimal('precio_unitario', 10, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('unit_price_catalogs');
    }
};
