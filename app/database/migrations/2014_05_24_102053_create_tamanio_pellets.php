<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTamanioPellets extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('tamanio_pellets', function(Blueprint $table)
		{
			//
			$table->increments('id');
			$table->decimal('diametro', 10,2);
			$table->decimal('pm_min', 10,2);
			$table->decimal('pm_max', 10,2);
			$table->decimal('transito', 10,2);
			$table->integer('proveedor_pienso_id');
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
		Schema::drop('tamanio_pellets', function(Blueprint $table)
		{
			//
		});
	}

}
