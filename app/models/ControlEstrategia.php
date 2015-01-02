<?php 

class ControlEstrategia extends Eloquent{

    protected $table = 'control_estrategias';
    protected $fillable = array('jaula_id', 'lote_id', 'fecha');

    public function jaula(){
    	return $this->belongsTo('Jaula');
    }

    public function lote(){
    	return $this->belongsTo('Lote');
    }

}

 ?>