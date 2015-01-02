<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDetalleRangosTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('detalle_rangos', function(Blueprint $table)
		{
			//
			$table->increments('id');
			$table->integer('cabecera_rango_id');
			$table->integer('tamanio_pellet_id');
			$table->integer('pm_min');
			$table->integer('pm_max');
			$table->integer('pm_transito');
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
		Schema::drop('detalle_rangos', function(Blueprint $table)
		{
			//
		});
	}

}
