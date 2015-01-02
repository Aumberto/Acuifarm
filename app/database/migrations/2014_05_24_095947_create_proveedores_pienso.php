<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProveedoresPienso extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('proveedores_pienso', function(Blueprint $table)
		{
			// Creamos la tabla proveedores_pienso
			$table->increments('id');
			$table->string('nombre');
			$table->string('email');
			$table->string('web');
			$table->string('telefono');
			$table->string('fax');
			$table->string('direccion');
			$table->string('cp');
			$table->string('localidad');
			$table->string('pais');
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
		Schema::drop('proveedores_pienso', function(Blueprint $table)
		{
			//
		});
	}

}
