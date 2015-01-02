<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePedidosDetallesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('pedidos_detalles', function(Blueprint $table)
		{
			//
			$table->increments('id');
			$table->integer('pedido_id');
			$table->integer('pienso_id');
			$table->string('pienso');
			$table->string('codigopienso');
			$table->integer('cantidad');
			$table->decimal('precio', 10,2);
			$table->decimal('total', 10,2);
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
		Schema::drop('pedidos_detalles', function(Blueprint $table)
		{
			//
		});
	}

}
