<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateControlTransitoTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('control_transito', function(Blueprint $table)
		{
			//
			$table->increments('id');
			$table->integer('jaula_id');
			$table->string('jaula');
			$table->integer('lote_id');
			$table->string('lote');
			$table->integer('id_regla_transito');
			$table->integer('id_pienso_transito_inicial');
			$table->integer('id_pienso_transito_final');
			$table->date('fecha_inicial');
			$table->date('fecha_final');
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
		Schema::drop('control_transito', function(Blueprint $table)
		{
			//
		});
	}

}
