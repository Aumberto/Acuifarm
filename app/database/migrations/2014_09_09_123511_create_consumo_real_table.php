<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateConsumoRealTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('consumo_real', function(Blueprint $table)
		{
			//
			$table->increments('id');
			$table->integer('granja_id');
			$table->string('granja');
			$table->integer('jaula_id');
			$table->string('jaula');
			$table->integer('lote_id');
			$table->string('lote');
			$table->integer('proveedor_id');
			$table->string('proveedor');
			$table->integer('pienso_id');
			$table->string('pienso');
			$table->string('codigo_pienso');
			$table->decimal('diametro_pienso', 10, 2);
			$table->integer('diametro_pienso_id');
			$table->integer('cantidad');
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
		Schema::drop('consumo_real', function(Blueprint $table)
		{
			//
		});
	}

}
