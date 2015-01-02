<?php 

class Traslado extends Eloquent{

    protected $table = 'traslados';
    protected $fillable = array('nombre', 'id_almacen_origen', 'id_almacen_destino', 'fecha_traslado', 'estado');

 public function almacenorigen(){
    	return $this->belongsTo('Almacen', 'id_almacen_origen');
    }   
 public function almacendestino(){
    	return $this->belongsTo('Almacen', 'id_almacen_destino');
    }  

  public function detalletraslado()
  {
  	return $this->hasMany('TrasladoDetalle', 'traslado_id');
  }
}

 ?>