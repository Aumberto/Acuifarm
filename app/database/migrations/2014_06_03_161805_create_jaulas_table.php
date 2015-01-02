<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateJaulasTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('jaulas', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('nombre');
			$table->decimal('diametro', 10,2);
			$table->integer('granja_id');
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
		Schema::drop('jaulas', function(Blueprint $table)
		{
			//
		});
	}

}
