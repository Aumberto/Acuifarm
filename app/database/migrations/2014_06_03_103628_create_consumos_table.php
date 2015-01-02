<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateConsumosTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('consumos', function(Blueprint $table)
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
			$table->integer('cantidad_recomendada');
			$table->integer('porcentaje_estrategia');
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
		Schema::drop('consumos', function(Blueprint $table)
		{
			//
		});
	}

}
