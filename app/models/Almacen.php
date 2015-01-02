<?php 
class Almacen extends Eloquent{

		protected $table = 'almacenes';
		protected $fillable = array('nombre', 'descripcion', 'capacidad_max');

	}
 ?>