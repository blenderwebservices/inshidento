<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'zona_cobertura')) {
                $table->string('zona_cobertura')->nullable()->after('especialidad');
            }
        });

        Schema::table('incidents', function (Blueprint $table) {
            if (!Schema::hasColumn('incidents', 'es_emergencia')) {
                $table->boolean('es_emergencia')->default(false)->after('prioridad');
            }
            if (!Schema::hasColumn('incidents', 'motivo_emergencia')) {
                $table->text('motivo_emergencia')->nullable()->after('es_emergencia');
            }
        });

        Schema::table('incident_media', function (Blueprint $table) {
            if (!Schema::hasColumn('incident_media', 'plataforma')) {
                $table->string('plataforma')->nullable()->after('url_archivo');
            }
            if (!Schema::hasColumn('incident_media', 'titulo')) {
                $table->string('titulo')->nullable()->after('plataforma');
            }
            if (!Schema::hasColumn('incident_media', 'duracion_segundos')) {
                $table->integer('duracion_segundos')->nullable()->after('tipo');
            }
            if (!Schema::hasColumn('incident_media', 'peso_bytes')) {
                $table->bigInteger('peso_bytes')->nullable()->after('duracion_segundos');
            }
        });
    }

    public function down(): void
    {
        Schema::table('incident_media', function (Blueprint $table) {
            $table->dropColumn(['plataforma', 'titulo', 'duracion_segundos', 'peso_bytes']);
        });

        Schema::table('incidents', function (Blueprint $table) {
            $table->dropColumn(['es_emergencia', 'motivo_emergencia']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('zona_cobertura');
        });
    }
};
