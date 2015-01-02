<?php 

class ConsumosReales extends Eloquent{
   
   protected $table = 'consumo_real';
   protected $fillable = array('granja_id', 'granja', 'jaula_id', 'jaula', 'lote_id', 'lote', 'proveedor_id', 
   	                           'proveedor', 'pienso_id', 'pienso', 'codigo_pienso', 'diametro_pienso', 
   	                           'diametro_pienso_id', 'cantidad', 'fecha');

}
 ?>