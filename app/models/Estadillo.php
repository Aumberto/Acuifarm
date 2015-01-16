<?php 

	class Estadillo extends Eloquent{

		protected $table = 'estadillos';
		protected $fillable = array('jaula_id', 'fecha', 'num_tomas', 'porcentaje_primera_toma');

		public function jaula()
 		{
 			return $this->belongsTo('Jaula', 'jaula_id');
 		}

	}
 ?>