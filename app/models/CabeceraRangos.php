<?php 
	
	class CabeceraRangos extends Eloquent{

		protected $table = 'cabecera_rangos';
		protected $fillable = array('nombre', 'descripcion');

	    public function detallerango(){
    	   return $this->hasMany('DetalleRangos', 'cabecera_rango_id');
        }
	}


 ?>