<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePersonaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('persona', function (Blueprint $table) {
            $table->id();
            $table->string('nombres')->nullable();
            $table->string('apellidos')->nullable();
            $table->unsignedBigInteger('id_documento')->nullable();
            $table->unsignedBigInteger('id_usaurio')->nullable();
            $table->unsignedBigInteger('id_notaria')->nullable();
            $table->string('documento')->nullable();
            $table->string('correo')->nullable();
            $table->string('telefono')->nullable();
            $table->string('tipo',20)->nullable();
            $table->string('razon_social')->nullable();
            $table->string('nro_ruc',20)->nullable();
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
        Schema::dropIfExists('persona');
    }
}
