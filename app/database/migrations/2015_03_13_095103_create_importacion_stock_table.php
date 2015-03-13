<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateImportacionStockTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('importacion_stock', function(Blueprint $table)
		{
			//
			$table->increments('id');
			$table->integer('almacen_id');
			$table->integer('pienso_id');
			$table->integer('cantidad_acuifarm');
			$table->integer('cantidad_fishtalk');
			$table->integer('diferencia');
			$table->date('fecha');
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
		Schema::drop('importacion_stock', function(Blueprint $table)
		{
			//
		});
	}

}
