<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHistoricoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('historico', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_solicitud')->nullable();
            $table->unsignedBigInteger('id_expediente')->nullable();
            $table->unsignedBigInteger('id_usuario_save')->nullable();
            $table->unsignedBigInteger('id_usuario_update')->nullable();
            $table->string('estado',50);
            $table->boolean('documento')->default(false);
            $table->string('nombre_documento',50)->nullable();
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
        Schema::dropIfExists('historico');
    }
}
