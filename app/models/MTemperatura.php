<?php 

class MTemperatura extends Eloquent{
	protected $table = 'm_temperatura';
    protected $fillable = array('nombre', 'descripcion', 'enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'agoosto', 'septiembre', 'octubre', 'noviembre', 'diciembre');
}


 ?>