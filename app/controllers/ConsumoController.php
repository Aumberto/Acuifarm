<?php 

	class ConsumoController extends BaseController{

		public function getIndex(){

			$granjas = Granja::all();
             //echo $granjas->jaulas;
             //echo $granjas->jaulas();
            return View::make('consumo.consumo_list')->with('granjas', $granjas);
            //foreach ($granjas as $granja){
            //    return View::make('consumo.consumo_granja_list')->with('granja', $granja);
            //}
			

		}

        public function getSemanales()
        {

            //echo 'Hola';
            $fecha_inicial = new DateTime('2014-09-01');
            $fecha_final   = new DateTime('2014-09-07');
            //echo $fecha_inicial->format('Y-m-d') . ' ' . $fecha_final->format('Y-m-d');
            $primera_fila = 0;
            $j = 3;
            $semanas = array();
            for ($x = 0; $x < 8; $x++)
            {
                array_push($semanas, $fecha_inicial->format('W'));
                //echo ' ' . $fecha_inicial->format('Y-m-d') . ' ' . $fecha_final->format('Y-m-d');
                $consumos = DB::select('Select week(consumos.fecha, 3) as semanas, tamanio_pellets.proveedor_pienso_id, consumos.proveedor_id, 
                                           proveedores_pienso.nombre,  tamanio_pellets.diametro, ifnull(sum(consumos.cantidad),0) as cantidad
                                      from consumos right join tamanio_pellets on consumos.proveedor_id =  tamanio_pellets.proveedor_pienso_id 
                                                                              and consumos.diametro_pienso = tamanio_pellets.diametro 
                                                                              and consumos.fecha >= ? 
                                                                              and consumos.fecha <= ?
                                                     inner join proveedores_pienso on proveedores_pienso.id = tamanio_pellets.proveedor_pienso_id
                                  group by week(consumos.fecha, 3), tamanio_pellets.proveedor_pienso_id, tamanio_pellets.diametro
                                  order by tamanio_pellets.proveedor_pienso_id,  tamanio_pellets.diametro, week(consumos.fecha, 3)', array($fecha_inicial, $fecha_final));
                $i = 1;
                 foreach ($consumos as $consumo)
                 {
                    if ($x == 0)
                     {
                       ${'fila'.$i}[1] = $consumo->nombre; 
                       ${'fila'.$i}[2] = $consumo->diametro;
                       ${'fila'.$i}[3] = $consumo->cantidad;
                       
                       
                     }
                     else
                     {
                       ${'fila'.$i}[$j] = $consumo->cantidad;
                       
                     }
                    
                    $i++;
                 }
                 $j++;
                $fecha_inicial->modify('+7 day');
                $fecha_final->modify('+7 day');
            }

               //echo 'i' . $i;
            $matriz = array();
                for ($j=1; $j<$i;$j++)
                {
                    array_push($matriz, ${'fila'.$j});
                }
                $semanas = array_unique($semanas);
                
                return View::make('consumo.proveedores')->with('semanas', $semanas)
                                                        ->with('datos', $matriz);
            
            
        }

        public function getStockSemanalII()
        {
          // Obtenemos la fecha de la última importación de datos reales
          $fecha_ultima_actualizacion = ProduccionReales::orderby('date', 'desc')->first();

          $fecha = new DateTime($fecha_ultima_actualizacion->date);

          // Obtenemos la semana que le corresponde a la fecha de la última actualización.
          $semana_objeto = Semana::where('last_day', '>=', $fecha_ultima_actualizacion->date)->orderby('first_day')
                                  ->take(7)->get();
          //echo 'Los valores de la semana son: ';
          //echo($semana_objeto);
          echo $fecha->format(' Y-m-d');
          foreach ($semana_objeto as $so)
          {
            //echo 'Semana ' . $so->week . ' año ' . $so->year;
            echo ' Última fecha real ' . $fecha->format(' Y-m-d');
            $fecha_ini_semana = new DateTime($so->first_day);
            $fecha_fin_semana = new DateTime($so->last_day);
            $interval = $fecha->diff($fecha_ini_semana);
            echo ' Día inicial de la semana actual ' . $fecha_ini_semana->format(' Y-m-d');
            echo ' Diferencia de dias ' . $interval->format(' %R%a días');
            if ($fecha > $fecha_ini_semana)
            {
              echo 'Estamos en la semana actual';
            }
            else
              echo ' No es la semana actual';
          }
          
          
          //echo($semana_objeto);
          // Averiguamos la semana correspondiente
          $num_semana = $fecha->format('W');
          $semanas = array();
          $j=3;
          //echo $num_semana;
          //echo $fecha->format('Y-m-d');
          for ($i=0; $i<7;$i++)
          {
            $datos_stock = DB::select('Select proveedores_pienso.nombre, tamanio_pellets.diametro, 
                                              ifnull((Select sum(cantidad)
                                                        from movimientos_almacenes ma, almacenes a, piensos p , tamanio_pellets tp, proveedores_pienso pp
                                                       where a.id = ma.almacen_id
                                                         and p.id   = ma.pienso_id
                                                         and p.diametro_pellet_id = tp.id
                                                         and ma.fecha <= ?
                                                         and pp.id = p.proveedor_id
                                                         and tamanio_pellets.id = tp.id
                                                    group by pp.nombre, tp.diametro),0) as stock_real, 
                                              ifnull((Select sum(cantidad)
                                                        from piensos p , tamanio_pellets tp, proveedores_pienso pp, consumos c
                                                       where c.proveedor_id = pp.id
                                                         and c.pienso_id = p.id
                                                         and tp.id = p.diametro_pellet_id
                                                         and tamanio_pellets.id = tp.id
                                                         and fecha > ?
                                                         and week(fecha, 3) = ?
                                                    group by pp.nombre, tp.diametro),0) as consumo_simulado,
                                              ifnull((Select sum(cantidad)
                                                        from pedidos_detalles pd, pedidos p, piensos ps, tamanio_pellets tp, proveedores_pienso pp
                                                       where tamanio_pellets.id = tp.id
                                                         and pd.pedido_id = p.id  
                                                         and pd.pienso_id = ps.id
                                                         and tp.id = ps.diametro_pellet_id
                                                         and ps.proveedor_id = pp.id
                                                         and p.fecha_descarga > ?
                                                         and week(p.fecha_descarga, 3) = ?
                                                         and p.estado <> ?
                                                    group by pp.nombre, tp.diametro ) ,0) as pedidos
                                        from proveedores_pienso, tamanio_pellets
                                       where proveedores_pienso.id = tamanio_pellets.proveedor_pienso_id
                                       order by proveedores_pienso.nombre, tamanio_pellets.diametro', array($fecha, $fecha, $num_semana, $fecha, $num_semana, 'Descargado'));
          $x=1;
          foreach ($datos_stock as $dato_stock)
           {
             if ($i == 0)
             {
               ${'fila'.$x}[1] = $dato_stock->nombre; 
               ${'fila'.$x}[2] = $dato_stock->diametro;
               ${'fila'.$x}[3] = $dato_stock->consumo_simulado;
               ${'fila'.$x}[4] = $dato_stock->pedidos;
               ${'fila'.$x}[5] = ($dato_stock->stock_real)+ ($dato_stock->pedidos) - ($dato_stock->consumo_simulado);
               if ((($dato_stock->consumo_simulado)*2) - ($dato_stock->stock_real)+ ($dato_stock->pedidos) - ($dato_stock->consumo_simulado) < 0 )
               {
                  ${'fila'.$x}[6] = 0;
               }
               else
               {
                  ${'fila'.$x}[6] = (($dato_stock->consumo_simulado)*2) - ($dato_stock->stock_real)+ ($dato_stock->pedidos) - ($dato_stock->consumo_simulado);
               }
               
             }
             else
             {
               ${'fila'.$x}[$j] = $dato_stock->consumo_simulado;
               ${'fila'.$x}[$j+1] = $dato_stock->pedidos;
               //echo ' A la j le sumamos 1 ' . ($j+1); 
               ${'fila'.$x}[$j+2] = ${'fila'.$x}[$j-2] + ($dato_stock->pedidos) - ($dato_stock->consumo_simulado);
               if ((($dato_stock->consumo_simulado)*2) - (${'fila'.$x}[$j-2] + ($dato_stock->pedidos) - ($dato_stock->consumo_simulado)) < 0)
               {
                  ${'fila'.$x}[$j+3] = 0;
               }
               else
               {
                  ${'fila'.$x}[$j+3] = (($dato_stock->consumo_simulado)*2) - (${'fila'.$x}[$j-2] + ($dato_stock->pedidos) - ($dato_stock->consumo_simulado));
               }
               
             }
             $x++;
           }
          $j=$j+4;
          array_push($semanas, $num_semana);
          $num_semana++;
          }
          
          $matriz = array();
          for ($j=1; $j<$x;$j++)
            {
              array_push($matriz, ${'fila'.$j});
            }

          // Obtenemos el listado de semanas. Como máximo devolveremos 10 semanas, a partir de la semana actual.
            $semanas_listado = Semana::where('first_day', '>=', $fecha)->take(7)->get();

          return View::make('consumo.proveedores')->with('semanas', $semanas)
                                                  ->with('semanas_listado', $semanas_listado)
                                                  ->with('datos', $matriz);

        }
        public function getStockSemanal()
        {
          // Obtenemos la fecha de la última importación de datos reales
          $fecha_ultima_actualizacion = ProduccionReales::orderby('date', 'desc')->first();

          $fecha = new DateTime($fecha_ultima_actualizacion->date);

          // Obtenemos la semana que le corresponde a la fecha de la última actualización.
          $semana_objeto = Semana::where('last_day', '>=', $fecha_ultima_actualizacion->date)->orderby('first_day')
                                  ->take(7)->get();
          //echo 'Los valores de la semana son: ';
          //echo($semana_objeto);
          //echo $fecha->format(' Y-m-d');
          $j=4;
          $i=0;
          $semanas = array();
          foreach ($semana_objeto as $so)
          {
            //echo 'Semana ' . $so->week . ' año ' . $so->year;
            //echo ' Última fecha real ' . $fecha->format(' Y-m-d');
            $fecha_ini_semana = new DateTime($so->first_day);
            $fecha_fin_semana = new DateTime($so->last_day);
            $interval = $fecha->diff($fecha_ini_semana);
            //echo ' Día inicial de la semana actual ' . $fecha_ini_semana->format(' Y-m-d');
            //echo ' Diferencia de dias ' . $interval->format(' %R%a días');
            if ($fecha > $fecha_ini_semana)
            {
              $fecha_inicial = $fecha;
            }
            else
            {
              $fecha_inicial = $fecha_ini_semana;
            }
          
          
          //echo($semana_objeto);
          // Averiguamos la semana correspondiente
          $num_semana = $so->week . ' de ' . $so->year;
          
          
          //echo $num_semana;
          //echo $fecha->format('Y-m-d');
          echo $fecha_ini_semana->format('Y-m-d');
          echo $fecha_fin_semana->format('Y-m-d');
          
            $datos_stock = DB::select('Select proveedores_pienso.nombre, tamanio_pellets.diametro, 
                                              ifnull((Select sum(cantidad)
                                                        from movimientos_almacenes ma, almacenes a, piensos p , tamanio_pellets tp, proveedores_pienso pp
                                                       where a.id = ma.almacen_id
                                                         and p.id   = ma.pienso_id
                                                         and p.diametro_pellet_id = tp.id
                                                         and ma.fecha <= ?
                                                         and pp.id = p.proveedor_id
                                                         and tamanio_pellets.id = tp.id
                                                    group by pp.nombre, tp.diametro),0) as stock_real, 
                                              ifnull((Select sum(cantidad)
                                                        from piensos p , tamanio_pellets tp, proveedores_pienso pp, consumos c
                                                       where c.proveedor_id = pp.id
                                                         and c.pienso_id = p.id
                                                         and tp.id = p.diametro_pellet_id
                                                         and tamanio_pellets.id = tp.id
                                                         and fecha > ?
                                                         and fecha  >= ?
                                                         and fecha  <= ?
                                                    group by pp.nombre, tp.diametro),0) as consumo_simulado,
                                              ifnull((Select sum(cantidad)
                                                        from pedidos_detalles pd, pedidos p, piensos ps, tamanio_pellets tp, proveedores_pienso pp
                                                       where tamanio_pellets.id = tp.id
                                                         and pd.pedido_id = p.id  
                                                         and pd.pienso_id = ps.id
                                                         and tp.id = ps.diametro_pellet_id
                                                         and ps.proveedor_id = pp.id
                                                         and p.fecha_descarga >= ?
                                                         and p.fecha_descarga <= ?
                                                         and p.estado <> ?
                                                    group by pp.nombre, tp.diametro ) ,0) as pedidos
                                        from proveedores_pienso, tamanio_pellets
                                       where proveedores_pienso.id = tamanio_pellets.proveedor_pienso_id
                                       order by proveedores_pienso.nombre, tamanio_pellets.diametro', array($fecha, $fecha, $fecha_inicial, $fecha_fin_semana, $fecha_ini_semana, 
                                                                                                            $fecha_fin_semana, 'Descargado'));
/*
$datos_stock = DB::select('Select proveedores_pienso.nombre, tamanio_pellets.diametro, 
                                              ifnull((Select sum(cantidad)
                                                        from movimientos_almacenes ma, almacenes a, piensos p , tamanio_pellets tp, proveedores_pienso pp
                                                       where a.id = ma.almacen_id
                                                         and p.id   = ma.pienso_id
                                                         and p.diametro_pellet_id = tp.id
                                                         and ma.fecha <= ?
                                                         and pp.id = p.proveedor_id
                                                         and tamanio_pellets.id = tp.id
                                                    group by pp.nombre, tp.diametro),0) as stock_real, 
                                              ifnull((Select sum(cantidad)
                                                        from piensos p , tamanio_pellets tp, proveedores_pienso pp, consumos c
                                                       where c.proveedor_id = pp.id
                                                         and c.pienso_id = p.id
                                                         and tp.id = p.diametro_pellet_id
                                                         and tamanio_pellets.id = tp.id
                                                         and fecha > ?
                                                         and fecha  >= ?
                                                         and fecha  <= ?
                                                    group by pp.nombre, tp.diametro),0) as consumo_simulado,
                                              ifnull((Select sum(cantidad)
                                                        from pedidos_detalles pd, pedidos p, piensos ps, tamanio_pellets tp, proveedores_pienso pp
                                                       where tamanio_pellets.id = tp.id
                                                         and pd.pedido_id = p.id  
                                                         and pd.pienso_id = ps.id
                                                         and tp.id = ps.diametro_pellet_id
                                                         and ps.proveedor_id = pp.id
                                                         and p.fecha_descarga > ?
                                                         and p.fecha_descarga >= ?
                                                         and p.fecha_descarga <= ?
                                                         and p.estado <> ?
                                                    group by pp.nombre, tp.diametro ) ,0) as pedidos
                                        from proveedores_pienso, tamanio_pellets
                                       where proveedores_pienso.id = tamanio_pellets.proveedor_pienso_id
                                       order by proveedores_pienso.nombre, tamanio_pellets.diametro', array($fecha, $fecha, $fecha_inicial, $fecha_fin_semana, $fecha, $fecha_inicial, $fecha_fin_semana, 'Descargado'));
          */
          $x=1;
          foreach ($datos_stock as $dato_stock)
           {
             if ($i == 0)
             {
               ${'fila'.$x}[1] = $dato_stock->nombre; 
               ${'fila'.$x}[2] = $dato_stock->diametro;
               ${'fila'.$x}[3] = $dato_stock->stock_real;
               ${'fila'.$x}[4] = $dato_stock->consumo_simulado;
               ${'fila'.$x}[5] = ($dato_stock->pedidos) ;
               ${'fila'.$x}[6] = ($dato_stock->stock_real)+ ($dato_stock->pedidos) - ($dato_stock->consumo_simulado);
               /*
               if ((($dato_stock->consumo_simulado)*2) - ($dato_stock->stock_real)+ ($dato_stock->pedidos) - ($dato_stock->consumo_simulado) < 0 )
               {
                  ${'fila'.$x}[] = 0;
               }
               else
               {
                  ${'fila'.$x}[7] = (($dato_stock->consumo_simulado)*2) - ($dato_stock->stock_real)+ ($dato_stock->pedidos) - ($dato_stock->consumo_simulado);
               }
               */
             }
             else
             {
               ${'fila'.$x}[$j] = $dato_stock->consumo_simulado;
               ${'fila'.$x}[$j+1] = ($dato_stock->pedidos) ;
               //echo ' A la j le sumamos 1 ' . ($j+1); 
               ${'fila'.$x}[$j+2] = ${'fila'.$x}[$j-1] + ($dato_stock->pedidos) - ($dato_stock->consumo_simulado);
               /*
               if ((($dato_stock->consumo_simulado)*2) - (${'fila'.$x}[$j-2] + ($dato_stock->pedidos) - ($dato_stock->consumo_simulado)) < 0)
               {
                  ${'fila'.$x}[$j+3] = 0;
               }
               else
               {
                  ${'fila'.$x}[$j+3] = (($dato_stock->consumo_simulado)*2) - (${'fila'.$x}[$j-2] + ($dato_stock->pedidos) - ($dato_stock->consumo_simulado));
               }
               */
             }
             $x++;
           }
          $j=$j+3;
          array_push($semanas, $num_semana);
          //$num_semana++;
          $i++;
          }
          
          $matriz = array();
          for ($j=1; $j<$x;$j++)
            {
              array_push($matriz, ${'fila'.$j});
            }

          // Obtenemos el listado de semanas. Como máximo devolveremos 10 semanas, a partir de la semana actual.
            $semanas_listado = Semana::where('first_day', '>=', $fecha)->take(7)->get();

          return View::make('consumo.proveedores')->with('semanas', $semanas)
                                                  ->with('semanas_listado', $semanas_listado)
                                                  ->with('fecha', date_format($fecha, 'd/m/y'))
                                                  ->with('datos', $matriz);

        }

        public function getStockSemanalAlmacenes()
        {
          if (isset($_POST['id_almacen']))
          {
            $id_almacen = Input::get('id_almacen');
          }
          else
          {
            $id_almacen = 2;
          }
          
          // Obtenemos el nombre de la granja
          $almacen = Almacen::find($id_almacen);
          $granja = Granja::where('nombre', '=', $almacen->nombre)->first();
          // Obtenemos la fecha de la última importación de datos reales
          $fecha_ultima_actualizacion = ProduccionReales::orderby('date', 'desc')->first();

          $fecha = new DateTime($fecha_ultima_actualizacion->date);
          //var_dump($fecha_ultima_actualizacion);
          // Averiguamos la semana correspondiente
          //$num_semana = $fecha->format('W');
          $dias_semana = array("Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado", "Domingo");
          
          $dias = array();
          $j=3;
          //echo $num_semana;
          //echo $fecha->format('Y-m-d');
          for ($i=0; $i<8;$i++)
          {
            $datos_stock = DB::select('Select proveedores_pienso.nombre, tamanio_pellets.diametro, 
                                              ifnull((Select sum(cantidad)
                                                        from movimientos_almacenes ma, almacenes a, piensos p , tamanio_pellets tp, proveedores_pienso pp
                                                       where a.id = ma.almacen_id
                                                         and a.id = ?
                                                         and p.id   = ma.pienso_id
                                                         and p.diametro_pellet_id = tp.id
                                                         and ma.fecha <= ?
                                                         and pp.id = p.proveedor_id
                                                         and tamanio_pellets.id = tp.id
                                                    group by pp.nombre, tp.diametro),0) as stock_real, 
                                              ifnull((Select sum(cantidad)
                                                        from piensos p , tamanio_pellets tp, proveedores_pienso pp, consumos c
                                                       where c.proveedor_id = pp.id
                                                         and c.pienso_id = p.id
                                                         and c.granja_id = ?
                                                         and tp.id = p.diametro_pellet_id
                                                         and tamanio_pellets.id = tp.id
                                                         and fecha = DATE_ADD(?, INTERVAL 1 DAY)
                                                    group by pp.nombre, tp.diametro),0) as consumo_simulado,
                                              ifnull((Select sum(cantidad)
                                                        from traslados_detalles td, traslados t, piensos ps, tamanio_pellets tp, proveedores_pienso pp
                                                      where tamanio_pellets.id = tp.id
                                                        and td.traslado_id = t.id  
                                                        and td.pienso_id = ps.id
                                                        and tp.id = ps.diametro_pellet_id
                                                        and ps.proveedor_id = pp.id
                                                        and t.fecha_traslado = DATE_ADD(?, INTERVAL 1 DAY)
                                                        and t.estado <> ?
                                                    group by pp.nombre, tp.diametro ) ,0) as pedidos
                                        from proveedores_pienso, tamanio_pellets
                                      where proveedores_pienso.id = tamanio_pellets.proveedor_pienso_id
                                      order by proveedores_pienso.nombre, tamanio_pellets.diametro', array($id_almacen, $fecha, $granja->id, $fecha, $fecha, 'Descargado'));
          $x=1;
          foreach ($datos_stock as $dato_stock)
          {
             if ($i == 0)
             {
               ${'fila'.$x}[1] = $dato_stock->nombre; 
               ${'fila'.$x}[2] = $dato_stock->diametro;
               ${'fila'.$x}[3] = $dato_stock->consumo_simulado;
               ${'fila'.$x}[4] = $dato_stock->pedidos;
               ${'fila'.$x}[5] = ($dato_stock->stock_real)+ ($dato_stock->pedidos) - ($dato_stock->consumo_simulado);
               if ((($dato_stock->consumo_simulado)*2) - ($dato_stock->stock_real)+ ($dato_stock->pedidos) - ($dato_stock->consumo_simulado) < 0 )
               {
                  ${'fila'.$x}[6] = 0;
               }
               else
               {
                  ${'fila'.$x}[6] = (($dato_stock->consumo_simulado)*2) - ($dato_stock->stock_real)+ ($dato_stock->pedidos) - ($dato_stock->consumo_simulado);
               }
               
             }
             else
             {
               ${'fila'.$x}[$j] = $dato_stock->consumo_simulado;
               ${'fila'.$x}[$j+1] = $dato_stock->pedidos;
               //echo ' A la j le sumamos 1 ' . ($j+1); 
               ${'fila'.$x}[$j+2] = ${'fila'.$x}[$j-2] + ($dato_stock->pedidos) - ($dato_stock->consumo_simulado);
               if ((($dato_stock->consumo_simulado)*2) - (${'fila'.$x}[$j-2] + ($dato_stock->pedidos) - ($dato_stock->consumo_simulado)) < 0)
               {
                  ${'fila'.$x}[$j+3] = 0;
               }
               else
               {
                  ${'fila'.$x}[$j+3] = (($dato_stock->consumo_simulado)*2) - (${'fila'.$x}[$j-2] + ($dato_stock->pedidos) - ($dato_stock->consumo_simulado));
               }
               
             }
             $x++;
          }
          $j=$j+4;
          $fecha->modify('+1 day');
          array_push($dias, $dias_semana[$fecha->format('N')-1] . ' '.  $fecha->format('d-m-Y'));
          
          }
          $matriz = array();
          for ($j=1; $j<$x;$j++)
            {
              array_push($matriz, ${'fila'.$j});
            }

          // Obtenemos el listado de almacenes
            $almacenes = Almacen::where('id', '=', '2')->orwhere('id', '=', '3')->get();
          return View::make('consumo.almacenes')->with('semanas', $dias)
                                                ->with('datos', $matriz)
                                                ->with('granja', $granja)
                                                ->with('almacenes', $almacenes);

        }

        public function getSemana($anyo, $semana)
        {
            /*
            $anyo = Input::get('anyo');
            $semana = Input::get('semana'); 
            */
            if (empty($anyo) && empty($semana))
             
             {
               $anyo = date('Y');
               $semana = date('W');
             } 

            $pellets = Pellet::all();
            $granjas = Granja::orderby('nombre')->get();
            $jaulas = Jaula::orderby('nombre')->get();
            $proveedores = Proveedorpienso::orderby('nombre')->get();
            $lotes = Lote::orderby('nombre')->get();
            $dia_ini = Semana::where('year', '=', $anyo)->where('week', '=', $semana)->first();
            $dia = new DateTime($dia_ini->first_day);  // Primer día de la semana
            $dia_fin = new DateTime($dia_ini->last_day);  // Último día de la semana

            $meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
            $dias_semana = array("Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado", "Domingo");
                
            $dia_letras[] = $dias_semana[0];
            $dia_numeros[] = $dia->format('j') . " de " . $meses[$dia->format('n')-1];
            
            $consumos = Consumo::where('fecha', '>=', $dia)
                               ->where('fecha', '<=', $dia_fin)
                               ->orderby('granja')
                               ->orderby('jaula')
                               ->orderby('lote')
                               ->orderby('fecha')
                               ->get();             
         
            $consumo_linea = array(); //Contendrá los consumos semanales linea a linea
            $consumo_semanal = array(); //Contendrá todos los consumos que pasaremos a la vista para ser mostrados
            // 
            //Generamos el array que constituirá la tabla. siempre que tengamos datos
            if (count($consumos) > 0)
            {

            $granja = null;
            $jaula = null;
            $lote = null;
            $proveedor = null;
            $primera_fila = 1;
            
            
            foreach($consumos as $consumo)
            {   //echo "Granja: " . $consumo->granja . " Jaula: " . $consumo->jaula . " Lote: " . $consumo->lote . " Proveedor: " . $consumo->proveedor;
                if (($granja != $consumo->granja) || ($jaula != $consumo->jaula) || ($lote != $consumo->lote) || ($proveedor != $consumo->proveedor))
                {
                    //echo "Primera iteración";
                    //echo "Cambiamos de jaula";
                    $granja = $consumo->granja;
                    $jaula = $consumo->jaula;
                    $lote = $consumo->lote;
                    $proveedor = $consumo->proveedor;
                    
                    //echo "Granja: " . $granja . " Jaula: " . $jaula . " Lote: " . $lote . " Proveedor: " . $proveedor;
                    if ($primera_fila == 1)
                    {
                       
                       $primera_fila = 0;
                    }
                    else
                    {
                       //echo "No es la primera fila";
                        $consumo_linea[] = $id;
                        array_push($consumo_semanal, $consumo_linea);
                       //$consumo_semanal[] = $consumo_linea;
                       $consumo_linea = array();
                    }
                    //echo "Es la primera fila";
                    $id = $consumo->id;
                    $consumo_linea[] = $granja;
                    $consumo_linea[] = $jaula;
                    $consumo_linea[] = $lote;
                    $consumo_linea[] = $proveedor;
                    $consumo_linea[] = $consumo->cantidad_recomendada . "Kg.";
                    $consumo_linea[] = $consumo->porcentaje_estrategia . "%";
                    $consumo_linea[] = $consumo->cantidad . "Kg.";
                    $consumo_linea[] = $consumo->diametro_pienso . "mm";
                    $primera_fila = 0;
                    //var_dump($consumo_linea);
                    
                    //echo $primera_fila;
                }
                else
                {
                    //echo " Seguimos con la misma granja, jaula, lote y proveedor ";
                    $consumo_linea[] = $consumo->cantidad_recomendada . "Kg.";
                    $consumo_linea[] = $consumo->porcentaje_estrategia . "%";
                    $consumo_linea[] = $consumo->cantidad . "Kg.";
                    $consumo_linea[] = $consumo->diametro_pienso . "mm";
                    //var_dump($consumo_linea);
                }
            }
            $consumo_linea[] = $id;
            array_push($consumo_semanal, $consumo_linea);
            //var_dump($consumo_semanal);
            }
            for ($i=1; $i<7; $i++)
            {
              $dia->modify('+1 day');
              $dia_letras[] = $dias_semana[$i];
              $dia_numeros[] = $dia->format('j') . " de " . $meses[$dia->format('n')-1];
            }
            //var_dump($consumo_semanal);

             
            return View::make('consumo.consumo_semanal')
                        ->with('consumos', $consumo_semanal)
                        ->with('anyo', $anyo)
                        ->with('semana', $semana)
                        ->with('granjas', $granjas)
                        ->with('pellets', $pellets)
                        ->with('proveedores', $proveedores)
                        ->with('lotes', $lotes)
                        ->with('jaulas', $jaulas) 
                        ->with('dias_letras', $dia_letras)
                        ->with('dias_numeros', $dia_numeros);
            
        }

		public function getWeek()
        {
            
            $anyo = Input::get('anyo');
            $semana = Input::get('semana'); 
            
            if (empty($anyo) && empty($semana))
             
             {
               $anyo = date('Y');
               $semana = date('W');
             } 
            $pellets = Pellet::all();
            $granjas = Granja::orderby('nombre')->get();
            $jaulas = Jaula::orderby('nombre')->get();
            $proveedores = Proveedorpienso::orderby('nombre')->get();
            $lotes = Lote::orderby('nombre')->get();
            $dia_ini = Semana::where('year', '=', $anyo)->where('week', '=', $semana)->first();
            $dia = new DateTime($dia_ini->first_day);  // Primer día de la semana
            $dia_fin = new DateTime($dia_ini->last_day);  // Último día de la semana

            $meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
            $dias_semana = array("Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado", "Domingo");
                
            $dia_letras[] = $dias_semana[0];
            $dia_numeros[] = $dia->format('j') . " de " . $meses[$dia->format('n')-1];
            
            $consumos = Consumo::where('fecha', '>=', $dia)
                               ->where('fecha', '<=', $dia_fin)
                               ->orderby('granja')
                               ->orderby('jaula')
                               ->orderby('lote')
                               ->orderby('fecha')
                               ->get();             
         
            // 
            //Generamos el array que constituirá la tabla. siempre que tengamos datos
            $consumo_linea = array(); //Contendrá los consumos semanales linea a linea
            $consumo_semanal = array(); //Contendrá todos los consumos que pasaremos a la vista para ser mostrados
            if (count($consumos) > 0)
            {

            $granja = null;
            $jaula = null;
            $lote = null;
            $proveedor = null;
            $primera_fila = 1;
            
            
            foreach($consumos as $consumo)
            {   //echo "Granja: " . $consumo->granja . " Jaula: " . $consumo->jaula . " Lote: " . $consumo->lote . " Proveedor: " . $consumo->proveedor;
                if (($granja != $consumo->granja) || ($jaula != $consumo->jaula) || ($lote != $consumo->lote) || ($proveedor != $consumo->proveedor))
                {
                    //echo "Primera iteración";
                    //echo "Cambiamos de jaula";
                    $granja = $consumo->granja;
                    $jaula = $consumo->jaula;
                    $lote = $consumo->lote;
                    $proveedor = $consumo->proveedor;
                    
                    //echo "Granja: " . $granja . " Jaula: " . $jaula . " Lote: " . $lote . " Proveedor: " . $proveedor;
                    if ($primera_fila == 1)
                    {
                       
                       $primera_fila = 0;
                    }
                    else
                    {
                       //echo "No es la primera fila";
                        $consumo_linea[] = $id;
                        array_push($consumo_semanal, $consumo_linea);
                       //$consumo_semanal[] = $consumo_linea;
                       $consumo_linea = array();
                    }
                    //echo "Es la primera fila";
                    $id = $consumo->id;
                    $consumo_linea[] = $granja;
                    $consumo_linea[] = $jaula;
                    $consumo_linea[] = $lote;
                    $consumo_linea[] = $proveedor;
                    $consumo_linea[] = $consumo->cantidad_recomendada . "Kg.";
                    $consumo_linea[] = $consumo->porcentaje_estrategia . "%";
                    $consumo_linea[] = $consumo->cantidad . "Kg.";
                    $consumo_linea[] = $consumo->diametro_pienso . "mm";
                    $primera_fila = 0;
                    //var_dump($consumo_linea);
                    
                    //echo $primera_fila;
                }
                else
                {
                    //echo " Seguimos con la misma granja, jaula, lote y proveedor ";
                    $consumo_linea[] = $consumo->cantidad_recomendada . "Kg.";
                    $consumo_linea[] = $consumo->porcentaje_estrategia . "%";
                    $consumo_linea[] = $consumo->cantidad . "Kg.";
                    $consumo_linea[] = $consumo->diametro_pienso . "mm";
                    //var_dump($consumo_linea);
                }
            }
            $consumo_linea[] = $id;
            array_push($consumo_semanal, $consumo_linea);
            //var_dump($consumo_semanal);
            }
            for ($i=1; $i<7; $i++)
            {
              $dia->modify('+1 day');
              $dia_letras[] = $dias_semana[$i];
              $dia_numeros[] = $dia->format('j') . " de " . $meses[$dia->format('n')-1];
            }
            //var_dump($consumo_semanal);

             
            return View::make('consumo.consumo_semanal')
                        ->with('consumos', $consumo_semanal)
                        ->with('anyo', $anyo)
                        ->with('semana', $semana)
                        ->with('granjas', $granjas)
                        ->with('pellets', $pellets)
                        ->with('proveedores', $proveedores)
                        ->with('lotes', $lotes)
                        ->with('jaulas', $jaulas) 
                        ->with('dias_letras', $dia_letras)
                        ->with('dias_numeros', $dia_numeros);
          
		}

   public function getDay()
   {
     $fecha=Input::get('fecha_consumo');
     
     
     if (empty($anyo))
     {
        $fecha_consumo=date('Y-m-d');
     }
     else
     {
        list($dia, $mes, $year)=explode("-", $fecha);
        $fecha_consumo=$year."-".$mes."-".$dia;
     }

     // Obtenemos los datos que queremos mostrar
     $consumo = DB::Select('select granja_id, granja, jaula_id, jaula, lote_id, lote, 
                            stock_count_ini, stock_avg_ini, proveedor_id, 
                            proveedor, pienso_id, pienso, codigo_pienso, diametro_pienso, 
                            cantidad, porcentaje_estrategia, (cantidad/25) as Sacos
                       from consumos, produccion_simulado
                       where fecha = ?
                         and granja = site
                         and fecha = date
                         and jaula = unitname
                         and lote = groupid
                       order by granja, jaula, lote, codigo_pienso asc', array($fecha_consumo));


     
   }

		public function getAdd()
        {
			$pellet_id =Input::get('pellet_id');
			$proveedor_id =Input::get('proveedor_id');
			$granja_id =Input::get('granja_id');
			$jaula_id = Input::get('jaula_id');
			$lote_id = Input::get('lote_id');
			$anyo =Input::get('anyo');
			$semana = Input::get('semana');


			//Creamos el objeto pellet
			$pellet = Pellet::find($pellet_id);

			//Creamos el objeto granja
			$granja = Granja::find($granja_id);

			//Creamos el objeto Jaula
			$jaula = Jaula::find($jaula_id);

			//Creamos el objeto Proveedor
			$proveedor = Proveedorpienso::find($proveedor_id);

			//Creamos el objeto lote
			$lote = Lote::find($lote_id);

			//Buscamos un pienso que sea del proveedor y del pellet señalado
			$pienso = DB::table('piensos')->where('proveedor_id', '=', $proveedor_id)->where('diametro_pellet_id', '=', $pellet_id)->orderBy('updated_at', 'desc')->first();
            
            //Buscamos el primer día de la semana seleccionada.
            $dia_ini = Semana::where('year', '=', $anyo)->where('week', '=', $semana)->first();
            $dia = new DateTime($dia_ini->first_day);

            // Realizamos un bucle para insertar los mismos datos durante toda la semana.
            for ($i=1; $i<8; $i++)
            {
            	Consumo::create(
				array(
					'granja_id' => $granja_id,
					'granja' => $granja->nombre,
					'jaula_id' => $jaula_id,
					'jaula' => $jaula->nombre,
					'lote_id' => $lote_id,
					'lote' => $lote->nombre,
					'proveedor_id' => $proveedor_id,
					'proveedor' =>$proveedor->nombre,
					'pienso_id' =>$pienso->id,
					'pienso' =>$pienso->nombre,
					'codigo_pienso' => $pienso->codigo,
					'diametro_pienso' => $pellet->diametro,
                    'cantidad_recomendada' => Input::get('cantidad'),
                    'porcentaje_estrategia' => 100,
					'cantidad' => Input::get('cantidad'),
					'fecha' => $dia
					));
            	$dia->modify('+1 day');
            }
            //Añadimos el registro a la tabla
            
            return Redirect::to('consumo/semanal/'. $anyo . '/' . $semana);
			
		}

        public function getWeekDelete($id)
        {
            // Buscamos los datos de la jaula, la granja, proveedor y lote a los que pertenece los datos  
            // que tenemos que eliminar.
            
            
            $consumo = Consumo::find($id);
            $dia_ini = new DateTime($consumo->fecha);
            $semanas = Semana::where('first_day', '<=', $consumo->fecha)->get();
            foreach ($semanas as $semana){
                $year = $semana->year;
                $week = $semana->week;
            }

            //Procedemos a eliminar los datos
            for ($i=1; $i<8; $i++)
            {
                /*
                Consumo::where('fecha', '=', $dia_ini)
                       ->where('granja', '=' , $consumo->granja)
                       ->where('jaula', '=', $consumo->jaula)
                       ->where('lote', '=', $consumo->lote)
                       ->where('proveedor', '=', $consumo->proveedor)
                       ->where('diametro_pienso', '=', $consumo->diametro_pienso)
                       ->delete();
                */
                Consumo::find($id)->delete();       
                //$dia_ini->modify('+1 day');  
                $id++;     
            }

            return Redirect::to('consumo/semanal/' . $year . '/' . $week);
            
        }

        public function getWeekEdit($id)
        {
            
            //Recuperamos el registro de consumo a editar
            $consumo = Consumo::find($id);

            //Llenamos todos los lotes
            $lotes = Lote::orderby('nombre')->get();

            //Llenamos todos los tamaño de pellets del proveedor que tenemos por seleccionado
            $pellets = Pellet::where('proveedor_pienso_id', '=', $consumo->proveedor_id)
                           ->orderby('diametro')->get();

            //Llenamos todos los proeedores
            $proveedores = Proveedorpienso::orderby('nombre')->get();
            
            
            
            //Volcamos los datos originales
            $granja = $consumo->granja;
            $jaula = $consumo->jaula;
            $proveedorpienso = $consumo->proveedor;
            $lote_id = $consumo->lote_id; 

            $fecha = new DateTime($consumo->fecha);
            
            $semanas = Semana::where('first_day', '<=', $consumo->fecha)->get();
            foreach ($semanas as $semana){
                $year = $semana->year;
                $week = $semana->week;
            }
            
            // Elaboramos la cabecera de las fechas
            $meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
            $dias_semana = array("Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado", "Domingo");
            $dia = new DateTime($consumo->fecha);    
            $dia_letras[] = $dias_semana[0];
            $dia_numeros[] = $dia->format('j') . " de " . $meses[$dia->format('n')-1];
            
            for ($i=1; $i<7; $i++)
            {
              $dia->modify('+1 day');
              $dia_letras[] = $dias_semana[$i];
              $dia_numeros[] = $dia->format('j') . " de " . $meses[$dia->format('n')-1];
            }

            $cantidad_recomendada_lunes = $consumo->cantidad_recomendada;
            $porcentaje_estrategia_lunes = $consumo->porcentaje_estrategia;
            $cantidad_lunes = $consumo->cantidad;
            $pellet_lunes = $consumo->diametro_pienso;
            $id_lunes = $id;
            
            
            //Obtenemos los datos del martes
            
            $martes = $fecha->modify('+1 day'); 
            
            $consumo_martes = Consumo::where('granja', '=', $granja)
                                     ->where('jaula', '=', $jaula)
                                     ->where('proveedor', '=', $proveedorpienso)
                                     //->where('diametro_pienso', '=', $consumo->diametro_pienso)
                                     ->where('lote', '=', $consumo->lote)
                                     ->where('fecha', '=', $martes)
                                     ->first();
                        
            $cantidad_martes = $consumo_martes->cantidad;
            $pellet_martes = $consumo_martes->diametro_pienso;
            $id_martes = $id_lunes + 1;  //$consumo_martes->$id;
            

            //Obtenemos los datos del miércoles
            
            $miercoles = $fecha->modify('+1 day'); 
            $consumo_miercoles = Consumo::where('granja', '=', $granja)
                                     ->where('jaula', '=', $jaula)
                                     ->where('proveedor', '=', $proveedorpienso)
                                     //->where('diametro_pienso', '=', $consumo->diametro_pienso)
                                     ->where('lote', '=', $consumo->lote)
                                     ->where('fecha', '=', $miercoles)
                                     ->first();
                        
            $cantidad_miercoles = $consumo_miercoles->cantidad;
            $pellet_miercoles = $consumo_miercoles->diametro_pienso;
            $id_miercoles = $id_martes + 1;//$consumo_miercoles->$id;

            //Obtenemos los datos del jueves
            
            $jueves = $fecha->modify('+1 day'); 
            $consumo_jueves = Consumo::where('granja', '=', $granja)
                                     ->where('jaula', '=', $jaula)
                                     ->where('proveedor', '=', $proveedorpienso)
                                     //->where('diametro_pienso', '=', $consumo->diametro_pienso)
                                     ->where('lote', '=', $consumo->lote)
                                     ->where('fecha', '=', $jueves)
                                     ->first();
                        
            $cantidad_jueves = $consumo_jueves->cantidad;
            $pellet_jueves = $consumo_jueves->diametro_pienso;
            $id_jueves = $id_miercoles + 1;//$consumo_jueves->$id;

            //Obtenemos los datos del viernes
            
            $viernes = $fecha->modify('+1 day'); 
           $consumo_viernes = Consumo::where('granja', '=', $granja)
                                     ->where('jaula', '=', $jaula)
                                     ->where('proveedor', '=', $proveedorpienso)
                                     //->where('diametro_pienso', '=', $consumo->diametro_pienso)
                                     ->where('lote', '=', $consumo->lote)
                                     ->where('fecha', '=', $viernes)
                                     ->first();
                        
            $cantidad_viernes = $consumo_viernes->cantidad;
            $pellet_viernes = $consumo_viernes->diametro_pienso;
            $id_viernes = $id_jueves + 1; $consumo_viernes->$id;

            //Obtenemos los datos del sábado
            
            $sabado = $fecha->modify('+1 day'); 
            $consumo_sabado = Consumo::where('granja', '=', $granja)
                                     ->where('jaula', '=', $jaula)
                                     ->where('proveedor', '=', $proveedorpienso)
                                     //->where('diametro_pienso', '=', $consumo->diametro_pienso)
                                     ->where('lote', '=', $consumo->lote)
                                     ->where('fecha', '=', $sabado)
                                     ->first();
                        
            $cantidad_sabado = $consumo_sabado->cantidad;
            $pellet_sabado = $consumo_sabado->diametro_pienso;
            $id_sabado = $id_viernes + 1; $consumo_sabado->$id;

            //Obtenemos los datos del domingo
            
            $domingo = $fecha->modify('+1 day'); 
            $consumo_domingo = Consumo::where('granja', '=', $granja)
                                     ->where('jaula', '=', $jaula)
                                     ->where('proveedor', '=', $proveedorpienso)
                                     //->where('diametro_pienso', '=', $consumo->diametro_pienso)
                                     ->where('lote', '=', $consumo->lote)
                                     ->where('fecha', '=', $domingo)
                                     ->first();
                        
            $cantidad_domingo = $consumo_domingo->cantidad;
            $pellet_domingo = $consumo_domingo->diametro_pienso;
            $id_domingo = $id_sabado + 1; $consumo_domingo->$id;

             
            
            return View::make('consumo.consumo_semanal_edit')
                       ->with('lotes', $lotes)
                       ->with('pellets', $pellets)
                       ->with('proveedores', $proveedores)
                       ->with('lote_id', $lote_id)
                       ->with('cantidad_recomendada_lunes', $cantidad_recomendada_lunes)
                       ->with('porcentaje_estrategia_lunes', $porcentaje_estrategia_lunes)
                       ->with('cantidad_lunes', $cantidad_lunes)
                       ->with('cantidad_lunes', $cantidad_lunes)
                       ->with('pellet_lunes', $pellet_lunes)
                       ->with('cantidad_martes', $cantidad_martes)
                       ->with('pellet_martes', $pellet_martes)
                       ->with('cantidad_miercoles', $cantidad_miercoles)
                       ->with('pellet_miercoles', $pellet_miercoles)
                       ->with('cantidad_jueves', $cantidad_jueves)
                       ->with('pellet_jueves', $pellet_jueves)
                       ->with('cantidad_viernes', $cantidad_viernes)
                       ->with('pellet_viernes', $pellet_viernes)
                       ->with('cantidad_sabado', $cantidad_sabado)
                       ->with('pellet_sabado', $pellet_sabado)
                       ->with('cantidad_domingo', $cantidad_domingo)
                       ->with('pellet_domingo', $pellet_domingo)
                       ->with('granja', $granja)
                       ->with('jaula', $jaula)
                       ->with('proveedorpienso', $proveedorpienso)
                       ->with('id_lunes', $id_lunes)
                       ->with('id_martes', $id_martes)
                       ->with('id_miercoles', $id_miercoles)
                       ->with('id_jueves', $id_jueves)
                       ->with('id_viernes', $id_viernes)
                       ->with('id_sabado', $id_sabado)
                       ->with('id_domingo', $id_domingo)
                       ->with('year', $year)
                       ->with('week', $week)
                       ->with('dias_letras', $dia_letras)
                       ->with('dias_numeros', $dia_numeros);

            
            
        }

        public function getWeekSave(){
            $week = Input::get('week');
            $year = Input::get('year');

            $proveedor_id = Input::get('proveedor_id');

            $id_lunes = Input::get('id_lunes');
            $id_martes = Input::get('id_martes');
            $id_miercoles = Input::get('id_miercoles');
            $id_jueves = Input::get('id_jueves');
            $id_viernes = Input::get('id_viernes');
            $id_sabado = Input::get('id_sabado');
            $id_domingo = Input::get('id_domingo');

            $cantidad_lunes = Input::get('cantidad_lunes');
            $pellet_lunes = Input::get('pellet_lunes');

            $cantidad_martes = Input::get('cantidad_martes');
            $pellet_martes = Input::get('pellet_martes');

            $cantidad_miercoles = Input::get('cantidad_miercoles');
            $pellet_miercoles = Input::get('pellet_miercoles');

            $cantidad_jueves = Input::get('cantidad_jueves');
            $pellet_jueves = Input::get('pellet_jueves');

            $cantidad_viernes = Input::get('cantidad_viernes');
            $pellet_viernes = Input::get('pellet_viernes');

            $cantidad_sabado = Input::get('cantidad_sabado');
            $pellet_sabado = Input::get('pellet_sabado');

            $cantidad_domingo = Input::get('cantidad_domingo');
            $pellet_domingo = Input::get('pellet_domingo');

            /*
            echo " week: " . $week;
            echo " year: " . $year;
            echo " proveedor_id: " . $proveedor_id;
            echo " id_lunes: " . $id_lunes;
            echo " id_martes: " . $id_martes;
            echo " id_miercoles: " . $id_miercoles;
            echo " id_jueves: " . $id_jueves;
            echo " id_viernes: " . $id_viernes;
            echo " id_sabado: " . $id_sabado;
            echo " id_domingo: " . $id_domingo;
            echo " cantidad_lunes: " . $cantidad_lunes;
            echo " cantidad_martes: " . $cantidad_martes;
            echo " cantidad_miercoles: " . $cantidad_miercoles;
            echo " cantidad_jueves: " . $cantidad_jueves;
            echo " cantidad_viernes: " . $cantidad_viernes;
            echo " cantidad_sabado: " . $cantidad_sabado;
            echo " cantidad_domingo: " . $cantidad_domingo;
            */
            // Creamos un objeto con el proveedor concreto
            $proveedor = Proveedorpienso::find($proveedor_id);


            //Actualizamos cada uno de los días (Lunes)
              //Creamos el objeto pellet
            $pellet = Pellet::find($pellet_lunes);
            
              //Buscamos un pienso que sea del proveedor y del pellet señalado
            $pienso = DB::table('piensos')->where('proveedor_id', '=', $proveedor_id)->where('diametro_pellet_id', '=', $pellet->id)->orderBy('updated_at', 'desc')->first();
            
            $consumo_dia = Consumo::find($id_lunes);
            $consumo_dia->cantidad = $cantidad_lunes;
            $consumo_dia->diametro_pienso = $pellet->diametro;
            $consumo_dia->pienso_id = $pienso->id;
            $consumo_dia->pienso = $pienso->nombre;
            $consumo_dia->codigo_pienso = $pienso->codigo;
            $consumo_dia->proveedor_id = $proveedor_id;
            $consumo_dia->proveedor = $proveedor->nombre;
            $consumo_dia->save();

            //Actualizamos cada uno de los días (Martes)
              //Creamos el objeto pellet
            $pellet = Pellet::find($pellet_martes);

              //Buscamos un pienso que sea del proveedor y del pellet señalado
            $pienso = DB::table('piensos')->where('proveedor_id', '=', $proveedor_id)->where('diametro_pellet_id', '=', $pellet->id)->orderBy('updated_at', 'desc')->first();
            $consumo_dia = Consumo::find($id_martes);
            $consumo_dia->cantidad = $cantidad_martes;
            $consumo_dia->diametro_pienso = $pellet->diametro;
            $consumo_dia->pienso_id = $pienso->id;
            $consumo_dia->pienso = $pienso->nombre;
            $consumo_dia->proveedor_id = $proveedor_id;
            $consumo_dia->proveedor = $proveedor->nombre;
            $consumo_dia->codigo_pienso = $pienso->codigo;
            $consumo_dia->save();

            //Actualizamos cada uno de los días (Miércoles)
              //Creamos el objeto pellet
            $pellet = Pellet::find($pellet_miercoles);

              //Buscamos un pienso que sea del proveedor y del pellet señalado
            $pienso = DB::table('piensos')->where('proveedor_id', '=', $proveedor_id)->where('diametro_pellet_id', '=', $pellet->id)->orderBy('updated_at', 'desc')->first();
            $consumo_dia = Consumo::find($id_miercoles);
            $consumo_dia->cantidad = $cantidad_miercoles;
            $consumo_dia->diametro_pienso = $pellet->diametro;
            $consumo_dia->pienso_id = $pienso->id;
            $consumo_dia->pienso = $pienso->nombre;
            $consumo_dia->proveedor_id = $proveedor_id;
            $consumo_dia->proveedor = $proveedor->nombre;
            $consumo_dia->codigo_pienso = $pienso->codigo;
            $consumo_dia->save();

            //Actualizamos cada uno de los días (jueves)
              //Creamos el objeto pellet
            $pellet = Pellet::find($pellet_jueves);

              //Buscamos un pienso que sea del proveedor y del pellet señalado
            $pienso = DB::table('piensos')->where('proveedor_id', '=', $proveedor_id)->where('diametro_pellet_id', '=', $pellet->id)->orderBy('updated_at', 'desc')->first();
            $consumo_dia = Consumo::find($id_jueves);
            $consumo_dia->cantidad = $cantidad_jueves;
            $consumo_dia->diametro_pienso = $pellet->diametro;
            $consumo_dia->pienso_id = $pienso->id;
            $consumo_dia->pienso = $pienso->nombre;
            $consumo_dia->proveedor_id = $proveedor_id;
            $consumo_dia->proveedor = $proveedor->nombre;
            $consumo_dia->codigo_pienso = $pienso->codigo;
            $consumo_dia->save();

            //Actualizamos cada uno de los días (viernes)
              //Creamos el objeto pellet
            $pellet = Pellet::find($pellet_viernes);

              //Buscamos un pienso que sea del proveedor y del pellet señalado
            $pienso = DB::table('piensos')->where('proveedor_id', '=', $proveedor_id)->where('diametro_pellet_id', '=', $pellet->id)->orderBy('updated_at', 'desc')->first();
            $consumo_dia = Consumo::find($id_viernes);
            $consumo_dia->cantidad = $cantidad_viernes;
            $consumo_dia->diametro_pienso = $pellet->diametro;
            $consumo_dia->pienso_id = $pienso->id;
            $consumo_dia->pienso = $pienso->nombre;
            $consumo_dia->proveedor_id = $proveedor_id;
            $consumo_dia->proveedor = $proveedor->nombre;
            $consumo_dia->codigo_pienso = $pienso->codigo;
            $consumo_dia->save();

            //Actualizamos cada uno de los días (sabado)
              //Creamos el objeto pellet
            $pellet = Pellet::find($pellet_sabado);

              //Buscamos un pienso que sea del proveedor y del pellet señalado
            $pienso = DB::table('piensos')->where('proveedor_id', '=', $proveedor_id)->where('diametro_pellet_id', '=', $pellet->id)->orderBy('updated_at', 'desc')->first();
            $consumo_dia = Consumo::find($id_sabado);
            $consumo_dia->cantidad = $cantidad_sabado;
            $consumo_dia->diametro_pienso = $pellet->diametro;
            $consumo_dia->pienso_id = $pienso->id;
            $consumo_dia->pienso = $pienso->nombre;
            $consumo_dia->proveedor_id = $proveedor_id;
            $consumo_dia->proveedor = $proveedor->nombre;
            $consumo_dia->codigo_pienso = $pienso->codigo;
            $consumo_dia->save();

            //Actualizamos cada uno de los días (domingo)
              //Creamos el objeto pellet
            $pellet = Pellet::find($pellet_domingo);

              //Buscamos un pienso que sea del proveedor y del pellet señalado
            $pienso = DB::table('piensos')->where('proveedor_id', '=', $proveedor_id)->where('diametro_pellet_id', '=', $pellet->id)->orderBy('updated_at', 'desc')->first();
            $consumo_dia = Consumo::find($id_domingo);
            $consumo_dia->cantidad = $cantidad_domingo;
            $consumo_dia->diametro_pienso = $pellet->diametro;
            $consumo_dia->pienso_id = $pienso->id;
            $consumo_dia->pienso = $pienso->nombre;
            $consumo_dia->proveedor_id = $proveedor_id;
            $consumo_dia->proveedor = $proveedor->nombre;
            $consumo_dia->codigo_pienso = $pienso->codigo;
            $consumo_dia->save();


            return Redirect::to('consumo/semanal/'. $year . '/' . $week);
            
        } // Fin getWeekSave

        // Función que actualiza o genera los datos de consumos simulados en base a la cantidad de alimento
        // reflejada en la tabla de producción_simulada
        public function ActualizarConsumoSimulado($jaula, $fecha_ini, $fecha_fin, $proveedor_id)
        {
                      
           $fecha_inicial = $fecha_ini;
           $fecha_final   = $fecha_fin;
           //$proveedor_id  = 0;
           //echo 'Jaula: ' . $jaula;
           //var_dump($fecha_inicial);
           //var_dump($fecha_final);
           while ($fecha_inicial <= $fecha_final)
           {
              // Rescatamos el registro de la tabla produccion_simulada con los datos iniciales.
              $dato_produccion_simulada = ProduccionSimuladas::where('unitname', '=', $jaula)
                                                             ->where('date', '=', $fecha_inicial)
                                                             ->first();
              //echo ' Dia : '.$fecha_inicial->format('Y-m-d');
              if (count($dato_produccion_simulada) == 0) {return true; }
              // Buscamos si existen datos en la tabla de consumos simulados para dicha fecha, jaula y lote.
              $datos_consumo_simulados  = Consumo::where('jaula', '=', $jaula)
                                                 ->where('lote', '=', $dato_produccion_simulada->groupid)
                                                 ->where('fecha', '=', $fecha_inicial)
                                                 ->orderBy('diametro_pienso')
                                                 ->get();
              $cantidad_toma = $dato_produccion_simulada->cantidad_toma;
              //echo 'Cantidad de la toma: ' . $cantidad_toma;
              $porcentaje_toma = -1;
              // Si existen datos previos de consumos, debemos actualizar la cantidad total de alimentación.
              if (count($datos_consumo_simulados) > 0)
                {
                 //echo 'Existen datos de consumo para la jaula ' . $jaula;
                 // Comprobamos si hay más de un registro de alimentación para ese mismo día, para poder repartir
                 // las cantidades correctamente.
                 $i=0;
                 //echo 'Existen ' . count($datos_consumo_simulados) . ' registros de consumo';
                 //var_dump($fecha_inicial);
                 foreach ($datos_consumo_simulados as $dato_consumo_simulado) 
                  { 
                   //echo 'Lote: ' . $dato_produccion_simulada->unitname;
                   //echo 'Jaula: ' . $jaula;
                   //var_dump($fecha_inicial);
                   //echo 'Existen ' . count($datos_consumo_simulados) . ' registros de consumo';
                   $proveedor_id = $dato_consumo_simulado->proveedor_id;
                   if ($porcentaje_toma < 0)
                    {
                        //echo ' Es la primera vez que entramos en el bucle. Porcentaje toma vale: ' . $porcentaje_toma;
                        if (count($datos_consumo_simulados) == 1)
                        {
                           $porcentaje_toma = 100;
                        }
                        else
                        {
                           $porcentaje_toma = $dato_consumo_simulado->porcentaje_estrategia;
                        }
                        

                        //echo ' Cambiamos el valor, y ahora vale: ' . $porcentaje_toma;
                        $cantidad_a_repartir = round((($cantidad_toma * $porcentaje_toma) / 100), 0, PHP_ROUND_HALF_DOWN);
                        $cantidad_a_repartir = number_format(ceil(($cantidad_a_repartir/25))*25, 0, '.', '');
                        //echo 'Calculamos la nueva cantidad de la toma, y ésta es de: ' . $cantidad_a_repartir;

                        $porcentaje_toma = (round($cantidad_a_repartir / $cantidad_toma * 100,0, PHP_ROUND_HALF_DOWN));
                        //echo 'Finalmente ajustamos el nuevo valor del porcentaje: ' . $porcentaje_toma;
                        $dato_consumo_simulado->porcentaje_estrategia = $porcentaje_toma;
                        $dato_consumo_simulado->cantidad = $cantidad_a_repartir;
                        $dato_consumo_simulado->save();
                        //echo 'Antes de realizar la resta vale: ' . $porcentaje_toma;
                        $porcentaje_toma = 100 - $porcentaje_toma;

                    }
                    else
                    {
                        $cantidad_a_repartir = round((($cantidad_toma * $porcentaje_toma) / 100), 0, PHP_ROUND_HALF_DOWN);
                        $cantidad_a_repartir = number_format(ceil(($cantidad_a_repartir/25))*25, 0, '.', '');
                        $dato_consumo_simulado->porcentaje_estrategia = $porcentaje_toma;
                        $dato_consumo_simulado->cantidad = $cantidad_a_repartir;
                        $dato_consumo_simulado->save();
                        //$porcentaje_toma = -1;
                    }


                 
                   $i++; 
                }
            } 
           else // Si no existen, debemos crearlo.
            {
              //echo 'No Existen datos de consumo para la jaula ' . $jaula;
              if ($proveedor_id == 0)
               {
                 $proveedor_id = 1;
               }

               if (($proveedor_id == 1)  and (($dato_produccion_simulada->stock_avg_ini >= 40) and ($dato_produccion_simulada->stock_avg_ini < 85)))
               {
                return TRUE;
               }
               if (($proveedor_id == 2)  and (($dato_produccion_simulada->stock_avg_ini >= 25) and ($dato_produccion_simulada->stock_avg_ini < 40)))
               {
                return TRUE;
               }
               if ($jaula == 'J003')
                        { 
                           echo ' El peso medio de la J003 es ' . $dato_produccion_simulada->stock_avg_ini . ' el día ' . $fecha_inicial->format('Y-m-d');
                        }
               // Buscamos el tipo de grano adecuado al peso medio del pez. Primero buscamos si le corresponde un único tipo de grano
                    $pellets = DB::select('Select id, diametro
                                             from  tamanio_pellets
                                            where (transito <= ? and transito+1 > ? and proveedor_pienso_id = ?) 
                                               or (pm_min <= ? and pm_max >  ? and proveedor_pienso_id = ?) 
                                         order by diametro', array($dato_produccion_simulada->stock_avg_ini, 
                                                                  $dato_produccion_simulada->stock_avg_ini,
                                                                  $proveedor_id,
                                                                  $dato_produccion_simulada->stock_avg_ini,
                                                                  $dato_produccion_simulada->stock_avg_ini,
                                                                  $proveedor_id));
                    //echo 'Peso medio de la jaula: ' . $dato_produccion_simulada->stock_avg_ini;
                    //var_dump($pellets); 

               // Buscamos los datos maestros de la granja, la jaula, el lote y el proveedor de pienso
                    $granja = Granja::where('nombre', '=', $dato_produccion_simulada->site)->first();
                    $jaulas  = Jaula::where('nombre', '=', $dato_produccion_simulada->unitname)->first();
                    $lote   = Lote::where('nombre', '=', $dato_produccion_simulada->groupid)->first();   
                    $proveedor = Proveedorpienso::find($proveedor_id);      
                $num_registros = 0;
                foreach ($pellets as $pellet)
                 {
                    if ($jaula == 'J003')
                        { 
                            echo 'El nº de pellets es de ' . count($pellets) . ' para la jaula ' . $jaula  . ' y el proveedor es' . $proveedor_id;
                            echo ' Num registro vale: ' . $num_registros;

                        }
                    
                    if ($num_registros == 1)
                    {
                       $pellet_id_2 = $pellet->id;
                       $diametro_pellet_2 = $pellet->diametro;
                       $num_registros++;
                       if ($jaula == 'J003')
                        {
                          echo ' El id del pellet 2 es ' . $pellet_id_2 . ' y su diametro es ' . $diametro_pellet_2 . ' y el proveedor es' . $proveedor_id;
                          echo ' Num registro vale: ' . $num_registros;
                          echo ' El id del pellet 1 es ' . $pellet_id_1 . ' y su diametro es ' . $diametro_pellet_1 . ' y el proveedor es' . $proveedor_id;
                             echo ' Num registro vale: ' . $num_registros;
                        }
                    }   
                    if ($num_registros == 0)
                    {
                        $pellet_id_1 = $pellet->id;
                        $diametro_pellet_1 = $pellet->diametro;
                        $num_registros++;
                        if ($jaula == 'J003')
                           {
                             echo ' El id del pellet 1 es ' . $pellet_id_1 . ' y su diametro es ' . $diametro_pellet_1 . ' y el proveedor es' . $proveedor_id;
                             echo ' Num registro vale: ' . $num_registros;
                           }
                    }
                 }

                 if ($num_registros > 1) // Hay transición de piensos. Durante 15 días alternamos los dos tipos de piensos.
                  {
                    for ($i = 1; $i <= 15; $i++)
                    { 
                      //Recuperamos los datos de la tabla de producción simulada
                      $dato_produccion_simulada_transicion = ProduccionSimuladas::where('unitname', '=', $jaula)
                                                                                ->where('date', '=', $fecha_inicial)
                                                                                ->first();
              
                      if (count($dato_produccion_simulada_transicion) == 0) {return true; }
                      // Repartimos la cantidad al 50% 
                      $cantidad1   = number_format((ceil(($dato_produccion_simulada_transicion->cantidad_toma * 0.5)/25))*25,0, '.', '');
                      $porcentaje1 = number_format(ceil($cantidad1 / $dato_produccion_simulada_transicion->cantidad_toma), 0, '.', '') * 100 ;
                      $cantidad2   = $dato_produccion_simulada_transicion->cantidad_toma - $cantidad1;
                      $porcentaje2 = 100 - $porcentaje1;

                      // Primer tipo de pienso
                      $pienso1 = Pienso::where('diametro_pellet_id', '=', $pellet_id_1)
                                    ->where('proveedor_id', '=', $proveedor_id)
                                    ->orderby('updated_at')
                                    ->first();
                      echo ' El id del pellet 1 es ' . $pellet_id_1 . ' y su diametro es ' . $diametro_pellet_1 . ' así que lo vamos a buscar' . ' y el proveedor es' . $proveedor_id;
                      $nuevo_registro_consumo = new Consumo();           
                      $nuevo_registro_consumo->granja_id             =$granja->id;
                      $nuevo_registro_consumo->granja                =$granja->nombre;
                      $nuevo_registro_consumo->jaula_id              =$jaulas->id;
                      $nuevo_registro_consumo->jaula                 =$jaulas->nombre;
                      $nuevo_registro_consumo->lote_id               =$lote->id;
                      $nuevo_registro_consumo->lote                  =$lote->nombre;
                      $nuevo_registro_consumo->proveedor_id          =$proveedor->id;
                      $nuevo_registro_consumo->proveedor             =$proveedor->nombre;
                      $nuevo_registro_consumo->pienso_id             =$pienso1->id;
                      $nuevo_registro_consumo->pienso                =$pienso1->nombre;
                      $nuevo_registro_consumo->codigo_pienso         =$pienso1->codigo;
                      $nuevo_registro_consumo->diametro_pienso       =$diametro_pellet_1; 
                      $nuevo_registro_consumo->cantidad_recomendada  =$dato_produccion_simulada_transicion->cantidad_toma_modelo;
                      $nuevo_registro_consumo->porcentaje_estrategia =$porcentaje1;
                      $nuevo_registro_consumo->cantidad              =$cantidad1;
                      $nuevo_registro_consumo->fecha                 =$fecha_inicial;
                      $nuevo_registro_consumo->save();

                      // Segundo tipo de pienso
                      $pienso2 = Pienso::where('diametro_pellet_id', '=', $pellet_id_2)
                                    ->where('proveedor_id', '=', $proveedor_id)
                                    ->orderby('updated_at')
                                    ->first();

                      $nuevo_registro_consumo = new Consumo();           
                      $nuevo_registro_consumo->granja_id             =$granja->id;
                      $nuevo_registro_consumo->granja                =$granja->nombre;
                      $nuevo_registro_consumo->jaula_id              =$jaulas->id;
                      $nuevo_registro_consumo->jaula                 =$jaulas->nombre;
                      $nuevo_registro_consumo->lote_id               =$lote->id;
                      $nuevo_registro_consumo->lote                  =$lote->nombre;
                      $nuevo_registro_consumo->proveedor_id          =$proveedor->id;
                      $nuevo_registro_consumo->proveedor             =$proveedor->nombre;
                      $nuevo_registro_consumo->pienso_id             =$pienso2->id;
                      $nuevo_registro_consumo->pienso                =$pienso2->nombre;
                      $nuevo_registro_consumo->codigo_pienso         =$pienso2->codigo;
                      $nuevo_registro_consumo->diametro_pienso       =$diametro_pellet_2; 
                      $nuevo_registro_consumo->cantidad_recomendada  =$dato_produccion_simulada_transicion->cantidad_toma_modelo;
                      $nuevo_registro_consumo->porcentaje_estrategia =$porcentaje2;
                      $nuevo_registro_consumo->cantidad              =$cantidad2;
                      $nuevo_registro_consumo->fecha                 =$fecha_inicial;
                      $nuevo_registro_consumo->save();

                      $fecha_inicial->modify('+1 day');

                    }
                    $fecha_inicial->modify('-1 day');
                  }
                 else // Le corresponde un sólo tipo de pienso
                  {
                    $pienso = Pienso::where('diametro_pellet_id', '=', $pellet_id_1)
                                    ->where('proveedor_id', '=', $proveedor_id)
                                    ->orderby('updated_at')
                                    ->first();
                         
               
                    $nuevo_registro_consumo = new Consumo();           
                    $nuevo_registro_consumo->granja_id             =$granja->id;
                    $nuevo_registro_consumo->granja                =$granja->nombre;
                    $nuevo_registro_consumo->jaula_id              =$jaulas->id;
                    $nuevo_registro_consumo->jaula                 =$jaulas->nombre;
                    $nuevo_registro_consumo->lote_id               =$lote->id;
                    $nuevo_registro_consumo->lote                  =$lote->nombre;
                    $nuevo_registro_consumo->proveedor_id          =$proveedor->id;
                    $nuevo_registro_consumo->proveedor             =$proveedor->nombre;
                    $nuevo_registro_consumo->pienso_id             =$pienso->id;
                    $nuevo_registro_consumo->pienso                =$pienso->nombre;
                    $nuevo_registro_consumo->codigo_pienso         =$pienso->codigo;
                    $nuevo_registro_consumo->diametro_pienso       =$diametro_pellet_1; 
                    $nuevo_registro_consumo->cantidad_recomendada  =$dato_produccion_simulada->cantidad_toma_modelo;
                    $nuevo_registro_consumo->porcentaje_estrategia =100;
                    $nuevo_registro_consumo->cantidad              =number_format(ceil(($dato_produccion_simulada->cantidad_toma/25))*25, 0, '.', '');
                    $nuevo_registro_consumo->fecha                 =$fecha_inicial;
                    $nuevo_registro_consumo->save();
                  }


                 /*
                else // Al pez le corresponde dos tipos de pienso xq entra en la transición de dos granos.
                 {
                    //echo 'El pez comienza la transición de la alimentación';
                    $pellet = Pellet::where('proveedor_pienso_id', '=', $proveedor_id)
                                    ->where('transito', '<=', $dato_produccion_simulada->stock_avg_ini)
                                    ->orderby('diametro')
                                    ->get();
                    // Como hemos ordenado los posibles tamaños de pellet por su diametro, sólo vamos a seleccionar los dos primeros.
                    $j = 0;
                    foreach($pellet as $p)
                     {
                        if ($j==0)
                         {
                           $pellet_id_1 = $p->id;
                           $pellet_diametro_1 = $p->diametro;
                           //echo ' Pelet id 1: ' . $pellet_id_1;
                           //echo ' $pellet_diametro_1: ' . $pellet_diametro_1;
                           $j++;
                         }
                         if ($j==1)
                         {
                           $pellet_id_2 = $p->id;
                           $pellet_diametro_2 = $p->diametro;
                           //echo 'Pelet id 2: ' . $pellet_id_2;
                           //echo ' $pellet_diametro_2: ' . $pellet_diametro_2;
                           $j++;
                         }
                     }
                    
                    // Además, debemos tener en cuenta que la transición dura 15 días
                    // Comprobamos que entre la fecha actual y la fecha final hay más de 15 días, sino ampliamos la fecha final
                    // Nueva parte para calcular la diferencia de dias
                     $diferencia = strtotime($fecha_final->format('Y-m-d')) - strtotime($fecha_inicial->format('Y-m-d'));
                     $diferencia = floor($diferencia/86400) - 1;
                     //echo 'Diferencia ' . $diferencia;
                    //$diferencia = $fecha_final->diff($fecha_inicial);
                    //echo 'Diferencia de días: ' . var_dump($diferencia);
                    
                    if ($diferencia < 15)
                     {
                        $fecha_final->modify('+' . (15-$diferencia) .'day');
                     }
                    for ($i=1; $i<15; $i++) //Durante 15 días aplicamos la transición
                     {
                        // Rescatamos el registro de la tabla produccion_simulada con los datos iniciales.
                        //echo ' Jaula ' .$jaula;
                        //echo ' Día ' .$fecha_inicial->format('Y-m-d');
                        $dato_produccion_simulada = ProduccionSimuladas::where('unitname', '=', $jaula)
                                                                       ->where('date', '=', $fecha_inicial)
                                                                       ->first();
                        //echo 'Dato produccion simulada: ' . $dato_produccion_simulada;
                        if (count($dato_produccion_simulada)== 0)
                        {
                            return TRUE;
                        }
                        // Buscamos los datos maestros de la granja, la jaula, el lote y el proveedor de pienso
                        $granja = Granja::where('nombre', '=', $dato_produccion_simulada->site)->first();
                        $jaulas  = Jaula::where('nombre', '=', $dato_produccion_simulada->unitname)->first();
                        $lote   = Lote::where('nombre', '=', $dato_produccion_simulada->groupid)->first();   
                        $proveedor = Proveedorpienso::find($proveedor_id);   

                        //echo 'Lote: ' . $lote;   
                        $cantidad_toma = $dato_produccion_simulada->cantidad_toma;   
                        $porcentaje_1 = 100 - ($i * 7);   
                        $cantidad_a_repartir = round((($cantidad_toma * $porcentaje_1) / 100), 0, PHP_ROUND_HALF_DOWN);
                        $cantidad_a_repartir = number_format(ceil(($cantidad_a_repartir/25))*25, 0);
                        
                        $porcentaje_toma = (round($cantidad_a_repartir / $cantidad_toma * 100,0, PHP_ROUND_HALF_DOWN)); 
                        $pienso = Pienso::where('diametro_pellet_id', '=', $pellet_id_1)
                                        ->where('proveedor_id', '=', $proveedor_id)
                                        ->orderby('updated_at')
                                        ->first();

                        //Generamos el primer registro de consumo correspondiente
                        $nuevo_registro_consumo = new Consumo();           
                        $nuevo_registro_consumo->granja_id             =$granja->id;
                        $nuevo_registro_consumo->granja                =$granja->nombre;
                        $nuevo_registro_consumo->jaula_id              =$jaulas->id;
                        $nuevo_registro_consumo->jaula                 =$jaulas->nombre;
                        $nuevo_registro_consumo->lote_id               =$lote->id;
                        $nuevo_registro_consumo->lote                  =$lote->nombre;
                        $nuevo_registro_consumo->proveedor_id          =$proveedor->nombre;
                        $nuevo_registro_consumo->proveedor             =$proveedor->id;
                        $nuevo_registro_consumo->pienso_id             =$pienso->id;
                        $nuevo_registro_consumo->pienso                =$pienso->nombre;
                        $nuevo_registro_consumo->codigo_pienso         =$pienso->codigo;
                        $nuevo_registro_consumo->diametro_pienso       =$pellet_diametro_1; 
                        $nuevo_registro_consumo->cantidad_recomendada  =$cantidad_a_repartir;
                        $nuevo_registro_consumo->porcentaje_estrategia =$porcentaje_1;
                        $nuevo_registro_consumo->cantidad              =$cantidad_a_repartir;
                        $nuevo_registro_consumo->fecha                 =$fecha_inicial;
                        $nuevo_registro_consumo->save();     

                        //Generamos el segundo registro de consumo correspondiente  

                        $pienso = Pienso::where('diametro_pellet_id', '=', $pellet_id_2)
                                        ->where('proveedor_id', '=', $proveedor_id)
                                        ->orderby('updated_at')
                                        ->first();

                        $nuevo_registro_consumo = new Consumo();           
                        $nuevo_registro_consumo->granja_id             =$granja->id;
                        $nuevo_registro_consumo->granja                =$granja->nombre;
                        $nuevo_registro_consumo->jaula_id              =$jaulas->id;
                        $nuevo_registro_consumo->jaula                 =$jaulas->nombre;
                        $nuevo_registro_consumo->lote_id               =$lote->id;
                        $nuevo_registro_consumo->lote                  =$lote->nombre;
                        $nuevo_registro_consumo->proveedor_id          =$proveedor->nombre;
                        $nuevo_registro_consumo->proveedor             =$proveedor->id;
                        $nuevo_registro_consumo->pienso_id             =$pienso->id;
                        $nuevo_registro_consumo->pienso                =$pienso->nombre;
                        $nuevo_registro_consumo->codigo_pienso         =$pienso->codigo;
                        $nuevo_registro_consumo->diametro_pienso       =$pellet_diametro_2; 
                        $nuevo_registro_consumo->cantidad_recomendada  =$cantidad_toma - $cantidad_a_repartir;
                        $nuevo_registro_consumo->porcentaje_estrategia =100 - $porcentaje_1;
                        $nuevo_registro_consumo->cantidad              =$cantidad_toma - $cantidad_a_repartir;
                        $nuevo_registro_consumo->fecha                 =$fecha_inicial;
                        $nuevo_registro_consumo->save();                                      
                        

                        $fecha_inicial->modify('+1 day');
                     }
                     $fecha_inicial->modify('-1 day');
                 }*/
            }   
            $fecha_inicial->modify('+1 day');
           }
           return TRUE;
        } // Fin ActualizarConsumoSimulado

        public function ActualizarConsumoSimuladoII($jaula, $fecha_ini, $fecha_fin, $proveedor_id)
        {
          $fecha_inicial = $fecha_ini;
          $fecha_final   = $fecha_fin;
          $id_proveedor  = $proveedor_id;
          //echo 'Jaula: ' . $jaula;
          //var_dump($fecha_inicial);
          //var_dump($fecha_final);
          $fecha_inicial->format('Y-m-d');
          //echo ' Comenzamos la actualización de consumos para la jaula ' . $jaula;
          //echo ' El intervalo de fecha es desde el ' . $fecha_inicial->format('Y-m-d') . ' al ' . $fecha_final->format('Y-m-d');
          //echo ' comenzamos.... ';
          // Recorremos todo el intervalo temporal
          while ($fecha_inicial <= $fecha_final)
            {
                $num_registro_actualizar = 0;
                //echo ' ' . $fecha_inicial->format('Y-m-d');
                // Buscamos el registro de producción simulada de la jaula
                $produccion = ProduccionSimuladas::where('unitname','=',$jaula)
                                                 ->where('date', '=', $fecha_inicial)
                                                 ->first();

                if (count($produccion)==0)
                 {
                   // No existe dato de producción para esa jaula. Salimos de la función.
                   return False;
                 }

                // Buscamos los datos maestros
                // Jaula
                $jaula_maestro = Jaula::where('nombre', '=', $produccion->unitname)
                                      ->first();
                // Granja 
                $granja_maestro = Granja::where('nombre', '=', $produccion->site)
                                        ->first();

                // Lote
                $lote_maestro = Lote::where('nombre', '=', $produccion->groupid)
                                    ->first();

                // Buscamos el registro de consumo para la fecha en la que nos encontramos
                // y que pertenezca a la jaula
                $consumo = Consumo::where('jaula', '=', $jaula)
                                  ->where('fecha', '=', $fecha_inicial)
                                  ->where('lote', '=', $produccion->groupid)
                                  ->orderby('diametro_pienso')
                                  ->get();
                
                if (count($consumo)>0)
                  {
                    // Existe consumo
                    //echo (' 2');
                    // Buscamos si la jaula está en transito de piensos.
                    $jaula_transito = Controltransito::where('jaula', '=', $jaula)
                                                     ->where('lote' , '=', $produccion->groupid)
                                                     ->where('fecha_inicial', '<=', $fecha_inicial)
                                                     ->where('fecha_final', '>=', $fecha_inicial)
                                                     ->first();
                    if (count($jaula_transito)>0)
                    {
                       //echo(' 8');
                       // Se encuentra en tránsito
                        
                       // Buscamos si en la tabla de consumos hay dos registros para ese día
                       if(count($consumo)>1)
                       {
                        //echo(' 10');
                        // Si existe los dos registros
                         
                         // Aplicamos el 50% de la toma al pienso                  
                        $cantidad_pienso_inicial = ceil(($produccion->cantidad_toma * 0.5)/25)*25;
                        $porcentaje_pienso_inicial = floor(($cantidad_pienso_inicial/$produccion->cantidad_toma)*100);

                        $cantidad_pienso_final = $produccion->cantidad_toma - $cantidad_pienso_inicial;
                        $porcentaje_pienso_final = 100 - $porcentaje_pienso_inicial;

                        $num_registro_actualizar = 0;
                        foreach($consumo as $consumo_actualizar)
                        {
                            if ($num_registro_actualizar == 1)
                            {
                                //echo ' Hoy es día ' . $fecha_inicial->format('Y-m-d');
                                //echo ' Cantidad a repartir ' . $produccion->cantidad_toma;
                                //echo ' Num registro a actualizar ' . $num_registro_actualizar;
                                //echo ' % pienso final ' . $porcentaje_pienso_final;
                                //echo ' Cantidad pienso final ' . $cantidad_pienso_final;
                                

                                $consumo_actualizar->cantidad_recomendada  =$produccion->cantidad_toma_modelo;
                                $consumo_actualizar->porcentaje_estrategia =$porcentaje_pienso_final;
                                $consumo_actualizar->cantidad              =$cantidad_pienso_final;
                                $consumo_actualizar->save();
                                $num_registro_actualizar++;
                            }

                            if ($num_registro_actualizar == 0) 
                            {
                                //echo ' Hoy es día ' . $fecha_inicial->format('Y-m-d');
                                //echo ' Cantidad a repartir ' . $produccion->cantidad_toma;
                                //echo ' Num registro a actualizar ' . $num_registro_actualizar;
                                //echo ' % pienso inicial ' . $porcentaje_pienso_inicial;
                                //echo ' Cantidad pienso inicial ' . $cantidad_pienso_inicial;
                                
                                $consumo_actualizar->cantidad_recomendada  =$produccion->cantidad_toma_modelo;
                                $consumo_actualizar->porcentaje_estrategia =$porcentaje_pienso_inicial;
                                $consumo_actualizar->cantidad              =$cantidad_pienso_inicial;
                                $consumo_actualizar->save();
                                $num_registro_actualizar++;
                            }

                        }
                       }
                       else
                       {
                        //echo(' 9');
                        // No existe los dos registros de consumo
                        // Debemos crear el segundo registro de consumo
 
                         // Aplicamos el 50% de la toma al pienso                  
                        $cantidad_pienso_inicial = ceil(($produccion->cantidad_toma * 0.5)/25)*25;
                        $porcentaje_pienso_inicial = floor(($cantidad_pienso_inicial/$produccion->cantidad_toma)*100);

                        $cantidad_pienso_final = $produccion->cantidad_toma - $cantidad_pienso_inicial;
                        $porcentaje_pienso_final = 100 - $porcentaje_pienso_inicial;
                        $num_registro_actualizar = 0;
                        foreach($consumo as $consumo_actualizar)
                        {
                           if ($num_registro_actualizar == 0) 
                            {
                                
                                $consumo_actualizar->cantidad_recomendada  =$produccion->cantidad_toma_modelo;
                                $consumo_actualizar->porcentaje_estrategia =$porcentaje_pienso_inicial;
                                $consumo_actualizar->cantidad              =$cantidad_pienso_inicial;
                                $consumo_actualizar->save();
                                $num_registro_actualizar++;
                            } 
                        }
                        // Buscamos el pienso final
                        $pienso_final = Pienso::where('diametro_pellet_id', '=', $jaula_transito->id_pienso_transito_final)
                                              ->orderby('updated_at')
                                              ->first();  
                        $proveedor_pienso_final  = Proveedorpienso::find($pienso_final->proveedor_id);  
                        $diametro_pellet_final  = Pellet::find($jaula_transito->id_pienso_transito_final);

                        // Consumo del pienso final
                        $nuevo_registro_consumo = new Consumo();           
                        $nuevo_registro_consumo->granja_id             =$granja_maestro->id;
                        $nuevo_registro_consumo->granja                =$granja_maestro->nombre;
                        $nuevo_registro_consumo->jaula_id              =$jaula_maestro->id;
                        $nuevo_registro_consumo->jaula                 =$jaula_maestro->nombre;
                        $nuevo_registro_consumo->lote_id               =$lote_maestro->id;
                        $nuevo_registro_consumo->lote                  =$lote_maestro->nombre;
                        $nuevo_registro_consumo->proveedor_id          =$proveedor_pienso_final->id;
                        $nuevo_registro_consumo->proveedor             =$proveedor_pienso_final->nombre;
                        $nuevo_registro_consumo->pienso_id             =$pienso_final->id;
                        $nuevo_registro_consumo->pienso                =$pienso_final->nombre;
                        $nuevo_registro_consumo->codigo_pienso         =$pienso_final->codigo;
                        $nuevo_registro_consumo->diametro_pienso       =$diametro_pellet_final->diametro; 
                        $nuevo_registro_consumo->cantidad_recomendada  =$produccion->cantidad_toma_modelo;
                        $nuevo_registro_consumo->porcentaje_estrategia =$porcentaje_pienso_final;
                        $nuevo_registro_consumo->cantidad              =$cantidad_pienso_final;
                        $nuevo_registro_consumo->fecha                 =$fecha_inicial;
                        $nuevo_registro_consumo->save();
                        
                        $id_proveedor = $proveedor_pienso_final->id;
                       }
                    }
                    else
                    {
                        // No se encuentra en la tabla de tránsito
                        //echo(' 7');
                        $tienepesomedioparacomenzartransito = DB::select('Select id, diametro
                                                                            from  tamanio_pellets
                                                                           where (transito <= ? and transito+2 > ? and proveedor_pienso_id = ?) 
                                                                        order by diametro', array($produccion->stock_avg_ini, 
                                                                                                  $produccion->stock_avg_ini,
                                                                                                  $id_proveedor));
                        if (count($tienepesomedioparacomenzartransito)>0)
                          {
                              // Tiene peso medio para comenzar el tránsito de pienso
                              // Buscamos en las reglas de tránsito cual le corresponde.
                              echo(' 12');
                               foreach ($tienepesomedioparacomenzartransito as $tienepesomedio)
                                {
                                  $regla_transito = Reglastransito::where('id_pienso_transito_inicial', '=', $tienepesomedio->id)
                                                                  ->first();
                                  $fecha_final_temp = new DateTime($fecha_inicial->format('Y-m-d'));
                                  //$fecha_final_temp = $fecha_inicial;
                                  $fecha_final_temp->modify('+14 day');
                                  //echo 'Vamos a registrar un nuevo control de tránsito: Fecha Inicial ' . $fecha_inicial->format('Y-m-d') . ' y la Fecha Final ' . $fecha_final_temp->format('Y-m-d');
                                  // Insertamos los datos en la tabla de tránsito.
                                  $nuevo_registro_control_transito = new Controltransito();
                                  $nuevo_registro_control_transito->jaula_id                   = $jaula_maestro->id;
                                  $nuevo_registro_control_transito->jaula                      = $jaula_maestro->nombre;
                                  $nuevo_registro_control_transito->lote_id                    = $lote_maestro->id;
                                  $nuevo_registro_control_transito->lote                       = $lote_maestro->nombre;
                                  $nuevo_registro_control_transito->id_regla_transito          = $regla_transito->id;
                                  $nuevo_registro_control_transito->id_pienso_transito_inicial = $regla_transito->id_pienso_transito_inicial;
                                  $nuevo_registro_control_transito->id_pienso_transito_final   = $regla_transito->id_pienso_transito_final;
                                  $nuevo_registro_control_transito->fecha_inicial              = $fecha_inicial;
                                  $nuevo_registro_control_transito->fecha_final                = $fecha_final_temp;
                                  $nuevo_registro_control_transito->save();
                                  
                                  // Generamos los dos registros de consumo.
                                  // Buscamos el pienso inicial
                                  $pienso_inicial = Pienso::where('diametro_pellet_id', '=', $regla_transito->id_pienso_transito_inicial)
                                                          ->orderby('updated_at')
                                                          ->first();

                                  $proveedor_pienso_inicial = Proveedorpienso::find($pienso_inicial->proveedor_id);
                                  $diametro_pellet_inicial  = Pellet::find($regla_transito->id_pienso_transito_inicial);
                        
                                  // Buscamos el pienso final
                                  $pienso_final = Pienso::where('diametro_pellet_id', '=', $regla_transito->id_pienso_transito_final)
                                                        ->orderby('updated_at')
                                                        ->first();  
                                  $proveedor_pienso_final  = Proveedorpienso::find($pienso_final->proveedor_id);  
                                  $diametro_pellet_final  = Pellet::find($regla_transito->id_pienso_transito_final);

                                  // Aplicamos el 50% de la toma al pienso                  
                                  $cantidad_pienso_inicial = ceil(($produccion->cantidad_toma * 0.5)/25)*25;
                                  $porcentaje_pienso_inicial = floor(($cantidad_pienso_inicial/$produccion->cantidad_toma)*100);

                                  $cantidad_pienso_final = $produccion->cantidad_toma - $cantidad_pienso_inicial;
                                  $porcentaje_pienso_final = 100 - $porcentaje_pienso_inicial;

                                   // Actualizamos la linea de consumo existente y generamos la nueva línea
                                   foreach($consumo as $consumo_actualizar)
                                   {
                                       if ($num_registro_actualizar == 0) 
                                       {
                                
                                           $consumo_actualizar->proveedor_id          =$proveedor_pienso_inicial->id;
                                           $consumo_actualizar->proveedor             =$proveedor_pienso_inicial->nombre;
                                           $consumo_actualizar->pienso_id             =$pienso_inicial->id;
                                           $consumo_actualizar->pienso                =$pienso_inicial->nombre;
                                           $consumo_actualizar->codigo_pienso         =$pienso_inicial->codigo;
                                           $consumo_actualizar->diametro_pienso       =$diametro_pellet_inicial->diametro; 
                                           $consumo_actualizar->cantidad_recomendada  =$produccion->cantidad_toma_modelo;
                                           $consumo_actualizar->porcentaje_estrategia =$porcentaje_pienso_inicial;
                                           $consumo_actualizar->cantidad              =$cantidad_pienso_inicial;
                                           $consumo_actualizar->fecha                 =$fecha_inicial;
                                           $consumo_actualizar->save();
                                           $num_registro_actualizar++;
                                       } 
                                   }
                                   
                                   // Consumo del pienso final
                                   $nuevo_registro_consumo = new Consumo();           
                                   $nuevo_registro_consumo->granja_id             =$granja_maestro->id;
                                   $nuevo_registro_consumo->granja                =$granja_maestro->nombre;
                                   $nuevo_registro_consumo->jaula_id              =$jaula_maestro->id;
                                   $nuevo_registro_consumo->jaula                 =$jaula_maestro->nombre;
                                   $nuevo_registro_consumo->lote_id               =$lote_maestro->id;
                                   $nuevo_registro_consumo->lote                  =$lote_maestro->nombre;
                                   $nuevo_registro_consumo->proveedor_id          =$proveedor_pienso_final->id;
                                   $nuevo_registro_consumo->proveedor             =$proveedor_pienso_final->nombre;
                                   $nuevo_registro_consumo->pienso_id             =$pienso_final->id;
                                   $nuevo_registro_consumo->pienso                =$pienso_final->nombre;
                                   $nuevo_registro_consumo->codigo_pienso         =$pienso_final->codigo;
                                   $nuevo_registro_consumo->diametro_pienso       =$diametro_pellet_final->diametro; 
                                   $nuevo_registro_consumo->cantidad_recomendada  =$produccion->cantidad_toma_modelo;
                                   $nuevo_registro_consumo->porcentaje_estrategia =$porcentaje_pienso_final;
                                   $nuevo_registro_consumo->cantidad              =$cantidad_pienso_final;
                                   $nuevo_registro_consumo->fecha                 =$fecha_inicial;
                                   $nuevo_registro_consumo->save();

                                   $id_proveedor = $proveedor_pienso_final->id;
                                }
                          }
                          else
                          {
                              // No tiene peso medio para comenzar el tránsito de pienso
                              //echo(' 11');
                              // Buscamos el tamaño del pellet que le corresponde tras la actualización.
                              $pellets = DB::select('Select id, diametro
                                                       from  tamanio_pellets
                                                      where (pm_min <= ? and pm_max >  ? and proveedor_pienso_id = ?)
                                          order by diametro', array($produccion->stock_avg_ini, 
                                                                    $produccion->stock_avg_ini,
                                                                    $id_proveedor));
                              foreach ($pellets as $pellet)
                               {
                                 $pienso = Pienso::where('diametro_pellet_id', '=', $pellet->id)
                                                 ->orderby('updated_at')
                                                 ->first();  
                                 $proveedor = Proveedorpienso::find($pienso->proveedor_id);
                                 foreach($consumo as $consumo_actualizar)
                                   {
                                       if ($num_registro_actualizar == 0) 
                                       {
                                
                                           $consumo_actualizar->proveedor_id          =$proveedor->id;
                                           $consumo_actualizar->proveedor             =$proveedor->nombre;
                                           $consumo_actualizar->pienso_id             =$pienso->id;
                                           $consumo_actualizar->pienso                =$pienso->nombre;
                                           $consumo_actualizar->codigo_pienso         =$pienso->codigo;
                                           $consumo_actualizar->diametro_pienso       =$pellet->diametro; 
                                           $consumo_actualizar->cantidad_recomendada  =$produccion->cantidad_toma_modelo;
                                           $consumo_actualizar->porcentaje_estrategia =100;
                                           $consumo_actualizar->cantidad              =$produccion->cantidad_toma;
                                           $consumo_actualizar->fecha                 =$fecha_inicial;
                                           $consumo_actualizar->save();
                                           $num_registro_actualizar++;
                                           $id_proveedor = $proveedor->id;
                                       } 
                                   }
                               }
                          }

                    }
                    //return true;
                  }
                else
                  {
                    // No existe consumo
                    //echo (' 1');
                    // Buscamos si la jaula está en transito de piensos.
                    $jaula_transito = Controltransito::where('jaula', '=', $jaula)
                                                     ->where('lote' , '=', $produccion->groupid)
                                                     ->where('fecha_inicial', '<=', $fecha_inicial)
                                                     ->where('fecha_final', '>=', $fecha_inicial)
                                                     ->first();
                    if (count($jaula_transito)>0)
                      {
                         // Se encuentra en tránsito
                         // Generamos los dos registros en la tabla de consumos
                        //echo (' 4');
                        // Buscamos el pienso inicial
                        $pienso_inicial = Pienso::where('diametro_pellet_id', '=', $jaula_transito->id_pienso_transito_inicial)
                                                ->orderby('updated_at')
                                                ->first();

                        $proveedor_pienso_inicial = Proveedorpienso::find($pienso_inicial->proveedor_id);
                        $diametro_pellet_inicial  = Pellet::find($jaula_transito->id_pienso_transito_inicial);
                        
                        // Buscamos el pienso final
                        $pienso_final = Pienso::where('diametro_pellet_id', '=', $jaula_transito->id_pienso_transito_final)
                                              ->orderby('updated_at')
                                              ->first();  
                        $proveedor_pienso_final  = Proveedorpienso::find($pienso_final->proveedor_id);  
                        $diametro_pellet_final  = Pellet::find($jaula_transito->id_pienso_transito_final);

                        // Aplicamos el 50% de la toma al pienso                  
                        $cantidad_pienso_inicial = ceil(($produccion->cantidad_toma * 0.5)/25)*25;
                        $porcentaje_pienso_inicial = floor(($cantidad_pienso_inicial/$produccion->cantidad_toma)*100);

                        $cantidad_pienso_final = $produccion->cantidad_toma - $cantidad_pienso_inicial;
                        $porcentaje_pienso_final = 100 - $porcentaje_pienso_inicial;

                        // Generamos las dos lineas de consumo

                        // Consumo del pienso inicial
                        $nuevo_registro_consumo = new Consumo();           
                        $nuevo_registro_consumo->granja_id             =$granja_maestro->id;
                        $nuevo_registro_consumo->granja                =$granja_maestro->nombre;
                        $nuevo_registro_consumo->jaula_id              =$jaula_maestro->id;
                        $nuevo_registro_consumo->jaula                 =$jaula_maestro->nombre;
                        $nuevo_registro_consumo->lote_id               =$lote_maestro->id;
                        $nuevo_registro_consumo->lote                  =$lote_maestro->nombre;
                        $nuevo_registro_consumo->proveedor_id          =$proveedor_pienso_inicial->id;
                        $nuevo_registro_consumo->proveedor             =$proveedor_pienso_inicial->nombre;
                        $nuevo_registro_consumo->pienso_id             =$pienso_inicial->id;
                        $nuevo_registro_consumo->pienso                =$pienso_inicial->nombre;
                        $nuevo_registro_consumo->codigo_pienso         =$pienso_inicial->codigo;
                        $nuevo_registro_consumo->diametro_pienso       =$diametro_pellet_inicial->diametro; 
                        $nuevo_registro_consumo->cantidad_recomendada  =$produccion->cantidad_toma_modelo;
                        $nuevo_registro_consumo->porcentaje_estrategia =$porcentaje_pienso_inicial;
                        $nuevo_registro_consumo->cantidad              =$cantidad_pienso_inicial;
                        $nuevo_registro_consumo->fecha                 =$fecha_inicial;
                        $nuevo_registro_consumo->save();

                        // Consumo del pienso final
                        $nuevo_registro_consumo = new Consumo();           
                        $nuevo_registro_consumo->granja_id             =$granja_maestro->id;
                        $nuevo_registro_consumo->granja                =$granja_maestro->nombre;
                        $nuevo_registro_consumo->jaula_id              =$jaula_maestro->id;
                        $nuevo_registro_consumo->jaula                 =$jaula_maestro->nombre;
                        $nuevo_registro_consumo->lote_id               =$lote_maestro->id;
                        $nuevo_registro_consumo->lote                  =$lote_maestro->nombre;
                        $nuevo_registro_consumo->proveedor_id          =$proveedor_pienso_final->id;
                        $nuevo_registro_consumo->proveedor             =$proveedor_pienso_final->nombre;
                        $nuevo_registro_consumo->pienso_id             =$pienso_final->id;
                        $nuevo_registro_consumo->pienso                =$pienso_final->nombre;
                        $nuevo_registro_consumo->codigo_pienso         =$pienso_final->codigo;
                        $nuevo_registro_consumo->diametro_pienso       =$diametro_pellet_final->diametro; 
                        $nuevo_registro_consumo->cantidad_recomendada  =$produccion->cantidad_toma_modelo;
                        $nuevo_registro_consumo->porcentaje_estrategia =$porcentaje_pienso_final;
                        $nuevo_registro_consumo->cantidad              =$cantidad_pienso_final;
                        $nuevo_registro_consumo->fecha                 =$fecha_inicial;
                        $nuevo_registro_consumo->save();
                        
                        $id_proveedor = $proveedor_pienso_final->id;
                      }
                    else
                      {
                         // No se encuentra en tránsito
                         // Comprobamos si tiene peso medio para comenzar el tránsito
                         // de pienso
                        //echo(' 3');
                        if (($id_proveedor  == 0) and ($proveedor_id == 0)) {$id_proveedor = 1;}
                        $tienepesomedioparacomenzartransito = DB::select('Select id, diametro
                                                                            from  tamanio_pellets
                                                                           where (transito <= ? and transito+2 > ? and proveedor_pienso_id = ?) 
                                                                        order by diametro', array($produccion->stock_avg_ini, 
                                                                                                  $produccion->stock_avg_ini,
                                                                                                  $id_proveedor));
                        if (count($tienepesomedioparacomenzartransito)>0)
                          {
                              // Tiene peso medio para comenzar el tránsito de pienso
                              // Buscamos en las reglas de tránsito cual le corresponde.
                              //echo(' 6');
                              foreach ($tienepesomedioparacomenzartransito as $tienepesomedio)
                                {
                                  $regla_transito = Reglastransito::where('id_pienso_transito_inicial', '=', $tienepesomedio->id)
                                                                  ->first();
                                  $fecha_final_temp = new DateTime($fecha_inicial->format('Y-m-d'));
                                  //$fecha_final_temp = $fecha_inicial;
                                  $fecha_final_temp->modify('+14 day');
                                  //echo 'Vamos a registrar un nuevo control de tránsito: Fecha Inicial ' . $fecha_inicial->format('Y-m-d') . ' y la Fecha Final ' . $fecha_final_temp->format('Y-m-d');
                                  // Insertamos los datos en la tabla de tránsito.
                                  $nuevo_registro_control_transito = new Controltransito();
                                  $nuevo_registro_control_transito->jaula_id                   = $jaula_maestro->id;
                                  $nuevo_registro_control_transito->jaula                      = $jaula_maestro->nombre;
                                  $nuevo_registro_control_transito->lote_id                    = $lote_maestro->id;
                                  $nuevo_registro_control_transito->lote                       = $lote_maestro->nombre;
                                  $nuevo_registro_control_transito->id_regla_transito          = $regla_transito->id;
                                  $nuevo_registro_control_transito->id_pienso_transito_inicial = $regla_transito->id_pienso_transito_inicial;
                                  $nuevo_registro_control_transito->id_pienso_transito_final   = $regla_transito->id_pienso_transito_final;
                                  $nuevo_registro_control_transito->fecha_inicial              = $fecha_inicial;
                                  $nuevo_registro_control_transito->fecha_final                = $fecha_final_temp;
                                  $nuevo_registro_control_transito->save();
                                  
                                  // Generamos los dos registros de consumo.
                                  // Buscamos el pienso inicial
                                  $pienso_inicial = Pienso::where('diametro_pellet_id', '=', $regla_transito->id_pienso_transito_inicial)
                                                          ->orderby('updated_at')
                                                          ->first();

                                  $proveedor_pienso_inicial = Proveedorpienso::find($pienso_inicial->proveedor_id);
                                  $diametro_pellet_inicial  = Pellet::find($regla_transito->id_pienso_transito_inicial);
                        
                                  // Buscamos el pienso final
                                  $pienso_final = Pienso::where('diametro_pellet_id', '=', $regla_transito->id_pienso_transito_final)
                                                        ->orderby('updated_at')
                                                        ->first();  
                                  $proveedor_pienso_final  = Proveedorpienso::find($pienso_final->proveedor_id);  
                                  $diametro_pellet_final  = Pellet::find($regla_transito->id_pienso_transito_final);

                                  // Aplicamos el 50% de la toma al pienso                  
                                  $cantidad_pienso_inicial = ceil(($produccion->cantidad_toma * 0.5)/25)*25;
                                  $porcentaje_pienso_inicial = floor(($cantidad_pienso_inicial/$produccion->cantidad_toma)*100);

                                  $cantidad_pienso_final = $produccion->cantidad_toma - $cantidad_pienso_inicial;
                                  $porcentaje_pienso_final = 100 - $porcentaje_pienso_inicial;

                                   // Generamos las dos lineas de consumo

                                   // Consumo del pienso inicial
                                   $nuevo_registro_consumo = new Consumo();           
                                   $nuevo_registro_consumo->granja_id             =$granja_maestro->id;
                                   $nuevo_registro_consumo->granja                =$granja_maestro->nombre;
                                   $nuevo_registro_consumo->jaula_id              =$jaula_maestro->id;
                                   $nuevo_registro_consumo->jaula                 =$jaula_maestro->nombre;
                                   $nuevo_registro_consumo->lote_id               =$lote_maestro->id;
                                   $nuevo_registro_consumo->lote                  =$lote_maestro->nombre;
                                   $nuevo_registro_consumo->proveedor_id          =$proveedor_pienso_inicial->id;
                                   $nuevo_registro_consumo->proveedor             =$proveedor_pienso_inicial->nombre;
                                   $nuevo_registro_consumo->pienso_id             =$pienso_inicial->id;
                                   $nuevo_registro_consumo->pienso                =$pienso_inicial->nombre;
                                   $nuevo_registro_consumo->codigo_pienso         =$pienso_inicial->codigo;
                                   $nuevo_registro_consumo->diametro_pienso       =$diametro_pellet_inicial->diametro; 
                                   $nuevo_registro_consumo->cantidad_recomendada  =$produccion->cantidad_toma_modelo;
                                   $nuevo_registro_consumo->porcentaje_estrategia =$porcentaje_pienso_inicial;
                                   $nuevo_registro_consumo->cantidad              =$cantidad_pienso_inicial;
                                   $nuevo_registro_consumo->fecha                 =$fecha_inicial;
                                   $nuevo_registro_consumo->save();

                                   // Consumo del pienso final
                                   $nuevo_registro_consumo = new Consumo();           
                                   $nuevo_registro_consumo->granja_id             =$granja_maestro->id;
                                   $nuevo_registro_consumo->granja                =$granja_maestro->nombre;
                                   $nuevo_registro_consumo->jaula_id              =$jaula_maestro->id;
                                   $nuevo_registro_consumo->jaula                 =$jaula_maestro->nombre;
                                   $nuevo_registro_consumo->lote_id               =$lote_maestro->id;
                                   $nuevo_registro_consumo->lote                  =$lote_maestro->nombre;
                                   $nuevo_registro_consumo->proveedor_id          =$proveedor_pienso_final->id;
                                   $nuevo_registro_consumo->proveedor             =$proveedor_pienso_final->nombre;
                                   $nuevo_registro_consumo->pienso_id             =$pienso_final->id;
                                   $nuevo_registro_consumo->pienso                =$pienso_final->nombre;
                                   $nuevo_registro_consumo->codigo_pienso         =$pienso_final->codigo;
                                   $nuevo_registro_consumo->diametro_pienso       =$diametro_pellet_final->diametro; 
                                   $nuevo_registro_consumo->cantidad_recomendada  =$produccion->cantidad_toma_modelo;
                                   $nuevo_registro_consumo->porcentaje_estrategia =$porcentaje_pienso_final;
                                   $nuevo_registro_consumo->cantidad              =$cantidad_pienso_final;
                                   $nuevo_registro_consumo->fecha                 =$fecha_inicial;
                                   $nuevo_registro_consumo->save();

                                   $id_proveedor = $proveedor_pienso_final->id;
                                }
                              
                          }
                        else
                          {
                            //echo(' 5');
                              // No tiene peso medio para comenzar el tránsito de pienso
                              // Buscamos el grano que le corresponde
                              $pellets = DB::select('Select id, diametro
                                                       from  tamanio_pellets
                                                      where (pm_min <= ? and pm_max >  ? and proveedor_pienso_id = ?)
                                          order by diametro', array($produccion->stock_avg_ini, 
                                                                    $produccion->stock_avg_ini,
                                                                    $id_proveedor));
                              foreach ($pellets as $pellet)
                               {
                                 $pienso = Pienso::where('diametro_pellet_id', '=', $pellet->id)
                                                 ->orderby('updated_at')
                                                 ->first();  
                                 $proveedor = Proveedorpienso::find($pienso->proveedor_id);

                                 $nuevo_registro_consumo = new Consumo();           
                                 $nuevo_registro_consumo->granja_id             =$granja_maestro->id;
                                 $nuevo_registro_consumo->granja                =$granja_maestro->nombre;
                                 $nuevo_registro_consumo->jaula_id              =$jaula_maestro->id;
                                 $nuevo_registro_consumo->jaula                 =$jaula_maestro->nombre;
                                 $nuevo_registro_consumo->lote_id               =$lote_maestro->id;
                                 $nuevo_registro_consumo->lote                  =$lote_maestro->nombre;
                                 $nuevo_registro_consumo->proveedor_id          =$proveedor->id;
                                 $nuevo_registro_consumo->proveedor             =$proveedor->nombre;
                                 $nuevo_registro_consumo->pienso_id             =$pienso->id;
                                 $nuevo_registro_consumo->pienso                =$pienso->nombre;
                                 $nuevo_registro_consumo->codigo_pienso         =$pienso->codigo;
                                 $nuevo_registro_consumo->diametro_pienso       =$pellet->diametro; 
                                 $nuevo_registro_consumo->cantidad_recomendada  =$produccion->cantidad_toma_modelo;
                                 $nuevo_registro_consumo->porcentaje_estrategia =100;
                                 $nuevo_registro_consumo->cantidad              =$produccion->cantidad_toma;
                                 $nuevo_registro_consumo->fecha                 =$fecha_inicial;
                                 $nuevo_registro_consumo->save();

                                 $id_proveedor = $proveedor->id;

                               }
                              // Creamos el único registro de consumo que le corresponde.
                          }
                      } 

                  }
                
                // Sumar un dia a la fecha inicial
                  $fecha_inicial->modify('+1 day');
            }
        }

        public function ActualizarConsumoSimuladoIII($jaula, $fecha_ini, $fecha_fin, $rango_pienso)
        {
            //
          $fecha_inicial = $fecha_ini;
          $fecha_final   = $fecha_fin;
          $id_cabecera_rango  = $rango_pienso; //
          //echo 'Jaula: ' . $jaula;
          //var_dump($fecha_inicial);
          //var_dump($fecha_final);
          //$fecha_inicial->format('Y-m-d');
          //echo ' Comenzamos la actualización de consumos para la jaula ' . $jaula;
          //echo ' El intervalo de fecha es desde el ' . $fecha_inicial->format('Y-m-d') . ' al ' . $fecha_final->format('Y-m-d');
          //echo ' comenzamos.... ';
          // Recorremos todo el intervalo temporal

          // Buscamos todos los registros de consumo de esa jaula y los borramos a partir de la fecha inicial.
          //echo 'Vamos a eliminar los registros';
          $consumos_a_eliminar = Consumo::where('jaula', '=', $jaula)
                                         ->where('fecha', '>=', $fecha_inicial)
                                         ->orderby('fecha')
                                         ->delete();
          //echo ' Registros eliminados';
          /*
          if (count($consumos_a_eliminar)>0)
          {
            // Eliminamos cada uno de los registros
            foreach($consumos_a_eliminar as $consumo_a_eliminar)
            {
                $consumo_a_eliminar->delete();
            }
          }
          */

          while ($fecha_inicial <= $fecha_final)
            {
               //echo 'Fecha inicial: ' . $fecha_inicial->format('Y-m-d');
               // Buscamos el registro de producción simulada que le corresponde a la jaula para este día
                //echo 'Buscamos el registro de producción simulada que le corresponde a la jaula para este día';
                $produccion_simulada = ProduccionSimuladas::where('unitname', '=', $jaula)
                                                          ->where('date', '=', $fecha_inicial)
                                                          ->first();
                if (count($produccion_simulada)<1) 
                { 
                  //echo 'No hemos encontrado elementos';  
                  return false;
                }
                //echo ' Elementos encontrados';
                if ($produccion_simulada->ayuno == 0)
                {
                // Buscamos en la tabla jaula_lote_rango la correspondencia de este día para la jaula y lote.
                                // Primero debemos el id de la jaula y del lote.
                                $datos_jaula_maestro = Jaula::where('nombre', '=', $jaula)->first();
                                $datos_lote_maestro  = Lote::where('nombre', '=', $produccion_simulada->groupid)->first();
                                $datos_granja_maestro = Granja::where('nombre', '=', $produccion_simulada->site)->first();
                
                
                                $jaula_lote_rango = JaulaLoteRango::where('jaula_id', '=', $datos_jaula_maestro->id)
                                                                  ->where('lote_id', '=', $datos_lote_maestro->id)
                                                                  ->where('fecha_inicio', '<=', $fecha_inicial)
                                                                  ->orderby('fecha_inicio', 'desc')
                                                                  ->orderby('created_at', 'desc')
                                                                  ->first();
                                if (count($jaula_lote_rango)<1)
                                {
                                    //No tenemos rango asignado al lote en dicha jaula
                                    //echo 'No tenemos rango asignado al lote en dicha jaula';
                
                                    $cabecera_rango_asignar = CabeceraRangos::where('predeterminado', '=', 1)->first();
                                    $cabecera_rango_id = $cabecera_rango_asignar->id;
                                    // Generamos el registro en la tabla de jaula_lote_rango 
                                    $nuevo_registro_jaula_lote_rango = new JaulaLoteRango();
                                    $nuevo_registro_jaula_lote_rango->jaula_id          = $datos_jaula_maestro->id;
                                    $nuevo_registro_jaula_lote_rango->lote_id              = $datos_lote_maestro->id;
                                    $nuevo_registro_jaula_lote_rango->cabecera_rango_id = $cabecera_rango_id;
                                    $nuevo_registro_jaula_lote_rango->fecha_inicio      = $fecha_inicial;
                                    $nuevo_registro_jaula_lote_rango->save();
                                    //echo 'nuevo_registro_jaula_lote_rango almacenado';
                                }
                                else
                                {
                                    //echo 'tenemos rango asignado al lote en dicha jaula';
                                    $cabecera_rango_id= $jaula_lote_rango->cabecera_rango_id;
                                }
                
                                //Buscamos el pienso que le corresponde al lote, teniendo en cuenta el peso medio y la tabla de asignación de piensos
                                $tamanio_pellets_rango = DetalleRangos::where('cabecera_rango_id', '=', $cabecera_rango_id)
                                                                      ->where('pm_min', '<=', $produccion_simulada->stock_avg_ini)
                                                                      ->where('pm_transito', '>', $produccion_simulada->stock_avg_ini)
                                                                      ->first();
                                if (count($tamanio_pellets_rango)>0)
                                {
                                    // Sólo le corresponde un sólo pienso.
                                    $pienso = Pienso::where('diametro_pellet_id', '=', $tamanio_pellets_rango->tamanio_pellet_id)
                                                    ->orderby('updated_at')
                                                    ->first();  
                                    $proveedor = Proveedorpienso::find($pienso->proveedor_id);
                                    $tamanio_pellet = Pellet::find($tamanio_pellets_rango->tamanio_pellet_id);
                
                                    $nuevo_registro_consumo = new Consumo();           
                                    $nuevo_registro_consumo->granja_id             =$datos_granja_maestro->id;
                                    $nuevo_registro_consumo->granja                =$datos_granja_maestro->nombre;
                                    $nuevo_registro_consumo->jaula_id              =$datos_jaula_maestro->id;
                                    $nuevo_registro_consumo->jaula                 =$datos_jaula_maestro->nombre;
                                    $nuevo_registro_consumo->lote_id               =$datos_lote_maestro->id;
                                    $nuevo_registro_consumo->lote                  =$datos_lote_maestro->nombre;
                                    $nuevo_registro_consumo->proveedor_id          =$proveedor->id;
                                    $nuevo_registro_consumo->proveedor             =$proveedor->nombre;
                                    $nuevo_registro_consumo->pienso_id             =$pienso->id;
                                    $nuevo_registro_consumo->pienso                =$pienso->nombre;
                                    $nuevo_registro_consumo->codigo_pienso         =$pienso->codigo;
                                    $nuevo_registro_consumo->diametro_pienso       =$tamanio_pellet->diametro; 
                                    $nuevo_registro_consumo->cantidad_recomendada  =$produccion_simulada->cantidad_toma_modelo;
                                    $nuevo_registro_consumo->porcentaje_estrategia =100;
                                    $nuevo_registro_consumo->cantidad              =$produccion_simulada->cantidad_toma;
                                    $nuevo_registro_consumo->fecha                 =$fecha_inicial;
                                    $nuevo_registro_consumo->save();
                                }
                                else
                                {
                                    
                                    // Le corresponde dos tipos de pienso. Así que realizamos nuevamente la búsqueda
                                    //echo 'Le corresponde dos tipos de pienso. Así que realizamos nuevamente la búsqueda';
                                    $tamanio_pellets_rango = DB::select('Select tamanio_pellet_id 
                                                                           from detalle_rangos rd 
                                                                          where (pm_max >  ? and pm_transito <= ? and cabecera_rango_id = ?)
                                                                             or (pm_min >= ? and pm_transito >  ? and cabecera_rango_id = ?)
                                                                             order by pm_min limit 2', array($produccion_simulada->stock_avg_ini, 
                                                                                                     $produccion_simulada->stock_avg_ini,
                                                                                                     $cabecera_rango_id,
                                                                                                     $produccion_simulada->stock_avg_ini, 
                                                                                                     $produccion_simulada->stock_avg_ini,
                                                                                                     $cabecera_rango_id));
                                    $registros = 0;
                                    $cantidad_pienso = 0;
                                    $porcentaje_pienso = 0;
                                    foreach($tamanio_pellets_rango as $tamanio_pellets)
                                    {
                                       if ($registros == 1)
                                       {
                                          // Es el segundo registro
                                          //echo 'Es el segundo registro';
                                          $cantidad_pienso = $produccion_simulada->cantidad_toma - $cantidad_pienso;
                                          $porcentaje_pienso = 100 - $porcentaje_pienso;
                                          //echo '$cantidad_pienso' . $cantidad_pienso;
                                          //echo '$porcentaje_pienso' . $porcentaje_pienso;
                                          //echo 'Fin del segundo';
                                       }
                                       else
                                       {
                                          //echo 'Es el primero';
                                          //echo 'Aplicamos el 50% de la toma al pienso';                  
                                          $cantidad_pienso = ceil(($produccion_simulada->cantidad_toma * 0.5)/25)*25;
                                          $porcentaje_pienso = floor(($cantidad_pienso/$produccion_simulada->cantidad_toma)*100);
                                          $registros++;
                                           //echo '$cantidad_pienso' . $cantidad_pienso;
                                          //echo '$porcentaje_pienso' . $porcentaje_pienso;
                                          //echo ' Fin del primero';
                                           
                                       }
                                       $pienso = Pienso::where('diametro_pellet_id', '=', $tamanio_pellets->tamanio_pellet_id)
                                                       ->orderby('updated_at')
                                                       ->first();  
                                       $proveedor = Proveedorpienso::find($pienso->proveedor_id);
                                       $tamanio_pellet = Pellet::find($tamanio_pellets->tamanio_pellet_id);
                
                                       $nuevo_registro_consumo = new Consumo();           
                                       $nuevo_registro_consumo->granja_id             =$datos_granja_maestro->id;
                                       $nuevo_registro_consumo->granja                =$datos_granja_maestro->nombre;
                                       $nuevo_registro_consumo->jaula_id              =$datos_jaula_maestro->id;
                                       $nuevo_registro_consumo->jaula                 =$datos_jaula_maestro->nombre;
                                       $nuevo_registro_consumo->lote_id               =$datos_lote_maestro->id;
                                       $nuevo_registro_consumo->lote                  =$datos_lote_maestro->nombre;
                                       $nuevo_registro_consumo->proveedor_id          =$proveedor->id;
                                       $nuevo_registro_consumo->proveedor             =$proveedor->nombre;
                                       $nuevo_registro_consumo->pienso_id             =$pienso->id;
                                       $nuevo_registro_consumo->pienso                =$pienso->nombre;
                                       $nuevo_registro_consumo->codigo_pienso         =$pienso->codigo;
                                       $nuevo_registro_consumo->diametro_pienso       =$tamanio_pellet->diametro; 
                                       $nuevo_registro_consumo->cantidad_recomendada  =$produccion_simulada->cantidad_toma_modelo;
                                       $nuevo_registro_consumo->porcentaje_estrategia =$porcentaje_pienso;
                                       $nuevo_registro_consumo->cantidad              =$cantidad_pienso;
                                       $nuevo_registro_consumo->fecha                 =$fecha_inicial;
                                       $nuevo_registro_consumo->save();
                                    }
                                }    
            }
            $fecha_inicial->modify('+1 day');
            }// end while

        }

        public function ProcesarConsumosReales()
        {
          //$fecha= '2014-10-15';
          //Buscamos el último valor real introducido
          $fecha = DB::table('produccion_real')->max('date');
          $ConsumosReales = DB::select('Select fecha, proveedor, granja, diametro_pienso, 
                                               pienso_id, sum(cantidad) as consumo_real 
                                          from consumo_real
                                          where fecha = ?
                                      group by fecha, proveedor, granja, diametro_pienso, pienso_id
                                      order by fecha, proveedor, granja, diametro_pienso, pienso_id', array($fecha));

          foreach ($ConsumosReales as $consumoreal)
          {
            //Buscamos el id del almacén
            $almacen = Almacen::where('nombre', '=', $consumoreal->granja)->first();

            //Creamos un nuevo objeto movimientos_almacenes
            $nuevo_registro = new MovimientosAlmacen();
            $nuevo_registro->almacen_id      = $almacen->id;
            $nuevo_registro->tipo_movimiento = 'Salida';
            $nuevo_registro->descripcion     = 'Consumo';
            $nuevo_registro->pienso_id       = $consumoreal->pienso_id;
            $nuevo_registro->cantidad        = ($consumoreal->consumo_real) * -1 ;
            $nuevo_registro->fecha           = $consumoreal->fecha;
            $nuevo_registro->save();
          }
        }
	}


 ?>