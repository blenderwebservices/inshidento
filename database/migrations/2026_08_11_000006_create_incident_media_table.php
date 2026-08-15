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
            $table->string('tipo')->default('image'); // 'image', 'video', 'document', 'external_link'
            $table->string('origen')->default('upload'); // 'upload', 'external_link', 'reporte_inicial', 'cierre_fixer'
            $table->timestamp('fecha_carga')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('incident_media');
    }
};
