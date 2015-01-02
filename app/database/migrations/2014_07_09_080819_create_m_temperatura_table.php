<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMTemperaturaTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('m_temperatura', function(Blueprint $table)
		{
			//
			$table->increments('id');
			$table->string('nombre');
			$table->string('descripcion');
			$table->decimal('enero', 5,2);
			$table->decimal('febrero', 5,2);
			$table->decimal('marzo', 5,2);
			$table->decimal('abril', 5,2);
			$table->decimal('mayo', 5,2);
			$table->decimal('junio', 5,2);
			$table->decimal('julio', 5,2);
			$table->decimal('agosto', 5,2);
			$table->decimal('septiembre', 5,2);
			$table->decimal('octubre', 5,2);
			$table->decimal('noviembre', 5,2);
			$table->decimal('diciembre', 5,2);
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
		Schema::drop('m_temperatura', function(Blueprint $table)
		{
			//
		});
	}

}
