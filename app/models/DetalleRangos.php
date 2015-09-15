<?php 
	
	class DetalleRangos extends Eloquent{

		protected $table = 'detalle_rangos';
		protected $fillable = array('cabecera_rango_id', 'tamanio_pellet_id', 'pm_min', 'pm_max', 'pm_transito');

		public function pellet(){
    	   return $this->belongsTo('Pellet', 'tamanio_pellet_id');
        }

	}


 ?>