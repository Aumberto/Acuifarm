<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePedidosTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('pedidos', function(Blueprint $table)
		{
			//
			$table->increments('id');
			$table->string('num_pedido');
			$table->string('num_contenedor');
			$table->integer('proveedor_id');
			$table->decimal('importe', 10,2);
			$table->boolean('pagado');
			$table->enum('estado', array('En preparación', 'En tránsito', 'Pendiente de descarga', 'Descargado'));
			$table->date('fecha_pedido');
			$table->date('fecha_confirmacion');
			$table->date('fecha_carga');
			$table->date('fecha_llegada');
			$table->date('fecha_descarga');
			$table->date('fecha_pago');
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
		Schema::drop('pedidos', function(Blueprint $table)
		{
			//
		});
	}

}
