<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('incidents', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('codigo_ticket')->unique();
            $table->foreignUuid('branch_id')->constrained('branches')->onDelete('cascade');
            $table->string('titulo');
            $table->text('descripcion');
            $table->foreignId('categoria_id')->constrained('categories')->onDelete('restrict');
            $table->enum('prioridad', ['baja', 'media', 'alta', 'critica'])->default('media');
            $table->enum('estado', ['abierta', 'asignada', 'en_progreso', 'resuelta', 'cancelada'])->default('abierta');
            $table->string('ubicacion_especifica')->nullable();
            $table->decimal('latitud', 10, 7)->nullable();
            $table->decimal('longitud', 10, 7)->nullable();
            $table->foreignUuid('notifier_id')->constrained('users')->onDelete('cascade');
            $table->foreignUuid('manager_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignUuid('fixer_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignUuid('billing_report_id')->nullable()->constrained('billing_reports')->onDelete('set null');
            $table->decimal('costo_mano_obra', 10, 2)->default(0.00);
            $table->decimal('costo_materiales', 10, 2)->default(0.00);
            $table->timestamp('fecha_resolucion')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('incidents');
    }
};
