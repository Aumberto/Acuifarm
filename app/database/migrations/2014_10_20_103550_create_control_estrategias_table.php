<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateControlEstrategiasTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('control_estrategias', function(Blueprint $table)
		{
			//
			$table->increments('id');
			$table->integer('jaula_id');
			$table->integer('lote_id');
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
		Schema::drop('control_estrategias', function(Blueprint $table)
		{
			//
		});
	}

}
