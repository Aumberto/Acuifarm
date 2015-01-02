<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMovimientosAlmacenesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('movimientos_almacenes', function(Blueprint $table)
		{
			//
			$table->increments('id');
			$table->integer('almacen_id');
			$table->string('tipo_movimiento');
			$table->text('descripcion');
			$table->integer('pienso_id');
			$table->integer('cantidad');
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
		Schema::drop('movimientos_almacenes', function(Blueprint $table)
		{
			//
		});
	}

}
