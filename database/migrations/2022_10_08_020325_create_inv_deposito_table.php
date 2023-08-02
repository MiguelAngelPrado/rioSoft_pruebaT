<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvDepositoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inv_deposito', function (Blueprint $table) {
            $table->id();
            $table->string('nombre',25)->unique();
            $table->string('contact_name',50)->nullable();
            $table->string('contact_telef',25)->nullable();
            $table->integer('enabled')->default(1);
            $table->string('address',150)->nullable();
            $table->string('provincia',50)->nullable();
            $table->string('email',100)->nullable();
            $table->unsignedBigInteger('last_updated_by')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
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
        Schema::dropIfExists('inv_deposito');
    }
}
