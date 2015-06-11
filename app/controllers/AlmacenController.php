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
     	    
     	    $estado_almacen = DB::select('Select almacenes.nombre as almacen, piensos.id as id, piensos.nombre as pienso, 
     		                                       sum(cantidad) as cantidad, max(fecha)
     	                                    from movimientos_almacenes, almacenes, piensos
     	                                   where almacenes.id = movimientos_almacenes.almacen_id
     	                                     and piensos.id   = movimientos_almacenes.pienso_id
     	                                     and movimientos_almacenes.fecha <= ?
     	                                group by almacenes.nombre, piensos.id, piensos.nombre 
                                        having sum(cantidad) <> 0 
                                      order by almacenes.nombre, piensos.nombre desc', array($fecha));
          
          $almacen ='';
          $primerafila = True;
          $stock_almacen_array = array();
          $stock_almacen = array();
          foreach ($estado_almacen as $linea_stock) 
           {
             if ($almacen <> $linea_stock->almacen)
               {
                 if ($primerafila)
                   {
                     $primerafila = False;
                   }
                 else
                   {
                     $datos_almacen = array('almacen' => $almacen,
                                            'stock'   => $stock_almacen_array);
                     array_push($stock_almacen, $datos_almacen);
                     $stock_almacen_array = array();
                   }
                 $almacen = $linea_stock->almacen;
                 $datos_pienso = array( 'id'             => $linea_stock->id,
                                        'pienso'         => $linea_stock->pienso,
                                        'cantidad'       => $linea_stock->cantidad);
                 array_push($stock_almacen_array, $datos_pienso);
               }
             else
               {
                 $datos_pienso = array( 'id'             => $linea_stock->id,
                                        'pienso'         => $linea_stock->pienso,
                                        'cantidad'       => $linea_stock->cantidad);
                 array_push($stock_almacen_array, $datos_pienso);
               }
           }
          $datos_almacen = array('almacen' => $almacen,
                                 'stock'   => $stock_almacen_array);
          array_push($stock_almacen, $datos_almacen);
          

     	return View::make('almacen.stock')->with('estado_almacen', $estado_almacen)->with('fecha', $fecha->format('d-m-Y'))->with('stock_almacen', $stock_almacen);
     }

     public function AjusteAutomaticoAlmacenesPienso(){
          if (isset($_POST['fecha_fichero']))
           {
               $fecha = Input::get('fecha_fichero');
               list($dia, $mes, $year)=explode("-", $fecha);
               $fecha_fichero=$year."-".$mes."-".$dia;

               // Import a user provided file
               $file = Input::file('fichero')->getClientOriginalName();
               $ruta = Input::file('fichero')->getRealPath();
               Input::file('fichero')->move('public/', 'importacion.xls');

               $resultado_ajuste_stock = array() ;
               $prueba = 'Hola';
               Excel::load('public/importacion.xls', function($reader) use ($fecha_fichero, $resultado_ajuste_stock) {

                 // reader methods
                
                $result=$reader->get();
                foreach($result as $linea)
                  {
                    // Para cada linea, buscamos la cantidad de pienso en el almacén
                    $estado_almacen = DB::select('Select almacenes.nombre as almacen, piensos.id, piensos.nombre as pienso, 
                                             sum(cantidad) as cantidad, max(fecha)
                                              from movimientos_almacenes, almacenes, piensos
                                             where almacenes.id = movimientos_almacenes.almacen_id
                                               and piensos.id   = movimientos_almacenes.pienso_id
                                               and movimientos_almacenes.fecha <= ?
                                               and almacenes.nombre = ?
                                               and piensos.nombre = ?
                                          group by almacenes.nombre, piensos.id, piensos.nombre order by almacenes.nombre, piensos.nombre desc', array($fecha_fichero, $linea->almacen, $linea->alimento));

                    $nuevo_registro_importacion_stock = new ImportacionStock();
                    $nuevo_registro = new MovimientosAlmacen();
                    // Obtenemos el id del almacen
                    $objeto_almacen = Almacen::where('nombre', '=', $linea->almacen)->first();
                    // Obtenemos el id del pienso
                    $objeto_pienso = Pienso::where('nombre', '=', $linea->alimento)->first();

                    if (count($estado_almacen) < 1){
                         //echo 'No existe el pieso ' .$linea->alimento. ' en el almacen ' .$linea->almacen;
                         if (count($objeto_pienso)>0){
                              /*
                           $nuevo_registro_importacion_stock->almacen_id =$objeto_almacen->id;
                           $nuevo_registro_importacion_stock->pienso_id = $objeto_pienso->id;
                           $nuevo_registro_importacion_stock->cantidad_acuifarm = 0;
                           $nuevo_registro_importacion_stock->cantidad_fishtalk = $linea->cantidad;
                           $nuevo_registro_importacion_stock->diferencia = $linea->cantidad;
                           $nuevo_registro_importacion_stock->fecha = $fecha_fichero;
                           $nuevo_registro_importacion_stock->save();
                           */
                           
                           $nuevo_registro->almacen_id =$objeto_almacen->id;
                           $nuevo_registro->tipo_movimiento = "Entrada";
                           $nuevo_registro->descripcion ="Ajuste automático procedente de archivo";
                           $nuevo_registro->pienso_id =$objeto_pienso->id;
                           $nuevo_registro->cantidad =$linea->cantidad;
                           $nuevo_registro->fecha = $fecha_fichero;
                           $nuevo_registro->save();
                         }
                    } else {
                         foreach ($estado_almacen as $linea_almacen) {
                              /*
                              $nuevo_registro_importacion_stock->almacen_id =$objeto_almacen->id;
                              $nuevo_registro_importacion_stock->pienso_id = $objeto_pienso->id;
                              $nuevo_registro_importacion_stock->cantidad_acuifarm = $linea_almacen->cantidad;
                              $nuevo_registro_importacion_stock->cantidad_fishtalk = $linea->cantidad;
                              $nuevo_registro_importacion_stock->diferencia = $linea->cantidad - $linea_almacen->cantidad;
                              $nuevo_registro_importacion_stock->fecha = $fecha_fichero;
                              $nuevo_registro_importacion_stock->save();
                              */
                              $nuevo_registro->almacen_id =$objeto_almacen->id;
                              $nuevo_registro->tipo_movimiento = "Entrada";
                              $nuevo_registro->descripcion ="Ajuste automático procedente de archivo";
                              $nuevo_registro->pienso_id =$objeto_pienso->id;
                              $nuevo_registro->cantidad = $linea->cantidad - $linea_almacen->cantidad;
                              $nuevo_registro->fecha = $fecha_fichero;
                              $nuevo_registro->save();
                         }

                         
                    }
                    
                    
                  }
                
                  
               }); 
             
             //Obtenemos el resultado de la importacion
             //$resultado_ajuste_stock = ImportacionStock::where('fecha', '=', $fecha_fichero)->orderby('almacen_id')->get();
              //return View::make('almacen.ajuste_confirmacion')->with('fecha', $fecha_fichero)->with('resultado', $resultado_ajuste_stock);  
              return Redirect::to('almacenes/stock');
           }
          else 
           {
             return View::make('almacen.ajuste_automatico');
           }
     }

     public function ImportarStock(){
          if (isset($_POST['fecha_fichero']))
           {
               $fecha = Input::get('fecha_fichero');
               list($dia, $mes, $year)=explode("-", $fecha);
               $fecha_fichero=$year."-".$mes."-".$dia;

               // Import a user provided file
               $file = Input::file('fichero')->getClientOriginalName();
               $ruta = Input::file('fichero')->getRealPath();
               Input::file('fichero')->move('public/', 'importacion.xls');

               // Obtenemos la granja
               $granja = Input::get('granja');

               $resultado_ajuste_stock = array() ;
               //$prueba = 'Hola';
               Excel::load('public/importacion.xls', function($reader) use ($fecha_fichero, $resultado_ajuste_stock, $granja) {

                 // reader methods
                 // Probar esto: 
                $result= $reader->get()->groupBy('nombre')->groupby('Tipo_de_alimento');
                //$result=$reader->get();
                //print_r($result);
                foreach($result as $almacen)
                  { 
                    //echo 'Nuevo almacen';
                    foreach ($almacen as $tipo_alimento)

                    {
                      echo ' nuevo tipo de pienso';
                      $nombre_almacen = '';
                      $nombre_alimento = '';
                      $cantidad = 0;
                      $primera_fila = True;
                       foreach ( $tipo_alimento as $linea)
                       {
                         //print_r($linea);
                         if ($nombre_alimento <> $linea->tipo_de_alimento)
                         {
                           if (!$primera_fila)
                            {
                               echo ' Alimento: ' . $nombre_alimento . ' Almacén: ' . $nombre_almacen . ' cantidad: ' . $cantidad;
                               // Aquí debemos actualizar los datos
                               switch ($nombre_almacen) {
                                 case 'MeH':
                                    $almacen = 'Martín e Hijos';
                                   break;
                                 
                                 case 'Salinetas':
                                   if ($granja == 'Melenara')
                                    {
                                       $almacen = 'Melenara';
                                    } 
                                   else 
                                    {
                                       $almacen = 'Procria';
                                    }
                                   break;
                               }

                               // Para cada linea, buscamos la cantidad de pienso en el almacén
                               $estado_almacen = DB::select('Select almacenes.nombre as almacen, piensos.id, piensos.nombre as pienso, 
                                                                    sum(cantidad) as cantidad, max(fecha)
                                                               from movimientos_almacenes, almacenes, piensos
                                                              where almacenes.id = movimientos_almacenes.almacen_id
                                                                and piensos.id   = movimientos_almacenes.pienso_id
                                                                and movimientos_almacenes.fecha <= ?
                                                                and almacenes.nombre = ?
                                                                and piensos.nombre = ?
                                                           group by almacenes.nombre, piensos.id, piensos.nombre order by almacenes.nombre, piensos.nombre desc', array($fecha_fichero, $almacen, $nombre_alimento));

                               $nuevo_registro_importacion_stock = new ImportacionStock();
                               $nuevo_registro = new MovimientosAlmacen();
                               // Obtenemos el id del almacen
                               $objeto_almacen = Almacen::where('nombre', '=', $almacen)->first();
                               // Obtenemos el id del pienso
                               $objeto_pienso = Pienso::where('nombre', '=', $nombre_alimento)->first();

                               if (count($estado_almacen) < 1)
                                {
                                 //echo 'No existe el pieso ' .$linea->alimento. ' en el almacen ' .$linea->almacen;
                                 if (count($objeto_pienso)>0)
                                  {
                                    $nuevo_registro->almacen_id =$objeto_almacen->id;
                                    $nuevo_registro->tipo_movimiento = "Entrada";
                                    $nuevo_registro->descripcion ="Ajuste automático procedente de archivo";
                                    $nuevo_registro->pienso_id =$objeto_pienso->id;
                                    $nuevo_registro->cantidad =$cantidad;
                                    $nuevo_registro->fecha = $fecha_fichero;
                                    $nuevo_registro->save();
                                  }
                                } 
                               else 
                                {
                                  foreach ($estado_almacen as $linea_almacen) 
                                   {
                                     $nuevo_registro->almacen_id =$objeto_almacen->id;
                                     $nuevo_registro->tipo_movimiento = "Entrada";
                                     $nuevo_registro->descripcion ="Ajuste automático procedente de archivo";
                                     $nuevo_registro->pienso_id =$objeto_pienso->id;
                                     $nuevo_registro->cantidad = $cantidad - $linea_almacen->cantidad;
                                     $nuevo_registro->fecha = $fecha_fichero;
                                     $nuevo_registro->save();
                                   }
                                }

                               $nombre_almacen = $linea->nombre;
                               $nombre_alimento = $linea->tipo_de_alimento;
                               $cantidad = $linea->amount_kg;
                            }
                            else
                            {
                               $nombre_almacen = $linea->nombre;
                               $nombre_alimento = $linea->tipo_de_alimento;
                               $cantidad = $linea->amount_kg;
                               $primera_fila = False;
                            }
                          }
                          else
                          {  
                            $cantidad = $cantidad + $linea->amount_kg;
                            
                         }
                         /*$nombre_almacen = $linea->nombre;
                         $nombre_alimento = $linea->tipo_de_alimento;
                         $cantidad = $cantidad + $linea->amount_kg;
                         echo ' Alimento: ' . $nombre_alimento; */

                       }
                      //echo 'Almacén: ' . $nombre_almacen . ' Alimento: ' . $nombre_alimento . ' cantidad: ' . $cantidad;
                    }
                    //echo 'Almacén ' . $nombre_almacen . ' Alimento: ' ; //. $linea->tipo_de_alimento . ' cantidad: '; //. $linea->Amount_[kg];
                    /*
                    
                    
                   */ 
                  }
                
                  
               }); 
             
             
              return Redirect::to('almacenes/stock');
           }
          else 
           {
             return View::make('almacen.importarstock');
           }
     }

     
 }
 ?>