<?php 

class Pedido extends Eloquent{

    protected $table = 'pedidos';
    protected $fillable = array('num_pedido', 'num_contenedor', 'proveedor_id', 'importe', 'pagado', 'estado', 'fecha_pedido', 'fecha_confirmacion', 'fecha_carga', 'fecha_llegada', 'fecha_descarga', 'fecha_pago');

    public function proveedor(){
    	return $this->belongsTo('Proveedorpienso');
    }

    public function detallepedido(){
    	return $this->hasMany('PedidoDetalle', 'pedido_id');
    }

}

 ?>