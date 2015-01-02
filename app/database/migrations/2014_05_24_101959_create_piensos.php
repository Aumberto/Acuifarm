<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePiensos extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('piensos', function(Blueprint $table)
		{
			//
			$table->increments('id');
			$table->string('codigo');
			$table->string('nombre');
			$table->integer('proveedor_id');
			$table->integer('diametro_pellet_id');
			$table->decimal('precio', 10,2);
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
		Schema::drop('piensos', function(Blueprint $table)
		{
			//
		});
	}

}
