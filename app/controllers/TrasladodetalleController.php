<?php 

	class TrasladodetalleController extends BaseController{

		public function getAdd(){
			$traslado_id = Input::get('traslado_id');
			$pienso_id = Input::get('pienso_id');
			$cantidad = Input::get('cantidad');

			$pienso = Pienso::find($pienso_id);
			

			// Comprobamos que el artículo que vamos a insertar no se encuentra ya en el pedido

			$count = TrasladoDetalle::where('traslado_id', '=', $traslado_id)->where('pienso_id', '=', $pienso_id)->count();
			if ($count){
                // Ya está el artículo, así que sumamos la nueva cantidad a la existente.
                $trasladodetalles = TrasladoDetalle::where('traslado_id', '=', $traslado_id)->where('pienso_id', '=', $pienso_id)->get();
                
                foreach($trasladodetalles as $trasladodetalle){
                     //echo $pedidodetalle->id;
                     $linea = TrasladoDetalle::find($trasladodetalle->id);
                     $nuevacantidad =  $cantidad + $linea->cantidad;
                     
                     // Actualizamos
                     $linea->cantidad = $nuevacantidad;
                     $linea->save();

                }
                 	
               
			}
			else
			{
				// No está, así que procedemos a añadir
				TrasladoDetalle::create(
				array(
					'traslado_id' => $traslado_id,
					'pienso_id' => $pienso_id,
					'pienso' => $pienso->nombre,
					'codigopienso' => $pienso->codigo,
					'cantidad' => $cantidad
					));
			}

			

			return Redirect::to('traslado/ver/'.$traslado_id);
		}

		public function getDelete($id){
			$trasladodetalle = TrasladoDetalle::find($id);
			$id_traslado = $trasladodetalle->traslado_id;
			$trasladodetalle->delete();

			return Redirect::to('traslado/ver/'. $id_traslado);
		}
	}
 ?>


