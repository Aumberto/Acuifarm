<?php 

	Class Pellet extends Eloquent
	{
		protected $table = 'tamanio_pellets';
		protected $fillable = array('diametro', 'pm_min', 'pm_max', 'transito', 'proveedor_pienso_id');

		public function proveedor()
 		{
 			return $this->belongsTo('Proveedorpienso', 'granja_id');
 		}

	}

 ?>