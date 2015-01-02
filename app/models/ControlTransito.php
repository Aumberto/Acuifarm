<?php 

class Controltransito extends Eloquent{

		protected $table = 'control_transito';
		protected $fillable = array('jaula_id', 'jaula', 'lote_id', 'lote', 'id_regla_transito', 'id_pienso_transito_inicial', 'id_pienso_transito_final', 'fecha_inicial', 'fecha_final');

	 
	}
 ?>