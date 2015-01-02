<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReglasTransitoTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('reglas_transito', function(Blueprint $table)
		{
			//
			$table->increments('id');
			$table->integer('id_pienso_transito_inicial');
			$table->integer('id_pienso_transito_final');
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
		Schema::drop('reglas_transito', function(Blueprint $table)
		{
			//
			//
			$table->increments('id');
			$table->integer('id_pienso_transito_inicial');
			$table->integer('id_pienso_transito_final');
			$table->timestamps();
		});
	}

}
