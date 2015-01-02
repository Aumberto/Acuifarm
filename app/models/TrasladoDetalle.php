<?php 

	class TrasladoDetalle extends Eloquent{

		protected $table = 'traslados_detalles';
		protected $fillable = array('traslado_id', 'pienso_id', 'pienso', 'codigopienso', 'cantidad');

		public function traslado(){
			return $this->belongsTo('Traslado');
		}

	}

 ?>