<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('empresas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('logo')->nullable();
            $table->string('favicon')->nullable();
            $table->string('telefono_principal')->nullable();
            $table->string('whatsapp_principal')->nullable();
            $table->string('email_contacto')->nullable();
            $table->text('direccion_fiscal')->nullable();
            $table->decimal('porcentaje_deltron', 5, 2)->default(25.00);
            $table->json('redes_sociales')->nullable();
            $table->json('banners_por_pagina')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('empresas');
    }
};
