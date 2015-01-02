<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProduccionRealTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('produccion_real', function(Blueprint $table)
		{
			//
			$table->increments('id');
			$table->date('date');
			$table->string('site');
			$table->string('unitname');
			$table->string('groupid');
			$table->integer('stock_count_ini');
			$table->decimal('stock_avg_ini', 10,2);
			$table->decimal('stock_bio_ini', 10,2);
			$table->integer('input_count');
			$table->decimal('input_avg', 10,2);
			$table->decimal('input_bio', 10,2);
			$table->integer('mortality_count');
			$table->decimal('mortality_avg', 10,2);
			$table->decimal('mortality_bio', 10,2);
			$table->integer('harvested_count');
			$table->decimal('harvested_avg', 10,2);
			$table->decimal('harvested_bio', 10,2);
			$table->integer('culling_count');
			$table->decimal('culling_avg', 10,2);
			$table->decimal('culling_bio', 10,2);
			$table->integer('deviation_count');
			$table->decimal('deviation_avg', 10,2);
			$table->decimal('deviation_bio', 10,2);
			$table->integer('stock_count_fin');
			$table->decimal('stock_avg_fin', 10,2);
			$table->decimal('stock_bio_fin', 10,2);
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
		Schema::drop('produccion_real', function(Blueprint $table)
		{
			//
		});
	}

}
