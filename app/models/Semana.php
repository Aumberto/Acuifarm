<?php 

	class Semana extends Eloquent{

		protected $table = 'semanas';
		protected $fillable = array('year', 'week', 'first_day', 'last_day');
		protected $perPage = 25;
	}

 ?>