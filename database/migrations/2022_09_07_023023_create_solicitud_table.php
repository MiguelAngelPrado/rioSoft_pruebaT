<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSolicitudTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('solicitud', function (Blueprint $table) {
            $table->id();
            $table->uuid('id_solicitud');
            $table->string('numero')->unique();
            $table->string('tipo',20)->nullable();
            $table->string('perdida',50)->nullable();
            $table->string('boleta_informativa')->nullable();
            $table->string('carta')->nullable();
            $table->unsignedBigInteger('id_notaria')->nullable();
            $table->string('acta_inicial')->nullable();
            $table->string('acta_final')->nullable();
            $table->string('qr')->nullable();
            $table->string('llave',10)->nullable();
            $table->string('tarjeta',10)->nullable();
            $table->unsignedBigInteger('id_deposito')->nullable();
            $table->unsignedBigInteger('id_ubicacion')->nullable();
            $table->unsignedBigInteger('id_vehiculo')->nullable();
            $table->unsignedBigInteger('id_persona')->nullable();
            $table->string('deposito')->nullable();
            $table->string('pagos',5)->nullable();
            $table->text('comentarios')->nullable();
            $table->text('observaciones')->nullable();
            $table->json('propietarios')->nullable();
            $table->text('entrego_a')->nullable();
            $table->string('recepcion')->nullable();
            $table->string('kardex')->nullable();
            $table->string('adjunto')->nullable();
            $table->string('adjunto_salida')->nullable();
            $table->string('estado');
            $table->dateTime('date_boleta_informativa')->nullable();
            $table->dateTime('date_carta')->nullable();
            $table->dateTime('date_acta_inicial')->nullable();
            $table->dateTime('date_acta_final')->nullable();
            $table->dateTime('date_adjunto')->nullable();
            $table->json('fotos')->nullable();
            $table->string('contacto')->nullable();
            $table->unsignedBigInteger('usuario_id')->nullable();
            $table->unsignedBigInteger('id_taller')->nullable();
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
        Schema::dropIfExists('solicitud');
    }
}
