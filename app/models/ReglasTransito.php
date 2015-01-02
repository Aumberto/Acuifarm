<?php 

class Reglastransito extends Eloquent{

		protected $table = 'reglas_transito';
		protected $fillable = array('id_pienso_transito_inicial', 'id_pienso_transito_final');

	 
	}

 ?>