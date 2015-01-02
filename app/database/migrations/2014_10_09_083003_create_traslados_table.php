<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTrasladosTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('traslados', function(Blueprint $table)
		{
			//
			$table->increments('id');
			$table->string('nombre');
			$table->integer('id_almacen_origen');
			$table->integer('id_almacen_destino');
			$table->date('fecha_traslado');
			$table->enum('estado', array('En tránsito', 'Descargado'));
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
		Schema::drop('traslados', function(Blueprint $table)
		{
			//
		});
	}

}
