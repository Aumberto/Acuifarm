<?php 
	
	class Consumo extends Eloquent{

		protected $table = 'consumos';
		protected $fillable = array('granja_id', 'granja', 'jaula_id', 'jaula', 'lote_id', 'lote', 'proveedor_id', 'proveedor', 'pienso_id', 'pienso', 'codigo_pienso', 'diametro_pienso', 'cantidad_recomendada', 'porcentaje_estrategia', 'cantidad', 'fecha');

	}

 ?>