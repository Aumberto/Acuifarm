<?php 
 class AlmacenController extends BaseController
 {
     public function InsertarMovimiento()
     {
     	if (isset($_POST['tipo_movimiento']))
     	{
     		//echo 'Tenemos que procesar el formulario';
     		$fecha = Input::get('fecha_movimiento');
     		list($dia, $mes, $year)=explode("-", $fecha);
            $fecha_movimiento=$year."-".$mes."-".$dia;
     		$tipo_movimiento = Input::get('tipo_movimiento');
     		$almacen_id= Input::get('almacen_id');
     		$descripcion= Input::get('descripcion');
     		$pienso_id= Input::get('pienso_id');
     		$cantidad= Input::get('cantidad');

     		if ($tipo_movimiento == 'Salida')
     		{
     			$cantidad = $cantidad * -1;
     		}

     		// Creamos un nuevo objeto movimiento de almacén.
     		$nuevo_registro = new MovimientosAlmacen();
     		$nuevo_registro->almacen_id =$almacen_id;
     		$nuevo_registro->tipo_movimiento = $tipo_movimiento;
     		$nuevo_registro->descripcion =$descripcion;
     		$nuevo_registro->pienso_id =$pienso_id;
     		$nuevo_registro->cantidad =$cantidad;
     		$nuevo_registro->fecha = $fecha_movimiento;
     		$nuevo_registro->save();
     		//echo 'Aparcao!!!!!';
     		return Redirect::to('almacenes/stock');
     	}
     	else
     	{
     		//echo 'Tenemos que mostrar el formulario';
     		// Listamos todos los almacenes disponibles
     		$almacenes = Almacen::orderby('nombre')->get();
     		// Listamos todos los tipos de pienso disponible
     		$piensos = Pienso::orderby('proveedor_id')->orderby('diametro_pellet_id')->get();
     		return View::make('almacen.movimiento_add')->with('almacenes', $almacenes)
     		                                           ->with('piensos', $piensos);
     	}
     }

     public function stock()
     {
          // Obtenemos la fecha de la última importación de datos reales
          $fecha_ultima_actualizacion = ProduccionReales::orderby('date', 'desc')->first();

          $fecha = new DateTime($fecha_ultima_actualizacion->date);
     	//$fecha='2014-10-07';
     	$estado_almacen = DB::select('Select almacenes.nombre as almacen, piensos.id, piensos.nombre as pienso, 
     		                              sum(cantidad) as cantidad, max(fecha)
     	                                    from movimientos_almacenes, almacenes, piensos
     	                                   where almacenes.id = movimientos_almacenes.almacen_id
     	                                     and piensos.id   = movimientos_almacenes.pienso_id
     	                                     and movimientos_almacenes.fecha <= ?
     	                                group by almacenes.nombre, piensos.id, piensos.nombre order by almacenes.nombre, piensos.nombre desc', array($fecha));

     	return View::make('almacen.stock')->with('estado_almacen', $estado_almacen)->with('fecha', $fecha->format('d-m-Y'));
     }

     
 }
 ?>