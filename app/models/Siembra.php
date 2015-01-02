<?php 

   Class Siembra extends Eloquent {

   	  protected $table = 'siembras';
   	  protected $fillable = array('granja_id', 'jaula_id', 'lote_id', 'cabecera_rangos_id', 'input_count', 'input_avg', 'input_bio', 'fecha');

    public function granja(){
    	return $this->belongsTo('Granja');
    }

    public function jaula(){
    	return $this->belongsTo('Jaula');
    }

    public function cabecerarangos(){
    	return $this->belongsTo('CabeceraRangos', 'cabecera_rangos_id');
    }

    public function lote(){
    	return $this->belongsTo('Lote', 'lote_id');
    }
   	  
   }

 ?>