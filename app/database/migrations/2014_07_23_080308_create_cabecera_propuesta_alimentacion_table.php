<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCabeceraPropuestaAlimentacionTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('cabecera_propuesta_alimentacion', function(Blueprint $table)
		{
			//
			$table->increments('id');
			$table->string('granja');
			$table->integer('granja_id');
			$table->string('descripcion');
			$table->date('fecha_ini');
			$table->date('fecha_fin');
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
		Schema::table('cabecera_propuesta_alimentacion', function(Blueprint $table)
		{
			//
		});
	}

}
