<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMenuTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('menu', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_padre')->nullable();
            $table->unsignedBigInteger('id_permiso')->nullable();
            $table->string('nombre',100);
            $table->string('alias',100);
            $table->string('ruta');
            $table->string('icono',100);
            $table->boolean('estado')->default(1);
            $table->timestamps();

            $table->foreign('id_padre')
                ->references('id')
                ->on('menu');
            $table->foreign('id_permiso')
                ->references('id')
                ->on('permissions');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('menu');
    }
}
