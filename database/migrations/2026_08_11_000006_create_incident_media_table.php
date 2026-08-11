<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('incident_media', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('incident_id')->constrained('incidents')->onDelete('cascade');
            $table->text('url_archivo');
            $table->enum('tipo', ['image', 'audio', 'video'])->default('image');
            $table->enum('origen', ['reporte_inicial', 'cierre_fixer'])->default('reporte_inicial');
            $table->timestamp('fecha_carga')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('incident_media');
    }
};
