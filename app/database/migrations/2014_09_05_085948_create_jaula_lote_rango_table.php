<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateJaulaLoteRangoTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('jaula_lote_rango', function(Blueprint $table)
		{
			//
			$table->increments('id');
			$table->integer('jaula_id');
			$table->integer('lote_id');
			$table->integer('cabecera_rango_id');
			$table->date('fecha_inicio');
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
		Schema::drop('jaula_lote_rango', function(Blueprint $table)
		{
			//
		});
	}

}
