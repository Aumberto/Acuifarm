<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSiembrasTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('siembras', function(Blueprint $table)
		{
			//
			$table->increments('id');
			$table->integer('granja_id');
			$table->integer('jaula_id');
			$table->integer('lote');
			$table->integer('cabecera_rangos_id');
			$table->integer('input_count');
			$table->decimal('input_avg', 10,2);
			$table->decimal('input_bio', 10,2);
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
		Schema::drop('siembras', function(Blueprint $table)
		{
			//
		});
	}

}
