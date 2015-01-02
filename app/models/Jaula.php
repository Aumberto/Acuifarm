<?php 

	class Jaula extends Eloquent{

		protected $table = 'jaulas';
		protected $fillable = array('nombre, diametro, granja_id');

		public function granja()
 		{
 			return $this->belongsTo('Granja', 'granja_id');
 		}

	}
 ?>