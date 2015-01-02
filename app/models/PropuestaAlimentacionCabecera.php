<?php 
class PropuestaAlimentacionCabecera extends Eloquent{
 		
 		protected $table = 'cabecera_propuesta_alimentacion';
 		protected $fillable = array('granja', 'granja_id', 'descripcion', 'fecha_ini', 'fecha_fin');
        protected $perPage = 10;
 		

	}

 ?>