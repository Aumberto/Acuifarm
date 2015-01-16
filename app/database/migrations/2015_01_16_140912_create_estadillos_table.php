<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEstadillosTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('estadillos', function(Blueprint $table)
		{
			//
			$table->increments('id');
			$table->integer('jaula_id');
			$table->date('fecha');
			$table->integer('num_tomas');
			$table->integer('porcentaje_primera_toma');
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
		Schema::drop('estadillos', function(Blueprint $table)
		{
			//
		});
	}

}
