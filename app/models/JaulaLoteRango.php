<?php 
	
	class JaulaLoteRango extends Eloquent{

		protected $table = 'jaula_lote_rango';
		protected $fillable = array('jaula_id', 'lote_id', 'cabecera_rango_id', 'fecha_inicio');

	}

 ?>