<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSemanasTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('semanas', function(Blueprint $table)
		{
			//
			$table->increments('id');
			$table->string('year');
			$table->string('week');
			$table->date('first_day');
			$table->date('last_day');
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
		Schema::drop('semanas', function(Blueprint $table)
		{
			//
		});
	}

}
