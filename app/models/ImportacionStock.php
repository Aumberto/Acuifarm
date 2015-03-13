<?php 

	Class ImportacionStock extends Eloquent
	{
		protected $table = 'importacion_stock';
		protected $fillable = array('almacen_id', 'pienso_id', 'cantidad_acuifarm', 'cantidad_fishtalk', 'diferencia', 'fecha');

		public function almacen()
 		{
 			return $this->belongsTo('Almacen', 'almacen_id');
 		}

 		public function pienso()
 		{
 			return $this->belongsTo('Pienso', 'pienso_id');
 		}

	}

 ?>