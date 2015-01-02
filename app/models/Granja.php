<?php 

	class Granja extends Eloquent{

		protected $table = 'granjas';
		protected $fillable = array('nombre');

	 public function jaulas(){
    	return $this->hasMany('Jaula', 'granja_id');
    }
	}

 ?>