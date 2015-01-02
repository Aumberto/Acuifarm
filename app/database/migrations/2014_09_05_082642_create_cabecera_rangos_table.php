<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCabeceraRangosTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('cabecera_rangos', function(Blueprint $table)
		{
			//
			$table->increments('id');
			$table->string('nombre');
			$table->text('descripcion');
			$table->boolean('predeterminada');
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
		Schema::drop('cabecera_rangos', function(Blueprint $table)
		{
			//
		});
	}

}
