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
        Schema::create('hotel', function (Blueprint $table) {
            $table->id(); // BIGINT auto-increment, primary key
            $table->integer('nit')->unique();
            $table->string('nombre');
            $table->string('ciudad');
            $table->string('direccion');
            $table->integer('numHab');
            $table->timestamps();

            $table->unique('id', 'id_hotel');
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hotel');
    }
};
