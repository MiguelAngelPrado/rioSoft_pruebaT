<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVehiculoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vehiculo', function (Blueprint $table) {
            $table->id();
            $table->string('placa',50);
            $table->unsignedBigInteger('marca')->nullable();
            $table->unsignedBigInteger('modelo')->nullable();
            $table->string('anio',5)->nullable();
            $table->string('serie');
            $table->string('color')->nullable();
            $table->string('motor')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vehiculo');
    }
}
