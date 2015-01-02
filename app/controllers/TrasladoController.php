<?php 

class TrasladoController extends BaseController{

  public function getIndex()
  {

  		$traslados = Traslado::orderby('fecha_traslado')
                             ->orderby('id_almacen_origen')
                             ->orderby('id_almacen_destino')
                             ->get();
  		return View::make('traslado.traslado_list')->with('traslados', $traslados);
  }

  public function getAdd()
  {
      $almacenes = Almacen::all();
      return View::make('traslado.traslado_add')->with('almacenes', $almacenes);
  }

  public function getNew()
  {
    $fecha=Input::get('fecha_descarga');
    list($dia, $mes, $year)=explode("-", $fecha);
    $fecha_descarga=$year."-".$mes."-".$dia;
       
       Traslado::create(
        array(
          'nombre' => Input::get('trasladonombre'),
          'id_almacen_origen' => Input::get('almacenorigen'),
          'id_almacen_destino' => Input::get('almacendestino'),
          'fecha_traslado' => $fecha_descarga, 
          'estado' => 'En tránsito',
          
          ));

       // Obtenemos el id del último pedido insertado
       $traslado = Traslado::all();

       

       return Redirect::to('traslado/ver/'.$traslado->last()->id);
  }

  public function getVer($id)
  {
    $traslado = Traslado::find($id);
    $detalles = Traslado::find($id)->detalletraslado;
     
     
     $piensos = Pienso::orderby('proveedor_id')->orderby('diametro_pellet_id')->orderby('nombre')->get();

   
    

     $traslado_total_cantidad=TrasladoDetalle::with('traslado')->where('traslado_id','=',$id)->sum('cantidad');
     // Obtenemos el listado de piensos de dicho proveedor de pienso

     return View::make('traslado.traslado_ver')
                ->with('traslado', $traslado)
                ->with('piensos', $piensos)
                ->with('detalles', $detalles)
                ->with('trasladototalcantidad', $traslado_total_cantidad);
  }

  public function getEntrada($id)
  {
    // Localizar los datos del traslado.
    $traslado = Traslado::find($id);

    // Localizar todas las lineas del traslado.
    $detallestraslado = TrasladoDetalle::where('traslado_id', '=', $id)->get();

    // Para cada linea, realizamos una salida en el almacén de origen y una entrada en el almacén de destino.
    foreach($detallestraslado as $detalletraslado)
     {
      //Realizamos la salida del almacén origen
      $nuevo_registro = new MovimientosAlmacen();
      $nuevo_registro->almacen_id =$traslado->id_almacen_origen;
      $nuevo_registro->tipo_movimiento = 'Salida';
      $nuevo_registro->descripcion ='Traslado '. $traslado->nombre;
      $nuevo_registro->pienso_id =$detalletraslado->pienso_id;
      $nuevo_registro->cantidad = ($detalletraslado->cantidad)* -1;
      $nuevo_registro->fecha = $traslado->fecha_traslado;
      $nuevo_registro->save();

      //Realizamos la entrada al almacén destino
      $nuevo_registro = new MovimientosAlmacen();
      $nuevo_registro->almacen_id =$traslado->id_almacen_destino;
      $nuevo_registro->tipo_movimiento = 'Entrada';
      $nuevo_registro->descripcion ='Traslado '. $traslado->nombre;
      $nuevo_registro->pienso_id =$detalletraslado->pienso_id;
      $nuevo_registro->cantidad = ($detalletraslado->cantidad);
      $nuevo_registro->fecha = $traslado->fecha_traslado;
      $nuevo_registro->save();


     }
     // Actualizamos el estado del traslado a 'Descargado'
     $traslado->estado ='Descargado';
     $traslado->save();
     return Redirect::to('traslado');
  }

  public function getDelete($id)
  {
     //Borramos todos los detalle del traslado
      $detallestraslado = TrasladoDetalle::where('traslado_id', '=', $id)->get();
      foreach($detallestraslado as $detalletraslado)
     {
        $detalletraslado->delete();
     }

     // Borramos la cabecera del traslado
     $traslado = Traslado::find($id);
     $traslado->delete();

     return Redirect::to('traslado');
  }

  
}

 ?>