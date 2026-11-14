<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tiendas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_sucursal');
            $table->string('direccion');
            $table->string('ciudad')->default('Lima');
            $table->string('telefono')->nullable();
            $table->string('whatsapp')->nullable();
            $table->string('horario_atencion')->nullable();
            $table->string('mapa_embed_url', 500)->nullable();
            $table->boolean('es_principal')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tiendas');
    }
};
