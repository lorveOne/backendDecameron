<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('habitacion_info_tipo', function (Blueprint $table) {
            $table->id(); // BIGINT auto-increment
            $table->unsignedBigInteger('idHotel');
            $table->integer('numHabi');
            $table->string('tipoHabi');
            $table->string('acomoda');
            $table->timestamps();

            $table->primary(['id', 'idHotel'], 'habitacion_info_tipo_pkey');
            $table->foreign('idHotel')->references('id')->on('hotel')->onDelete('cascade');
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('habitacion_info_tipo');
    }
};
