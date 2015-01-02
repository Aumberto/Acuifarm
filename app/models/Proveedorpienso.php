<?php 

	class Proveedorpienso extends Eloquent {

		protected $table = 'proveedores_pienso';
		protected $fillable = array('nombre', 'email', 'web', 'telefono', 'fax', 'direccion', 'cp', 'localidad', 'pais');
		protected $perPage = 5;

		public function piensos(){
			return $this->hasMany('Pienso', 'proveedor_id');
		}

		public function pellets(){
    	return $this->hasMany('Pellet', 'proveedor_pienso_id');
    }
	}

 ?>