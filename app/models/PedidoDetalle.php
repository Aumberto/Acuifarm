<?php 

	class PedidoDetalle extends Eloquent{

		protected $table = 'pedidos_detalles';
		protected $fillable = array('pedido_id', 'pienso_id', 'pienso', 'codigopienso', 'cantidad', 'precio', 'total');

		public function pedido(){
			return $this->belongsTo('Pedido');
		}

	}

 ?>