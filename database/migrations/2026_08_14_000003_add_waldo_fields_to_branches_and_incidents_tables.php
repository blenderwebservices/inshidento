<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('branches', function (Blueprint $table) {
            $table->string('zona_geografica')->default('Centro')->after('company_id');
        });

        Schema::table('incidents', function (Blueprint $table) {
            $table->text('diagnostico_texto')->nullable()->after('ubicacion_especifica');
            $table->text('propuesta_tecnica')->nullable()->after('diagnostico_texto');
            $table->foreignUuid('purchase_order_id')->nullable()->constrained('purchase_orders')->onDelete('set null')->after('billing_report_id');
            $table->json('documentos_fiscales')->nullable()->after('purchase_order_id');
        });
    }

    public function down(): void
    {
        Schema::table('incidents', function (Blueprint $table) {
            $table->dropForeign(['purchase_order_id']);
            $table->dropColumn(['diagnostico_texto', 'propuesta_tecnica', 'purchase_order_id', 'documentos_fiscales']);
        });

        Schema::table('branches', function (Blueprint $table) {
            $table->dropColumn('zona_geografica');
        });
    }
};
