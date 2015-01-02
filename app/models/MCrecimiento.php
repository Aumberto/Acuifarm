<?php 

class MCrecimiento extends Eloquent{
	protected $table = 'm_crecimiento';
    protected $fillable = array('nombre', 'descripcion', 'SGR_a_lub', 'SGR_Peso_max', 'SGR_T_cero', 'SGR_T_max', 'SGR_Seno', 'FCR_cte', 'FCR_a');
}


 ?>