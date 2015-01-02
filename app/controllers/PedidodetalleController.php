<?php 

	class PedidodetalleController extends BaseController{

		public function getAdd(){
			$pedido_id = Input::get('pedido_id');
			$pienso_id = Input::get('pienso_id');
			$cantidad = Input::get('cantidad');

			$pienso = Pienso::find($pienso_id);
			$total = $cantidad*$pienso->precio;

			// Comprobamos que el artículo que vamos a insertar no se encuentra ya en el pedido

			$count = PedidoDetalle::where('pedido_id', '=', $pedido_id)->where('pienso_id', '=', $pienso_id)->count();
			if ($count){
                // Ya está el artículo, así que sumamos la nueva cantidad a la existente.
                $pedidodetalles = PedidoDetalle::where('pedido_id', '=', $pedido_id)->where('pienso_id', '=', $pienso_id)->get();
                
                foreach($pedidodetalles as $pedidodetalle){
                     echo $pedidodetalle->id;
                     $linea = PedidoDetalle::find($pedidodetalle->id);
                     $nuevacantidad =  $cantidad + $linea->cantidad;
                     $total = $nuevacantidad * $pienso->precio;
                     // Actualizamos
                     $linea->cantidad = $nuevacantidad;
                     $linea->total = $total;
                     $linea->save();

                }
                 	
               
			}else{
				// No está, así que procedemos a añadir
				PedidoDetalle::create(
				array(
					'pedido_id' => $pedido_id,
					'pienso_id' => $pienso_id,
					'pienso' => $pienso->nombre,
					'codigopienso' => $pienso->codigo,
					'cantidad' => $cantidad,
					'precio' => $pienso->precio,
					'total' => $total
					));
			}

			

			return Redirect::to('pedido/ver/'.$pedido_id);
		}

		public function getDelete($id){
			$pedidodetalle = PedidoDetalle::find($id);
			$id_pedido = $pedidodetalle->pedido_id;
			$pedidodetalle->delete();

			return Redirect::to('pedido/ver/'. $id_pedido);
		}
	}
 ?>


