<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMCrecimientoTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('m_crecimiento', function(Blueprint $table)
		{
			//
			$table->increments('id');
			$table->string('nombre');
			$table->string('descripcion');
			$table->decimal('SGR_a_lub', 5,2);
			$table->decimal('SGR_Peso_max', 8,2);
			$table->decimal('SGR_T_cero', 5,2);
			$table->decimal('SGR_T_max', 5,2);
			$table->decimal('SGR_Seno', 5,2);
			$table->decimal('FCR_cte', 5,2);
			$table->decimal('FCR_a', 10,2);
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
		Schema::drop('m_crecimiento', function(Blueprint $table)
		{
			//
		});
	}

}
