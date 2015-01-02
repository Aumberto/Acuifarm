<?php 
class MovimientosAlmacen extends Eloquent{

		protected $table = 'movimientos_almacenes';
		protected $fillable = array('almacen_id', 'tipo_movimiento', 'descripcion', 'pienso_id', 'cantidad', 'fecha');

	}
 ?>