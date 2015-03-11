<?php 
 class EstadilloController extends BaseController
 {
     public function getIndex()
     {
     	// Obtenemos los datos simulados del día actual
        $fecha = Input::get('fecha_pedido');
        if (!isset($fecha)) 
        {
             //echo "Esta variable no está definida, así que se imprimirá";
             $fecha_estadillo = date("Y-m-d");
          }
          else
          {
              //$fecha = "2014-12-20";
              //$fecha=Input::get('fecha_pedido');
              list($dia, $mes, $year)=explode("-", $fecha);
              $fecha_estadillo=$year."-".$mes."-".$dia;
          }
          // Buscamos si existe estadillo para ese día.
          // Localizamos las jaulas que dicho día comen.
          $jaulas_alimentación = ProduccionSimuladas::where('date', '=', $fecha_estadillo)->where('ayuno','=', '0')->get();
          //Con cada una de las jaulas, comprobamos si hay registro de estadillos.
          foreach ($jaulas_alimentación as $jaulas) 
           {
             $jaula = Jaula::where('nombre', '=', $jaulas->unitname)->first();
             $estadillo = Estadillo::where('jaula_id', '=', $jaula->id)->where('fecha', '=', $fecha_estadillo)->get();
             if (count($estadillo) == 0)
              {
                $nuevo_estadillo = new Estadillo();
                $nuevo_estadillo->jaula_id = $jaula->id;
                $nuevo_estadillo->fecha    = $fecha_estadillo;
                // Buscamos que tipo de grano come
                $consumo_simulado = Consumo::where('jaula_id', '=', $jaula->id)->where('fecha', '=', $fecha_estadillo)->orderby('diametro_pienso')->first();
                if ($consumo_simulado->diametro_pienso <= 4.5) {
                  $nuevo_estadillo->num_tomas= 2;
                  $nuevo_estadillo->porcentaje_primera_toma=50;
                } else { 
                  $nuevo_estadillo->num_tomas= 1;
                  $nuevo_estadillo->porcentaje_primera_toma=100;
                }
                
                $nuevo_estadillo->save();
              }
           }
          // Si no existe, lo creamos
          $datos_estadillos = DB::select('select j.nombre as jaula, g.nombre as granja, ifnull(ps.groupid,"-") as lote, ifnull(ps.stock_count_ini,0) as stock_count_ini, 
                                                 ifnull(ps.stock_avg_ini,0) as stock_avg_ini, ifnull(ps.stock_bio_ini,0) as stock_bio_ini, ifnull(ps.cantidad_toma,0) as cantidad_toma, 
                                                 ifnull(c.pienso,"-") as pienso, ifnull(c.diametro_pienso,"-") as diametro_pienso, ifnull(c.cantidad,0) as cantidad,
                                                 ifnull(e.num_tomas, 0) as num_tomas, ifnull(e.porcentaje_primera_toma, 0) as porcentaje_primera_toma, e.id as estadillo_id
                                            from jaulas j left join produccion_simulado ps on j.nombre = ps.unitname and ps.date =  ? 
                                                 left join consumos c on j.nombre = c.jaula and c.fecha =  ? 
                                                 left join estadillos e on j.id = e.jaula_id and e.fecha = ? , granjas g
                                           where j.granja_id = g.id
                                        order by j.granja_id, j.nombre, c.diametro_pienso', array($fecha_estadillo, $fecha_estadillo, $fecha_estadillo));
          $granja = '';
          $jaula = '';
          $primerafila = True;
          $estadillos_granja = array();
          $estadillos_granja_jaula = array();
          $estadillos_granja_jaula_array = array();
          $jaula_consumos = array();

          foreach ($datos_estadillos as $linea_estadillo) 
          {
            if ($granja <> $linea_estadillo->granja)
             {
                if ($primerafila)
                 {
                    $primerafila = False;
                    $jaula_consumos = array();
                    $datos_consumo = array('pellet' => $linea_estadillo->diametro_pienso,
                                           'cantidad' => $linea_estadillo->cantidad);
                    array_push($jaula_consumos, $datos_consumo);
                    $jaula = $linea_estadillo->jaula;
                    $granja = $linea_estadillo->granja;
                    $lote = $linea_estadillo->lote;
                    $stock_count_ini = $linea_estadillo->stock_count_ini;
                    $stock_avg_ini = $linea_estadillo->stock_avg_ini;
                    $stock_bio_ini = $linea_estadillo->stock_bio_ini;
                    $cantidad_toma = $linea_estadillo->cantidad_toma;
                    $num_tomas = $linea_estadillo->num_tomas;
                    $porcentaje_primera_toma = $linea_estadillo->porcentaje_primera_toma;
                    $estadillo_id = $linea_estadillo->estadillo_id;

                 }
                else
                 {
                    $datos_jaula = array('jaula' => $jaula,
                                      'lote' => $lote,
                                      'consumos' => $jaula_consumos,
                                      'stock_count_ini' => $stock_count_ini,
                                      'stock_avg_ini' => $stock_avg_ini,
                                      'stock_bio_ini' => $stock_bio_ini,
                                      'cantidad_toma' => $cantidad_toma,
                                      'num_tomas' => $num_tomas, 
                                      'porcentaje_primera_toma' => $porcentaje_primera_toma, 
                                      'estadillo_id' => $estadillo_id);
                    array_push($estadillos_granja_jaula, $datos_jaula);
                    $datos_granjas = array('granja' => $granja,
                                           'jaulas' => $estadillos_granja_jaula);
                    array_push($estadillos_granja, $datos_granjas);
                    $estadillos_granja_jaula = array();
                    $jaula_consumos = array();
                    $datos_consumo = array('pellet' => $linea_estadillo->diametro_pienso,
                                           'cantidad' => $linea_estadillo->cantidad);
                    array_push($jaula_consumos, $datos_consumo);
                    //echo 'Cambio de granja: granja actual ' . $granja . ' nueva granja: ' . $linea_estadillo->granja;
                    //print_r($datos_granjas) ;
                    $jaula = $linea_estadillo->jaula;
                    $granja = $linea_estadillo->granja;
                    $lote = $linea_estadillo->lote;
                    $stock_count_ini = $linea_estadillo->stock_count_ini;
                    $stock_avg_ini = $linea_estadillo->stock_avg_ini;
                    $stock_bio_ini = $linea_estadillo->stock_bio_ini;
                    $cantidad_toma = $linea_estadillo->cantidad_toma;
                    $num_tomas = $linea_estadillo->num_tomas;
                    $porcentaje_primera_toma = $linea_estadillo->porcentaje_primera_toma;
                    $estadillo_id = $linea_estadillo->estadillo_id;

                 }
                
             }
            else
             {
              if ($jaula <> $linea_estadillo->jaula)
               {
                 $datos_jaula = array('jaula' => $jaula,
                                      'lote' => $lote,
                                      'consumos' => $jaula_consumos,
                                      'stock_count_ini' => $stock_count_ini,
                                      'stock_avg_ini' => $stock_avg_ini,
                                      'stock_bio_ini' => $stock_bio_ini,
                                      'cantidad_toma' => $cantidad_toma,
                                      'num_tomas' => $num_tomas, 
                                      'porcentaje_primera_toma' => $porcentaje_primera_toma, 
                                      'estadillo_id' => $estadillo_id);
                 array_push($estadillos_granja_jaula, $datos_jaula);
                 $jaula_consumos = array();
                 //$estadillos_granja_jaula = array();
                 $datos_consumo = array('pellet' => $linea_estadillo->diametro_pienso,
                                        'cantidad' => $linea_estadillo->cantidad);
                 array_push($jaula_consumos, $datos_consumo);
                 //echo 'Nueva jaula: actual ' . $jaula . ' nueva: ' . $linea_estadillo->jaula;
                 $jaula = $linea_estadillo->jaula;
                 $lote = $linea_estadillo->lote;
                 $stock_count_ini = $linea_estadillo->stock_count_ini;
                 $stock_avg_ini = $linea_estadillo->stock_avg_ini;
                 $stock_bio_ini = $linea_estadillo->stock_bio_ini;
                 $cantidad_toma = $linea_estadillo->cantidad_toma;
                 $num_tomas = $linea_estadillo->num_tomas;
                 $porcentaje_primera_toma = $linea_estadillo->porcentaje_primera_toma;
                 $estadillo_id = $linea_estadillo->estadillo_id;
               }
               else
               {
                $datos_consumo = array('pellet' => $linea_estadillo->diametro_pienso,
                                       'cantidad' => $linea_estadillo->cantidad);
                array_push($jaula_consumos, $datos_consumo);
               }

             }  
                      
          }
          $datos_jaula = array('jaula' => $jaula,
                                      'lote' => $lote,
                                      'consumos' => $jaula_consumos,
                                      'stock_count_ini' => $stock_count_ini,
                                      'stock_avg_ini' => $stock_avg_ini,
                                      'stock_bio_ini' => $stock_bio_ini,
                                      'cantidad_toma' => $cantidad_toma,
                                      'num_tomas' => $num_tomas, 
                                      'porcentaje_primera_toma' => $porcentaje_primera_toma, 
                                      'estadillo_id' => $estadillo_id);
          array_push($estadillos_granja_jaula, $datos_jaula);
          $datos_granja = array('granja' => $granja,
                                'jaulas' =>  $estadillos_granja_jaula);
          array_push($estadillos_granja, $datos_granja); 
          //print_r($estadillos_granja);
          return View::make('estadillo.estadillo_diario')->with('fecha', $fecha_estadillo)
                                                         ->with('datos', $datos_estadillos)
                                                         ->with('estadillos_granja', $estadillos_granja);
     }

     

     
 }
 ?>