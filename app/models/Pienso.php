<?php 

	class Pienso extends Eloquent{
 		
 		protected $table = 'piensos';
 		protected $fillable = array('codigo', 'nombre', 'proveedor_id', 'diametro_pellet_id', 'precio');
        protected $perPage = 100;
 		public function proveedor()
 		{
 			return $this->belongsTo('Proveedorpienso');
 		}

 		public function pellet()
 		{
 			return $this->belongsTo('Pellet', 'diametro_pellet_id');
 		}

	}

 ?>