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
        Schema::create('receipts', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('correlative_number')->unique();
            $table->string('client_name');
            $table->string('client_rut')->nullable(); // RUT del cliente
            $table->text('description');
            $table->integer('net_amount'); // Monto neto en pesos
            $table->integer('iva_amount'); // Monto del IVA en pesos
            $table->integer('total_amount'); // Monto total en pesos
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('receipts');
    }
};
