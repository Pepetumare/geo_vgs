<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('receipts', function (Blueprint $table) {
            // Cambiamos el tipo de columna para permitir números más grandes
            $table->bigInteger('net_amount')->change();
            $table->bigInteger('iva_amount')->change();
            $table->bigInteger('total_amount')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('receipts', function (Blueprint $table) {
            // Esto permite revertir el cambio si es necesario
            $table->integer('net_amount')->change();
            $table->integer('iva_amount')->change();
            $table->integer('total_amount')->change();
        });
    }
};