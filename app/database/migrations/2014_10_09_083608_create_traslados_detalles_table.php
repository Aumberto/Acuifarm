<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTrasladosDetallesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('traslados_detalles', function(Blueprint $table)
		{
			//
			$table->increments('id');
			$table->integer('traslado_id');
			$table->integer('pienso_id');
			$table->string('pienso');
			$table->string('codigopienso');
			$table->integer('cantidad');
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
		Schema::drop('traslados_detalles', function(Blueprint $table)
		{
			//
		});
	}

}
