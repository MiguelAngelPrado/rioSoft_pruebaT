<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConfDocTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('conf_doc', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_solicitud')->nullable();
            $table->unsignedBigInteger('id_documento')->nullable();
            $table->unsignedBigInteger('id_usuario')->nullable();
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
        Schema::dropIfExists('conf_doc');
    }
}
