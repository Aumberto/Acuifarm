<?php 

class AjaxController extends BaseController{
	//Clase creada para atender a todas las peticiones ajax de la web

	public function getJaulas(){

		$granja_id = Input::get('granja');
		$granja = Granja::find($granja_id);
		$jaulas = $granja->jaulas;
		return Response::json($jaulas);

	}

	public function getPellets(){
		$proveedor_id = Input::get('proveedor');
		$proveedor = Proveedorpienso::find($proveedor_id);
		$pellets = $proveedor->pellets;
		return Response::json($pellets);
	}

	public function getAlimentacion()
    {
		$jaula = Input::get('jaula');
        $lote  = Input::get('lote');
		$rango_id = Input::get('rango');
		$fechaIni = Input::get('fechaIni');
		$fechaFin = Input::get('fechaFin');

		$fecha_inicial = new DateTime($fechaIni);
        $fecha_final   = new DateTime($fechaFin);
        
        // Buscamos los datos maestros del lote y de la jaula
        $datos_jaula_maestro = Jaula::where('nombre', '=', $jaula)->first();
        $datos_lote_maestro  = Lote::where('nombre', '=', $lote)->first();

        $nuevo_registro_jaula_lote_rango = new JaulaLoteRango();
        $nuevo_registro_jaula_lote_rango->jaula_id          = $datos_jaula_maestro->id;
        $nuevo_registro_jaula_lote_rango->lote_id           = $datos_lote_maestro->id;
        $nuevo_registro_jaula_lote_rango->cabecera_rango_id = $rango_id;
        $nuevo_registro_jaula_lote_rango->fecha_inicio      = $fecha_inicial;
        $nuevo_registro_jaula_lote_rango->save();

        //Eliminamos los consumos anteriores
        $consumos = Consumo::where('jaula', '=', $jaula)
                           ->where('lote', '=', $lote)
                           ->where('fecha', '>=', $fecha_inicial)
                           ->delete();
                                        
        
                   
        
        // Buscamos todos los datos de producción simulada de esa jaula para el intervalo de tiempo determinado
        $datos_produccion_simulados = ProduccionSimuladas::where('unitname', '=' , $jaula)
                                                         ->where('groupid', '=', $lote)
                                                         ->where('date', '>=', $fechaIni)
                                                         ->orderBy('date', 'desc')
                                                         ->first();
        $fecha_final   = new DateTime($datos_produccion_simulados->date);
        $consumo = new ConsumoController();
        $consumo->ActualizarConsumoSimuladoIII($jaula, $fecha_inicial, $fecha_final, 0);
        
        $fecha_inicial = new DateTime($fechaIni);
        $fecha_final   = new DateTime($fechaFin);
        //var_dump($fecha_inicial);
        //var_dump($fecha_final);
        
        // Buscamos los nuevos datos de consumo
        $nuevos_consumos = Consumo::where('jaula', '=', $jaula)
                                  ->where('fecha', '>=', $fecha_inicial)
                                  ->where('fecha', '<=', $fecha_final)
                                  ->get();
        $temp = '';
        $tamanio_pellets = array();
        foreach($nuevos_consumos as $nuevo_consumo)
          {
            if ($temp != $nuevo_consumo->diametro_pienso)
            {
              array_push($tamanio_pellets, $nuevo_consumo->diametro_pienso);
              $temp = $nuevo_consumo->diametro_pienso;
            }
          }
        // Devolvemos un vector en formato json de tal manera {diametro_pienso: pienso1 + pienso2}
        //var_dump($tamanio_pellets);
        $resultado = array_unique($tamanio_pellets);
        //var_dump($resultado);
        $diametro_pienso = $resultado[0];
         
        if (count($resultado) > 1)
        {
          for ($i=1; $i<count($resultado); $i++)
          {
            $diametro_pienso = $diametro_pienso . ' + ' .  $resultado[$i];
          }
        }
            /*
         array_push($tamanio_pellets, 'Vaya mierda!!');
         $resultado = array_unique($tamanio_pellets);
         $diametro_pienso = $resultado[0];
         //$diametro_pienso = 'Vaya mierda!!!!'; */
       
      
		return json_encode($diametro_pienso);
		
		
	}

    public function UpdateAlimentacion()
    {
        //Leemos los parametros que recibimos
        $jaula      = Input::get('jaula');
        $lote       = Input::get('lote');
        $porcentaje = Input::get('porcentaje');
        $cantidad   = Input::get('cantidad');
        $fechaIni   = Input::get('fechaIni');
        $fechaFin   = Input::get('fechaFin');

        // Buscamos en la tabla de produccion simulada todos los registros correspondientes a dicha jaula en el intervalo de fecha concreta
        // 30/09/14 Modificación para que mantenga el porcentaje de la estrategia desde este momento en adelante.
        
        // $datos_simulados = ProduccionSimuladas::where('unitname', '=' , $jaula)
        //                                      ->where('date', '>=', $fechaIni)
        //                                      ->where('date', '<=', $fechaFin)
        //                                      ->orderBy('date')
        //                                      ->get();
        //

        // 12/12/14 Modificación para añadir la tabla jaula_lote_estrategia
        /*

        $datos_simulados = ProduccionSimuladas::where('unitname', '=' , $jaula)
                                              ->where('date', '>=', $fechaIni)
                                              ->orderBy('date')
                                              ->get();


        // Para cada uno de los registros anteriores, actualizamos su campo porcentaje y cantidad de la toma
        foreach ($datos_simulados as $dato_simulado)
        {
            //var_dump($dato_simulado->date);
            //echo ' Cantidad: ' . $cantidad;
            //echo ' Porcentaje: ' . $porcentaje;

            //echo ' Cantidad del modelo redondeada a sacos ' . number_format(ceil(($dato_simulado->cantidad_toma_modelo/25))*25, 0, '.', '');
            // Cantidad del modelo redondeada a sacos
            //$dato_simulado->($dato_simulado->cantidad_toma_modelo * ($porcentaje/100))
            $cantidad_redondeada = number_format(ceil((($dato_simulado->cantidad_toma_modelo * ($porcentaje/100))/25))*25, 0, '.', '');
            //echo ' Cantidad redondeada ' . $cantidad_redondeada;
            // Añadimos el porcentaje
            //$cantidad_redondeada = $cantidad_redondeada * ($porcentaje/100);
            $dato_simulado->porcentaje_toma = $porcentaje;
            //$dato_simulado->cantidad_toma   = number_format($cantidad_redondeada, 0, ',', ' ');
            $dato_simulado->cantidad_toma   = (int) $cantidad_redondeada;
            $dato_simulado->save();
        }
        
        */

        // Almacenamos en la tabla jaula_lote_estrategia el nuevo valor
        $jaula_objeto = Jaula::where('nombre', '=', $jaula)->first();
        $lote_objeto  = Lote::where('nombre', '=', $lote)->first();

        $registro_nuevo = new JaulaLoteEstrategia;
        $registro_nuevo->jaula_id = $jaula_objeto->id;
        $registro_nuevo->lote_id  = $lote_objeto->id;
        $registro_nuevo->porcentaje =$porcentaje;
        $registro_nuevo->fecha_inicio = $fechaIni;
        $registro_nuevo->save();
        //Ahora tenemos que actualizar los datos simulados, ya que se puede dar el caso de haber aumentado
        $datos_simulados = ProduccionSimuladas::where('unitname', '=' , $jaula)
                                              ->where('date', '=', $fechaIni)
                                              ->first();
        //dd($datos_simulados);
        
         
        $fecha_ini = new DateTime($fechaIni);
        $fecha_fin = new DateTime($fechaFin);
        $ps = new ProduccionController();
        $ps->actualizarSimulacionIntervalo($datos_simulados->site, 
                                           $datos_simulados->unitname, 
                                           $datos_simulados->groupid, 
                                           $datos_simulados->stock_count_ini, 
                                           $datos_simulados->stock_avg_ini, 
                                           $fecha_ini, 
                                           $fecha_fin); 
        
        $fecha_ini = new DateTime($fechaIni);
        $fecha_fin = new DateTime($fechaFin);
        $consumo = new ConsumoController();
        //var_dump($fecha_ini);
        //var_dump($fecha_fin); 
        $consumo->ActualizarConsumoSimulado($datos_simulados->unitname, $fecha_ini, $fecha_fin, 0);  
        

    }

    public function GraficaStatusContenedores()
    {
       //Leemos los parámetros que recibimos
        $post_proveedor      = Input::get('proveedor_id');
        //$post_proveedor = 2;
        //Declaramos las variables donde almacenaremos los datos de las gráficas
        $contenido_contenedores = array();
        $categorias = array();

        // Obtenemos la fecha de la última importación de datos reales
        $fecha_ultima_actualizacion = ProduccionReales::orderby('date', 'desc')->first();
        $fecha = new DateTime($fecha_ultima_actualizacion->date);

        // Obtenemos los datos del proveedor de pienso
        $proveedor_pienso = Proveedorpienso::find($post_proveedor);

        // Obtenemos todos los contenedores del proveedor cuyo estado sea igual a "Pendiente de descarga"
        $pedidos = Pedido::where('estado', '=', 'Pendiente de descarga')->where('proveedor_id', '=', $proveedor_pienso->id)->get();

        // Para cada uno de los pedidos, calculamos la cantidad de cada uno de los piensos.
        $i = 0; // Nos servirá para saber que estamos en la primera vuelta del bucle, que es la que llevará el nombre de los tipos de granos
        foreach($pedidos as $contenedor)
        {
           array_push($categorias, $contenedor->num_pedido);

           $consulta = DB::select('Select proveedores_pienso.nombre, tamanio_pellets.diametro as diametro, 
                                          ifnull((Select sum(pedidos_detalles.cantidad)
                                                    from tamanio_pellets tp, piensos, pedidos_detalles, pedidos
                                                   where piensos.diametro_pellet_id = tp.id
                                                     and pedidos.id = pedidos_detalles.pedido_id
                                                     and pedidos_detalles.pienso_id = piensos.id
                                                     and pedidos.id = ?
                                                     and piensos.proveedor_id = ?
                                                     and tp.id = tamanio_pellets.id
                                                group by pedidos.id,  pedidos.num_pedido, tamanio_pellets.diametro),0) as cantidad
                                     from proveedores_pienso, tamanio_pellets
                                    where proveedores_pienso.id = tamanio_pellets.proveedor_pienso_id
                                      and proveedores_pienso.id = ?
                                 order by tamanio_pellets.diametro', array($contenedor->id, $proveedor_pienso->id, $proveedor_pienso->id));
           $x=0;
           foreach($consulta as $resultado)
             {
                switch ($resultado->diametro) 
                {
                  case '1.50':
                     $color = '#4F81BD';
                  break;
                  case '1.90':
                     $color = '#953735';
                  break;
                  case '2.00':
                     $color = '#77933C';
                  break;
                  case '3.00':
                     $color = '#77933C';
                  break;
                  case '4.00':
                     $color = '#604A7B';
                  break;
                  case '4.50':
                     $color = '#604A7B';
                  break;
                  case '6.00':
                     $color = '#31859C';
                  break;
                  case '6.50':
                     $color = '#31859C';
                  break;
                  case '8.00':
                     $color = '#F79646';
                  break;
                  case '10.00':
                     $color = '#95B3D7';
                  break;
                  case '9.00':
                     $color = '#95B3D7';
                  break;
                  
                  default:
                    $color = '#000000';
                    break;
                }
                if ($i==0)
                 {
                   $data = array( (int)$resultado->cantidad);
                   $datos = array('name' => $resultado->diametro,
                                  'color' => $color, 
                                  'data' => $data); 

                   array_push($contenido_contenedores, $datos);
                 } 
                else 
                 {         
                   $contenido_contenedores[$x]['data'][] = (int)$resultado->cantidad;
                 }
                 $x++;
              }
          $i++;
          
        }
        //print_r($stock_teorico);
       $graph_data = array('categories'                 => $categorias, 
                           'contenido_contenedores'     => $contenido_contenedores,
                           'titulo'                     => 'Contenedores pendientes de descargar',
                           'subtitulo'                  => $proveedor_pienso->nombre);

        // devolvemos los datos en formato json
        return json_encode($graph_data);

    }

    public function GraficaConsumoSemanalGranjas()
    {
      //Leemos los parámetros que recibimos
      $post_proveedor      = Input::get('proveedor');
      $post_pellet        = Input::get('pellet');
      //echo $post_pellet;
      //echo $post_proveedor;
      //$post_proveedor = 2;
      //Declaramos las variables donde almacenaremos los datos de las gráficas
      $consumo_semanal = array();
      $categorias = array();

      // Obtenemos la fecha de la última importación de datos reales
      $fecha_ultima_actualizacion = ProduccionReales::orderby('date', 'desc')->first();
      $fecha = new DateTime($fecha_ultima_actualizacion->date);
      $fecha_real = new DateTime($fecha_ultima_actualizacion->date);
      // Obtenemos los datos del proveedor de pienso
      $proveedor_pienso = Proveedorpienso::where('nombre', '=', $post_proveedor)->first();

      // Obtenemos los datos del tamanño del pellets
      $pellet = Pellet::where('proveedor_pienso_id', '=', $proveedor_pienso->id)->where('diametro', '=', $post_pellet)->first();

      // Averiguamos la semana en la que nos encontramos
      $semana_actual = Semana::where('first_day', '<=', $fecha)->where('last_day', '>=', $fecha)->orderby('first_day', 'desc')->first();

      //$semana = $semana_actual->week;
      $first_day = new DateTime($semana_actual->first_day);
      $last_day = new DateTime($semana_actual->last_day);

      // Calculamos los consumos para las siguientes 5 semanas
      for ($i=0; $i<7; $i++)
      {
         $semana = Semana::where('first_day', '<=', $first_day)->where('last_day', '>=', $last_day)->orderby('first_day', 'desc')->first();
         $resultado_status = DB::select('select nombre, 
                                                ifnull((Select sum(cantidad)
                                                          from piensos p , tamanio_pellets tp, proveedores_pienso pp, consumos c
                                                         where c.proveedor_id = pp.id
                                                           and c.pienso_id = p.id
                                                           and tp.id = p.diametro_pellet_id
                                                           and tp.id = ?
                                                           and c.granja_id = granjas.id
                                                           and fecha   > ?
                                                           and fecha  >= ?
                                                           and fecha  <= ?
                                                      group by pp.nombre, tp.diametro),0) as consumo_simulado 
                                           from granjas 
                                       order by nombre', array($pellet->id, $fecha_real, $first_day, $last_day));
      $x = 0;
      foreach($resultado_status as $consumo)
       {
          switch ($consumo->nombre) {
                
                  case 'Procria':
                     $color = '#604A7B';
                  break;
                  
                  case 'Melenara':
                     $color = '#31859C';
                  break;
                  
                  case 'Pre-Engorde':
                     $color = '#95B3D7';
                  break;
                  
                  default:
                    $color = '#000000';
                    break;
                }
          if ($i==0)
          {
            //$stock = $resultado->stock_real + $resultado->pedidos_descargados - $resultado->consumo_simulado;
            $data = array((int)$consumo->consumo_simulado);
            $datos = array('name' => $consumo->nombre,
                           'color' => $color, 
                           'data' => $data); 

            array_push($consumo_semanal, $datos);
          } else {
            //$stock = $stock_teorico[$x]['data'][$i-2] - $resultado->consumo_simulado + $resultado->pedidos_descargados;
            $consumo_semanal[$x]['data'][] = (int)$consumo->consumo_simulado;
          }
        $x++;
       }
        array_push($categorias, $semana->week);
        $first_day->modify('+7 day');
        $last_day->modify('+7 day');
      }

      
      
      //print_r($consumo_semanal);
      //print_r($categorias);
      $graph_data = array('categories'        => $categorias, 
                           'consumo_semanal'  => $consumo_semanal,
                           'titulo'           => 'Consumo Semanal',
                           'subtitulo'        => 'Grano ' . $pellet->diametro . '(' . $proveedor_pienso->nombre . ')');

        // devolvemos los datos en formato json
        return json_encode($graph_data);

    }


    public function GraficaStatusStockFinal()
    {
        //Leemos los parámetros que recibimos
        $post_proveedor      = Input::get('proveedor_id');
        
        // Obtenemos la fecha de la última importación de datos reales
        $fecha_ultima_actualizacion = ProduccionReales::orderby('date', 'desc')->first();
        $fecha = new DateTime($fecha_ultima_actualizacion->date);

        // Obtenemos los datos del proveedor de pienso
        $proveedor_pienso = Proveedorpienso::find($post_proveedor);
        //var_dump($fecha);
        // Calculamos el stock final teórico durante los próximos 7 días a partir del último stock real.
        $stock_teorico = array();
        $categorias    = array();
        for ($i=1; $i<=7; $i++)
        {
          
           $resultado_status = DB::select('Select proveedores_pienso.nombre as nombre, tamanio_pellets.diametro as diametro, 
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
                                                         and fecha = DATE_ADD( ?, INTERVAL 1 DAY)
                                                    group by pp.nombre, tp.diametro),0) as consumo_simulado,
                                              ifnull((Select sum(cantidad)
                                                        from pedidos_detalles pd, pedidos p, piensos ps, tamanio_pellets tp, proveedores_pienso pp
                                                       where tamanio_pellets.id = tp.id
                                                         and pd.pedido_id = p.id  
                                                         and pd.pienso_id = ps.id
                                                         and tp.id = ps.diametro_pellet_id
                                                         and ps.proveedor_id = pp.id
                                                         and p.fecha_descarga = DATE_ADD( ?, INTERVAL 1 DAY)
                                                         and p.estado = ?
                                                    group by pp.nombre, tp.diametro ) ,0) as pedidos_descargados
                                        from proveedores_pienso, tamanio_pellets
                                      where proveedores_pienso.id = tamanio_pellets.proveedor_pienso_id
                                        and proveedores_pienso.id = ?
                                      order by proveedores_pienso.nombre, tamanio_pellets.diametro', array($fecha, $fecha, $fecha, 'Descargado', $proveedor_pienso->id));

           //var_dump($resultado_status);
           $x = 0;
           
           foreach($resultado_status as $resultado)
             {
                switch ($resultado->diametro) {
                  case '1.50':
                     $color = '#4F81BD';
                  break;
                  case '1.90':
                     $color = '#953735';
                  break;
                  case '2.00':
                     $color = '#77933C';
                  break;
                  case '3.00':
                     $color = '#77933C';
                  break;
                  case '4.00':
                     $color = '#604A7B';
                  break;
                  case '4.50':
                     $color = '#604A7B';
                  break;
                  case '6.00':
                     $color = '#31859C';
                  break;
                  case '6.50':
                     $color = '#31859C';
                  break;
                  case '8.00':
                     $color = '#F79646';
                  break;
                  case '10.00':
                     $color = '#95B3D7';
                  break;
                  case '9.00':
                     $color = '#95B3D7';
                  break;
                  
                  default:
                    $color = '#000000';
                    break;
                }


                if ($i==1)
                 {
                   $stock = $resultado->stock_real + $resultado->pedidos_descargados - $resultado->consumo_simulado;
                   $data = array($stock);
                   $datos = array('name' => $resultado->diametro,
                                  'color' => $color, 
                                  'data' => $data); 

                   array_push($stock_teorico, $datos);
                 } else {
                     
                     $stock = $stock_teorico[$x]['data'][$i-2] - $resultado->consumo_simulado + $resultado->pedidos_descargados;
                     $stock_teorico[$x]['data'][] = $stock;
                 }
               $x++;
             } 
          $fecha->modify('+1 day');
          array_push($categorias, $fecha->format('j'). "/" .$fecha->format('n'). "/" .$fecha->format('Y'));
          
        }
       //print_r($stock_teorico);
       $graph_data = array('categories'        => $categorias, 
                           'stock_teorico'     => $stock_teorico,
                           'titulo'            => 'Stock final teórico diario',
                           'subtitulo'         => $proveedor_pienso->nombre);

        // devolvemos los datos en formato json
        return json_encode($graph_data);
    }
    public function GraficaConsumoRealModeloPropuesta()
    {
        //Leemos los parámetros que recibimos
        $jaula      = Input::get('jaula');
        $fecha_ini  = Input::get('fechaini');
        $meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
        //Ejecutamos la consulta para obtener los datos. Sólo vamos a mostrar los últimos 15 días
        // Calculamos el final del intervalo
        $fecha_15dias_antes = new DateTime($fecha_ini);
        $fecha_15dias_antes->modify('-15 day');
        //var_dump($fecha_15dias_antes);
        //var_dump($fecha_ini);
        $resultado_status = DB::select('Select ps.date ,ps.groupid as lote, ifnull(pr.feeduse,0) as consumo_real, ps.cantidad_toma_modelo as consumo_modelo, 
                                               ps.cantidad_toma as consumo_propuesta, ps.porcentaje_toma as porcentaje_propuestaVsModelo,
                                               ifnull(((pr.feeduse/ps.cantidad_toma_modelo)*100),0) as porcentaje_realVsModelo
                                          from produccion_real pr right join produccion_simulado ps on pr.date     = ps.date
                                                                                                   and pr.groupid  = ps.groupid
                                                                                                   and pr.unitname = ps.unitname
                                         where ps.unitname = ?
                                           and ps.date     >= ?
                                           and ps.date     <= ? 
                                      order by ps.date', array($jaula, $fecha_15dias_antes, $fecha_ini));

        //Vamos completando cada uno de los vectores de datos
        $titulo               = 'Consumos de la ' . $jaula;
        $titulo_g2            = 'Crecimiento de la ' . $jaula;
        //$subtitulo            = 'Lote ' . $jaula;
        $categories        = array();
        $categories_g2        = array();
        $consumo_real      = array();
        $consumo_propuesta = array();
        $consumo_modelo = array();
        $porcentaje_propuestaVsModelo = array();
        $porcentaje_realVsModelo = array();
        $peso_medio = array();
        $SFR = array();
        $dia;
        $dia_g2;
        foreach ($resultado_status as $resultado)
         {
            $subtitulo            = 'Lote ' . $resultado->lote;
            $fecha = new DateTime($resultado->date);
            $dia = $fecha->format('j') . " de " . $meses[$fecha->format('n')-1];
            array_push($categories, $dia);
            array_push($consumo_real, (int)$resultado->consumo_real);
            array_push($consumo_propuesta, (int)$resultado->consumo_propuesta);
            array_push($consumo_modelo, (int)$resultado->consumo_modelo);
            array_push($porcentaje_propuestaVsModelo, (int)$resultado->porcentaje_propuestaVsModelo);
            array_push($porcentaje_realVsModelo, (int)$resultado->porcentaje_realVsModelo);
            //var_dump($resultado->consumo_real);
            //var_dump($resultado->consumo_propuesta);
            //var_dump($resultado->consumo_modelo);
            
            //echo $resultado->consumo_propuesta;
         }

         // Elaboramos la consulta para la gráfica del crecimiento
         $resultado_crecimiento = DB::select('Select date, unitname,  groupid, stock_count_ini, stock_avg_ini, stock_count_fin, stock_avg_fin, (feeduse/stock_bio_ini)*100 as SFR
                                                from produccion_real
                                               where unitname = ?
                                                 and date >= ?
                                                 and date <= ?
                                              union
                                              Select date, unitname,  groupid, stock_count_ini, stock_avg_ini, stock_count_fin, stock_avg_fin, (cantidad_toma/stock_bio_ini)*100 as SFR
                                                from produccion_simulado
                                               where unitname = ? 
                                                 and date not in (select distinct date from produccion_real)
                                                 and date >= ?
                                                 and date <= ?
                                              order by date, unitname', array($jaula, $fecha_15dias_antes, $fecha_ini, $jaula, $fecha_15dias_antes, $fecha_ini));
         //echo 'Resultado crecimiento: ' . $resultado_crecimiento;
         foreach ($resultado_crecimiento as $resultado_cr)
         {
            $subtitulo_g2            = 'Lote ' . $resultado_cr->groupid;
            $fecha_g2 = new DateTime($resultado_cr->date);
            $dia_g2 = $fecha_g2->format('j') . " de " . $meses[$fecha_g2->format('n')-1];
            array_push($categories_g2, $dia_g2);
            array_push($peso_medio, (float)$resultado_cr->stock_avg_ini);
            array_push($SFR, (float)$resultado_cr->SFR);
            
            //var_dump($resultado->consumo_real);
            //var_dump($resultado->consumo_propuesta);
            //var_dump($resultado->consumo_modelo);
            
            //echo $resultado->consumo_propuesta;
         }

        //$categories = array('Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');
        //$consumo_real = array(12, 25, 100, 58, 63, 30, 5, 40, 91, 10, 50, 36);
        //$click = array(6, 12, 40, 28, 31, 15, 2, 20, 45, 5, 25, 18);
        $graph_data = array('categories'        =>$categories, 
                            'real'              =>$consumo_real, 
                            'estrategia'        =>$consumo_propuesta, 
                            'modelo'            =>$consumo_modelo, 
                            'propuestaVsModelo' =>$porcentaje_propuestaVsModelo, 
                            'realVsModelo'      =>$porcentaje_realVsModelo, 
                            'titulo'            =>$titulo, 
                            'subtitulo'         =>$subtitulo,
                            'titulo2'           =>$titulo_g2,
                            'subtitulo2'        =>$subtitulo_g2,
                            'categories2'       =>$categories_g2,
                            'peso_medio'        =>$peso_medio,
                            'sfr'               =>$SFR);

        // devolvemos los datos en formato json
        return json_encode($graph_data);
    }

    public function MostrarConsumoJaula()
    {
        $jaula      = Input::get('jaula');
        $fecha_ini  = Input::get('fechaini');
        $fecha_fin  = Input::get('fechafin');
        //echo ' Jaula: ' . $jaula;
        //echo ' Fecha Ini: ' . $fecha_ini;
        //echo ' Fecha Fin: ' . $fecha_fin;
        // Recuperamos los datos de consumo de la jaula indicada para el intervalo
        $datos_consumos = Consumo::where('jaula', '=', $jaula)
                                 ->where('fecha', '>=', $fecha_ini)
                                 ->where('fecha', '<=', $fecha_fin)
                                 ->orderBy('fecha')
                                 ->get();
        //echo $datos_consumos;
        $salida = "<div class='datos_propuesta_estrategia'><table> <tr> <td>Día</td>  <td>Proveedor</td> <td>Pienso</td> <td>Cantidad Modelo</td> <td>Cantidad</td></tr>";
        //echo $salida;
        
        foreach($datos_consumos as $dato_consumo)
         {
            $salida = $salida . "<tr>";
            $salida = $salida . "<td>" . $dato_consumo->fecha . "</td>";
            $salida = $salida . "<td>" . $dato_consumo->proveedor . "</td>";
            $salida = $salida . "<td>" . $dato_consumo->pienso . "</td>";
            $salida = $salida . "<td>" . $dato_consumo->cantidad_recomendada . "</td>";
            $salida = $salida . "<td>" . $dato_consumo->cantidad . "</td>";
            $salida = $salida . "</tr>";
         }
         
         $salida = $salida . "</table></div>";
       
         echo $salida;
         //return $salida;
    }

    public function UpdateAyuno()
    {
      $id_ayuno = Input::get('id');
      $ayuno = Input::get('ayuno');
      $produccion_simulado = ProduccionSimuladas::find($id_ayuno);
      // Actualizamos el valor del ayuno
      $produccion_simulado->ayuno = $ayuno;
      $produccion_simulado->save();

      // Debemos actualizar los datos simulados a partir de esa fecha y durante mínimo 4 semanas
      $pc = new ProduccionController();
      $fecha_inicial = new DateTime($produccion_simulado->date);
      $fecha_final = new DateTime($produccion_simulado->date);
      $fecha_final->modify('+60 day');
      $pc->actualizarSimulacionIntervalo($produccion_simulado->site, 
                                                    $produccion_simulado->unitname, 
                                                    $produccion_simulado->groupid, 
                                                    $produccion_simulado->stock_count_ini, 
                                                    $produccion_simulado->stock_avg_ini, 
                                                    $fecha_inicial, 
                                                    $fecha_final);
      // Actualizamos los datos de consumos
      $consumo = new ConsumoController;
      $fecha_inicial = new DateTime($produccion_simulado->date);
      $fecha_final = new DateTime($produccion_simulado->date);
      $fecha_final->modify('+60 day');
      $consumo->ActualizarConsumoSimuladoIII($produccion_simulado->unitname, 
                                             $fecha_inicial, 
                                             $fecha_final,0);
    }

    public function ActualizarSimulacion() 
    {
        $pc = new ProduccionController();
        $pc->actualizarSimulacion();

        return 'Perfecto';
    }

    public function NuevaEstrategia()
    {
      $jaula = Input::get('jaula');
      $lote  = Input::get('lote');
      $fecha = Input::get('fecha');

      // Localizamos el id de la jaula
      $jaula_id = Jaula::where('nombre', '=', $jaula)->first();
      // Localizamos el id del lote
      $lote_id  = Lote::where('nombre', '=', $lote)->first();

      //Creamos un nuevo registro de la tabla Estrategia.
      $nuevo_registro = new ControlEstrategia;
    }

    public function PropuestaPedido()
    {
      // Obtenemos el valor del id de la semana
      $id_semana = Input::get('id_semana');
      
      // Obtenemos la fecha de la última importación de datos reales
      $fecha_ultima_actualizacion = ProduccionReales::orderby('date', 'desc')->first();
      $fecha_real = new DateTime($fecha_ultima_actualizacion->date);

      // Creamos un objeto Semana
      $semana = Semana::find($id_semana);

      // Obtenemos el día inicial y final de la semana seleccionada
      $fecha_inicial_semana_actual = new DateTime($semana->first_day);
      $fecha_final_semana_actual   = new DateTime($semana->last_day);

      // Obtenemos el día inicial y final de la semana siguiente a la seleccionada
      $fecha_inicial_semana_siguiente = new DateTime($semana->first_day);
      $fecha_final_semana_siguiente   = new DateTime($semana->last_day);

      $fecha_inicial_semana_siguiente->modify('+7 day');
      $fecha_final_semana_siguiente->modify('+7 day');

      // Realizamos la consulta
      $consulta = DB::select('Select proveedores_pienso.nombre, tamanio_pellets.diametro, 
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
                                                         and fecha >= ? and fecha <= ?
                                                    group by pp.nombre, tp.diametro),0) as consumo_simulado,
                                              ifnull((Select sum(cantidad)
                                                        from piensos p , tamanio_pellets tp, proveedores_pienso pp, consumos c
                                                       where c.proveedor_id = pp.id
                                                         and c.pienso_id = p.id
                                                         and tp.id = p.diametro_pellet_id
                                                         and tamanio_pellets.id = tp.id
                                                         and fecha > ?
                                                         and fecha < ?
                                                    group by pp.nombre, tp.diametro),0) as consumo_simulado_acumulado,
                                              ifnull((Select sum(cantidad)
                                                        from piensos p , tamanio_pellets tp, proveedores_pienso pp, consumos c
                                                       where c.proveedor_id = pp.id
                                                         and c.pienso_id = p.id
                                                         and tp.id = p.diametro_pellet_id
                                                         and tamanio_pellets.id = tp.id
                                                         and fecha > ?
                                                         and fecha >= ? 
                                                         and fecha <= ? 
                                                    group by pp.nombre, tp.diametro),0) as consumo_simulado_siguiente_semana,
                                              ifnull((Select sum(cantidad)
                                                        from pedidos_detalles pd, pedidos p, piensos ps, tamanio_pellets tp, proveedores_pienso pp
                                                       where tamanio_pellets.id = tp.id
                                                         and pd.pedido_id = p.id  
                                                         and pd.pienso_id = ps.id
                                                         and tp.id = ps.diametro_pellet_id
                                                         and ps.proveedor_id = pp.id
                                                         and p.fecha_descarga > ?
                                                         and p.fecha_descarga >= ? and p.fecha_descarga <= ?
                                                    group by pp.nombre, tp.diametro ) ,0) as pedidos, 
                                              ifnull((Select sum(cantidad)
                                                        from pedidos_detalles pd, pedidos p, piensos ps, tamanio_pellets tp, proveedores_pienso pp
                                                       where tamanio_pellets.id = tp.id
                                                         and pd.pedido_id = p.id  
                                                         and pd.pienso_id = ps.id
                                                         and tp.id = ps.diametro_pellet_id
                                                         and ps.proveedor_id = pp.id
                                                         and p.fecha_descarga > ?
                                                         and p.fecha_descarga < ?
                                                    group by pp.nombre, tp.diametro ) ,0) as pedidos_acumulados,
                                              ifnull((Select sum(cantidad)
                                                        from pedidos_detalles pd, pedidos p, piensos ps, tamanio_pellets tp, proveedores_pienso pp
                                                       where tamanio_pellets.id = tp.id
                                                         and pd.pedido_id = p.id  
                                                         and pd.pienso_id = ps.id
                                                         and tp.id = ps.diametro_pellet_id
                                                         and ps.proveedor_id = pp.id
                                                         and p.fecha_descarga <= ?
                                                         and p.estado <> ?
                                                    group by pp.nombre, tp.diametro ) ,0) as pedidosobsoletos
                                        from proveedores_pienso, tamanio_pellets
                                       where proveedores_pienso.id = tamanio_pellets.proveedor_pienso_id
                                       order by proveedores_pienso.nombre, tamanio_pellets.diametro', array($fecha_real, 
                                                                                                            $fecha_real, 
                                                                                                            $fecha_inicial_semana_actual, 
                                                                                                            $fecha_final_semana_actual, 
                                                                                                            $fecha_real, 
                                                                                                            $fecha_inicial_semana_actual,
                                                                                                            $fecha_inicial_semana_actual,
                                                                                                            $fecha_inicial_semana_siguiente,
                                                                                                            $fecha_final_semana_siguiente,
                                                                                                            $fecha_real,
                                                                                                            $fecha_inicial_semana_actual, 
                                                                                                            $fecha_final_semana_actual,
                                                                                                            
                                                                                                            $fecha_real,
                                                                                                            $fecha_inicial_semana_actual, $fecha_real, 'Descargado'));



      //return json_encode($consulta);
      return Response::json($consulta);
    }

    public function CambioNumeroTomas(){
      
      // Obtenemos el id del estadillo
      $id_estadillo = Input::get('idestadillo');
      
      // Obtenemos un objeto estadillo con dicho id
      $estadillo = Estadillo::find($id_estadillo);

      // Obtenemos un objeto producción simulado
      $produccion_simulada = ProduccionSimuladas::where('unitname', '=', $estadillo->jaula->nombre)->where('date', '=', $estadillo->fecha)->first();
      
      // Obtenemos un objeto de consumo simulado
      $consumo = Consumo::where('jaula_id', '=', $estadillo->jaula_id)->where('fecha', '=', $estadillo->fecha)->get();
      //echo $consumo;

      //echo $estadillo;
      // Obtenemos el nº de tomas
      $num_tomas = Input::get('numtomas');
      $porcentaje = 0;
      if ($num_tomas == 1) {
          $porcentaje = 100;
      }elseif ($num_tomas == 2){
          $cantidad_pienso = ceil(($produccion_simulada->cantidad_toma * 0.6)/25)*25;
          $porcentaje = floor(($cantidad_pienso/$produccion_simulada->cantidad_toma)*100);
            }
      $estadillo->num_tomas = $num_tomas;
      $estadillo->porcentaje_primera_toma = $porcentaje;
      $estadillo->save();
      $datos = array("porcentaje" => $porcentaje, "Kilos" => $produccion_simulada->cantidad_toma);
      //print_r(json_encode($data));
      return json_encode($datos);
    }

    public function CambioPorcentajeTomas(){
      
      // Obtenemos el id del estadillo
      $id_estadillo = Input::get('idestadillo');
      
      // Obtenemos un objeto estadillo con dicho id
      $estadillo = Estadillo::find($id_estadillo);

      // Obtenemos un objeto producción simulado
      $produccion_simulada = ProduccionSimuladas::where('unitname', '=', $estadillo->jaula->nombre)->where('date', '=', $estadillo->fecha)->first();
      
      // Obtenemos un objeto de consumo simulado
      $consumo = Consumo::where('jaula_id', '=', $estadillo->jaula_id)->where('fecha', '=', $estadillo->fecha)->get();
      //echo $consumo;

      //echo $estadillo;
      // Obtenemos el nº de tomas
      $porcentaje_primera_toma = Input::get('porcentaje');
      $porcentaje = 0;
      if ($estadillo->num_tomas == 1) {
          $porcentaje = 100;
      }elseif ($estadillo->num_tomas == 2){
          $cantidad_pienso = ceil(($produccion_simulada->cantidad_toma * ($porcentaje_primera_toma/100))/25)*25;
          $porcentaje = floor(($cantidad_pienso/$produccion_simulada->cantidad_toma)*100);
            }
      //$estadillo->num_tomas = $num_tomas;
      $estadillo->porcentaje_primera_toma = $porcentaje;
      $estadillo->save();
      $datos = array("porcentaje" => $porcentaje, "Kilos" => $produccion_simulada->cantidad_toma);
      //print_r(json_encode($data));
      return json_encode($datos);
    }

    public function GenerarExcel($fecha, $granja){
      

      
      
      // Generamos el objeto estadillo
      /*
      $datos_estadillos = DB::select('select j.nombre as jaula, g.nombre as granja, ifnull(ps.groupid,"-") as lote, ifnull(ps.stock_count_ini,0) as stock_count_ini, 
                                                 ifnull(ps.stock_avg_ini,0) as stock_avg_ini, ifnull(ps.stock_bio_ini,0) as stock_bio_ini, ifnull(ps.cantidad_toma,0) as cantidad_toma, 
                                                 ifnull(c.pienso,"-") as pienso, ifnull(c.diametro_pienso,"-") as diametro_pienso, ifnull(c.cantidad,0) as cantidad,
                                                 ifnull(e.num_tomas, 0) as num_tomas, ifnull(e.porcentaje_primera_toma, 0) as porcentaje_primera_toma, e.id as estadillo_id
                                            from jaulas j left join produccion_simulado ps on j.nombre = ps.unitname and ps.date =  ? 
                                                 left join consumos c on j.nombre = c.jaula and c.fecha =  ? 
                                                 left join estadillos e on j.id = e.jaula_id and e.fecha = ? , granjas g
                                           where g.nombre = ?
                                             and j.granja_id = g.id
                                        order by j.granja_id, j.nombre, c.diametro_pienso', array($fecha_estadillo, $fecha_estadillo, $fecha_estadillo, $granja));
      */
      // Creamos el nombre del archivo
      list($dia, $mes, $year)=explode("-", $fecha);
      $fecha_estadillo=$year."-".$mes."-".$dia;
      $filename = "Estadillo_" . $year.$mes.$dia. "_" . $granja;
       Excel::create($filename, function($excel) use($fecha, $granja)
  {
   $excel->sheet('Sheetname', function($sheet) use($fecha, $granja)
   {
    
     // Obtenemos la fecha
      list($dia, $mes, $year)=explode("-", $fecha);
      $fecha_estadillo=$year."-".$mes."-".$dia;

     //Averiguamos el día de la semana
      $fecha_ingles = new DateTime($fecha_estadillo);
      $dias_semana = array("Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado", "Domingo");
      $dia_semana = $dias_semana[$fecha_ingles->format('N')-1];

    //Obtenemos todas las jaulas de la granja
      $jaulas = DB::select('Select jaulas.nombre as jaula, jaulas.id as jaula_id 
                        from jaulas, granjas 
                       where jaulas.granja_id = granjas.id 
                         and granjas.nombre = ? 
                    order by jaulas.nombre', array($granja));

    $sheet->row(1, array('Fecha', $fecha, $dia_semana, ' ', ' ', 'T MAÑANA', ' ', ' ', ' ', ' ', ' ', ' ', 
                                                                            'T TARDE', ' ', ' ', ' ', ' ', ' ', ' ', 
                                                                            ' ', ' ', ' ', ' '));
 
    $sheet->row(2, array('Jaula', 'Nº de Peces', '', 'Biomasa', 'Nº sacos', 'Teo/Real', 'Respuesta', 'Kilos', 'Tipo', 'Nº Lote', 'Tiempo', 'Persona', 
                                                                            'Teo/Real', 'Respuesta', 'Kilos', 'Tipo', 'Nº Lote', 'Tiempo', 'Persona', 
                                                                            'Total', 'M. Sup', 'M. Fondo', 'Observaciones'));
    // Set black background
    $sheet->row(2, function($row) {
      // call cell manipulation methods
      $row->setFontWeight('bold');
      $row->setFontSize(11);
      $row->setFontFamily('Calibri');

    });
    $sheet->row(1, function($row) {
      // call cell manipulation methods
      $row->setFontFamily('Calibri');
      
    });
    //$row->setBackground('#000000');
    // Ancho de las columnas
    $sheet->setOrientation('landscape');
    $sheet->setPageMargin(0,25);
    $sheet->setFontFamily('Arial');
    $sheet->setHeight(1, 24);
    $sheet->setHeight(2, 16);
    $sheet->setWidth(array(
      'A'     =>  9.77852,
      'B'     =>  12.10852,
      'C'     =>  11.77852,
      'D'     =>  7.77852,
      'E'     =>  7.77852,
      'F'     =>  13.67852,
      'G'     =>  10.67852,
      'H'     =>  16.67852,
      'I'     =>  11.67852,
      'J'     =>  16.67852,
      'K'     =>  9.67852,
      'L'     =>  9.67852,
      'M'     =>  13.67852,
      'N'     =>  10.67852,
      'O'     =>  16.67852,
      'P'     =>  11.67852,
      'Q'     =>  16.67852,
      'R'     =>  9.67852,
      'S'     =>  9.67852,
      'T'     =>  12.67852,
      'U'     =>  8.77852,
      'V'     =>  8.77852,
      'W'     =>  66.278520
    ));



    $i=3;
    $estadillo_cantidad_total = 0;
      $estadillo_cantidad_total_primera_toma = 0;
      $estadillo_cantidad_total_segunda_toma = 0;
      $jaula_vacia = false;
      $varios_pellets = false;

    // Recorremos cada jaula y comprobamos si existen consumos.
    foreach ($jaulas as $jaula)
    {
      //Inicializamos las variables para completar los estadillos
      $estadillo_jaula = $jaula->jaula;
      $estadillo_lote = '';
      $estadillo_num_peces = '';
      $estadillo_peso_medio = '';
      $estadillo_biomasa = '';
      $estadillo_cantidad_toma_total = '';
      $estadillo_diametro_toma_total = '';
      $estadillo_cantidad_toma_primera = '';
      $estadillo_diametro_toma_primera = '';
      $estadillo_cantidad_toma_segunda = '';
      $estadillo_diametro_toma_segunda = '';
      $jaula_vacia = false;
      $varios_pellets = false;
      

      $datos_consumos = Consumo::where('jaula', '=', $jaula->jaula)->where('fecha', '=', $fecha_estadillo)->orderby('diametro_pienso')->get();
      if (count($datos_consumos)>0)
        //Existen consumos
       {
          //Localizamos los datos de producción
          $datos_produccion_simulados = ProduccionSimuladas::where('unitname', '=', $jaula->jaula)->where('date', '=', $fecha_estadillo)->first();
          
          //Obtenemos los datos del estadillo (num_tomas, %porcentaje primera toma)
          $datos_estadillo = Estadillo::where('jaula_id', '=', $jaula->jaula_id)->where('fecha', '=', $fecha_estadillo)->first();
          
          // Damos valores a las variables de los estadillos
          $estadillo_lote = $datos_produccion_simulados->groupid;
          $estadillo_num_peces = $datos_produccion_simulados->stock_count_ini;
          $estadillo_peso_medio = $datos_produccion_simulados->stock_avg_ini;
          $estadillo_biomasa = $datos_produccion_simulados->stock_bio_ini;
          $estadillo_cantidad_toma_total = $datos_produccion_simulados->cantidad_toma;

          //Comprobamos si existen dos tipos de pienso en la alimentación
          if (count($datos_consumos)==2)
           {
             $j=1;
             $varios_pellets = true;
             $diametro_pienso_1 ='';
             $diametro_pienso_2 ='';
             $cantidad_pienso_1 = 0;
             $cantidad_pienso_2 = 0;
             foreach($datos_consumos as $dato_consumo)
             {
               if ($j==1)
               {
                  $diametro_pienso_1 = $dato_consumo->diametro_pienso;
                  $cantidad_pienso_1 = $dato_consumo->cantidad;
               }
               else
               {
                  $diametro_pienso_2 = $dato_consumo->diametro_pienso;
                  $cantidad_pienso_2 = $dato_consumo->cantidad;
               }
               $j++;
             }
               // Averiguamos cuantas tomas tenemos
               
               if($datos_estadillo->num_tomas == 2)
                {

                   $cantidad_total_primera_toma = ceil(($datos_produccion_simulados->cantidad_toma * ($datos_estadillo->porcentaje_primera_toma / 100))/25)*25;
                   $cantidad_total_segunda_toma = $datos_produccion_simulados->cantidad_toma - $cantidad_total_primera_toma;

                   $cantidad_pienso_1_primera_toma = ceil(($cantidad_total_primera_toma * 0.5)/25)*25;
                   $cantidad_pienso_2_primera_toma = $cantidad_total_primera_toma - $cantidad_pienso_1_primera_toma;

                   $cantidad_pienso_1_segunda_toma = $cantidad_pienso_1 - $cantidad_pienso_1_primera_toma;
                   $cantidad_pienso_2_segunda_toma = $cantidad_pienso_2 - $cantidad_pienso_2_primera_toma; 

                   // Primera Toma
                   $estadillo_cantidad_toma_primera = $cantidad_pienso_1_primera_toma . ' + ' . $cantidad_pienso_2_primera_toma;
                   $estadillo_diametro_toma_primera = $diametro_pienso_1 . ' + ' . $diametro_pienso_2;

                   // Segunda Toma
                   $estadillo_cantidad_toma_segunda = $cantidad_pienso_1_segunda_toma . ' + ' . $cantidad_pienso_2_segunda_toma;
                   $estadillo_diametro_toma_segunda = $diametro_pienso_1 . ' + ' . $diametro_pienso_2;

                   // Totalizamos cada toma
                   $estadillo_cantidad_total_primera_toma = intval($estadillo_cantidad_total_primera_toma) + intval($cantidad_pienso_1_primera_toma) + intval($cantidad_pienso_2_primera_toma);
                   $estadillo_cantidad_total_segunda_toma = intval($estadillo_cantidad_total_segunda_toma) + intval($cantidad_pienso_1_segunda_toma) + intval($cantidad_pienso_2_segunda_toma);

                }
               else
                {
                   $cantidad_pienso_1_primera_toma = $cantidad_pienso_1;
                   $cantidad_pienso_2_primera_toma = $cantidad_pienso_2;

                   // Primera Toma
                   $estadillo_cantidad_toma_primera = $cantidad_pienso_1_primera_toma . ' + ' . $cantidad_pienso_2_primera_toma;
                   $estadillo_diametro_toma_primera = $diametro_pienso_1 . ' + ' . $diametro_pienso_2;

                   // Segunda Toma
                   $estadillo_cantidad_toma_segunda = '';
                   $estadillo_diametro_toma_segunda = '';

                   // Totalizamos cada toma
                   $estadillo_cantidad_total_primera_toma = intval($estadillo_cantidad_total_primera_toma) + intval($cantidad_pienso_1_primera_toma) + intval($cantidad_pienso_2_primera_toma);
                   
                   
                }

                $estadillo_diametro_toma_total = $diametro_pienso_1 . ' + ' . $diametro_pienso_2;
             
           }
          else
           {
             foreach($datos_consumos as $dato_consumo)
               {
                 $estadillo_cantidad_toma_total = $dato_consumo->cantidad;
                 $estadillo_diametro_toma_total = $dato_consumo->diametro_pienso;
               }
             if($datos_estadillo->num_tomas == 2)
              {
                 $cantidad_total_primera_toma = ceil(($datos_produccion_simulados->cantidad_toma * ($datos_estadillo->porcentaje_primera_toma / 100))/25)*25;
                 $cantidad_total_segunda_toma = $datos_produccion_simulados->cantidad_toma - $cantidad_total_primera_toma;
                 
                 // Primera Toma
                 $estadillo_cantidad_toma_primera = $cantidad_total_primera_toma;
                 $estadillo_diametro_toma_primera = $estadillo_diametro_toma_total;

                 // Segunda Toma
                 $estadillo_cantidad_toma_segunda = $cantidad_total_segunda_toma;
                 $estadillo_diametro_toma_segunda = $estadillo_diametro_toma_total;

                 // Totalizamos cada toma
                 $estadillo_cantidad_total_primera_toma = $estadillo_cantidad_total_primera_toma + intval($estadillo_cantidad_toma_primera);
                 $estadillo_cantidad_total_segunda_toma = $estadillo_cantidad_total_segunda_toma + intval($estadillo_cantidad_toma_segunda);

              }
             else
              {
                 // Primera Toma
                 $estadillo_cantidad_toma_primera = $datos_produccion_simulados->cantidad_toma;
                 $estadillo_diametro_toma_primera = $estadillo_diametro_toma_total;

                 // Segunda Toma
                 $estadillo_cantidad_toma_segunda = '';
                 $estadillo_diametro_toma_segunda = '';

                 // Totalizamos cada toma
                 $estadillo_cantidad_total_primera_toma = $estadillo_cantidad_total_primera_toma + intval($estadillo_cantidad_toma_primera);
                 

              }  
           }
           $jaula_vacia = false;
       } else {
        
          $jaula_vacia = true;
       }

       //Insertamos los datos en la hoja
       $sheet->appendRow(array(
                               $estadillo_jaula,
                               $estadillo_num_peces,
                               $estadillo_cantidad_toma_total,
                               $estadillo_biomasa,
                               $estadillo_cantidad_toma_total/25,
                               $estadillo_cantidad_toma_primera,
                               '',
                               '',
                               '',
                               '',
                               '',
                               '',
                               $estadillo_cantidad_toma_segunda
                               ));

       $sheet->appendRow(array(
                               $estadillo_lote,
                               $estadillo_peso_medio,
                               $estadillo_diametro_toma_total,
                               '',
                               '',
                               $estadillo_diametro_toma_primera,
                               '',
                               '',
                               '',
                               '',
                               '',
                               '',
                               $estadillo_diametro_toma_segunda
                               )); 
       if ($jaula_vacia)
       {
          // Set black background
          $sheet->row($i, function($row) {

           // call cell manipulation methods
           $row->setBackground('#D9D9D9');

          });

          // Set black background
          $sheet->row($i+1, function($row) {

           // call cell manipulation methods
           $row->setBackground('#D9D9D9');

          });
       }
       $string = "D" . $i . ":D" . ($i+1);
       $sheet->mergeCells($string);
       $string = "E" . $i . ":E" . ($i+1);
       $sheet->mergeCells($string);
       $string = "G" . $i . ":G" . ($i+1);
       $sheet->mergeCells($string);
       $string = "H" . $i . ":H" . ($i+1);
       $sheet->mergeCells($string);
       $string = "I" . $i . ":I" . ($i+1);
       $sheet->mergeCells($string);
       $string = "J" . $i . ":J" . ($i+1);
       $sheet->mergeCells($string);
       $string = "K" . $i . ":K" . ($i+1);
       $sheet->mergeCells($string);
       $string = "L" . $i . ":L" . ($i+1);
       $sheet->mergeCells($string);
       $string = "N" . $i . ":N" . ($i+1);
       $sheet->mergeCells($string);
       $string = "O" . $i . ":O" . ($i+1);
       $sheet->mergeCells($string);
       $string = "P" . $i . ":P" . ($i+1);
       $sheet->mergeCells($string);
       $string = "Q" . $i . ":Q" . ($i+1);
       $sheet->mergeCells($string);
       $string = "R" . $i . ":R" . ($i+1);
       $sheet->mergeCells($string);
       $string = "S" . $i . ":S" . ($i+1);
       $sheet->mergeCells($string);
       $string = "T" . $i . ":T" . ($i+1);
       $sheet->mergeCells($string);
       $string = "U" . $i . ":U" . ($i+1);
       $sheet->mergeCells($string);
       $string = "V" . $i . ":V" . ($i+1);
       $sheet->mergeCells($string);
       $string = "W" . $i . ":W" . ($i+1);
       $sheet->mergeCells($string);
       
       if ($jaula_vacia){
         $sheet->setHeight($i, 21);
         $sheet->setHeight($i+1, 21);
        } else  {
         $sheet->setHeight($i, 30);
         $sheet->setHeight($i+1, 30);
        } 
       

       // Bordes de A*
       $sheet->cell('A1', function($cells) {
   
         $cells->setFontSize(11);
         $cells->setFontWeight('bold');
       
       });

       $sheet->cell('B1', function($cells) {
   
         $cells->setFontSize(13);
       
       });


       $sheet->cell('C1', function($cells) {
   
         $cells->setFontSize(21);
         //$cells->setFontWeight('light');
       
       });

       $sheet->cell('F1', function($cells) {
   
         $cells->setFontWeight('bold');
       
       });

       $sheet->cell('M1', function($cells) {
   
         $cells->setFontWeight('bold');
       
       });
        
       $celda = 'A'. $i;
       $sheet->cell($celda, function($cells) {
   
       // Set all borders (top, right, bottom, left)
       $cells->setBorder('thin', 'thin', 'none', 'thin');
       //$cells->setFontWeight('bold');
       $cells->setFontSize(24);
       $cells->setBackground('#D9D9D9');
         });
       
       $celda = 'A'. ($i+1);
       $sheet->cell($celda, function($cells) {
  
       // Set all borders (top, right, bottom, left)
       $cells->setBorder('none', 'thin', 'thin', 'thin');

       });

       // Bordes de B*
       $celda = 'B'. $i;
       $sheet->cell($celda, function($cells) {
   
       // Set all borders (top, right, bottom, left)
       $cells->setBorder('thin', 'thin', 'none', 'thin');

         });
       
       $celda = 'B'. ($i+1);
       $sheet->cell($celda, function($cells) {
  
       // Set all borders (top, right, bottom, left)
       $cells->setBorder('none', 'thin', 'thin', 'thin');

       });

       $celda = 'C'. $i;
       $sheet->cell($celda, function($cells) use($varios_pellets) {
       if ($varios_pellets){
         $cells->setFontSize(24); 
       } else {
         $cells->setFontSize(24);
       }
       
         });

       $celda = 'C'. ($i+1);
       $sheet->cell($celda, function($cells) use($varios_pellets){
  
       // Set all borders (top, right, bottom, left)
       $cells->setFontWeight('bold');
       $cells->setBackground('#D9D9D9');
       if ($varios_pellets){
         $cells->setFontSize(13); 
       } else {
         $cells->setFontSize(24);
       }
       

       });

       $celda = 'F'. ($i);
       $sheet->cell($celda, function($cells) use($varios_pellets){
  
       // Set all borders (top, right, bottom, left)
       //$cells->setFontWeight('bold');
       //$cells->setBackground('#D9D9D9');
       if ($varios_pellets){
         $cells->setFontSize(16); 
       } else {
         $cells->setFontSize(24);
       }
       });
       $celda = 'F'. ($i+1);
       $sheet->cell($celda, function($cells) use($varios_pellets){
  
       // Set all borders (top, right, bottom, left)
       $cells->setFontWeight('bold');
       $cells->setBackground('#D9D9D9');
       if ($varios_pellets){
         $cells->setFontSize(13); 
       } else {
         $cells->setFontSize(24);
       }

       });

       $celda = 'M'. ($i);
       $sheet->cell($celda, function($cells) use($varios_pellets){
  
       // Set all borders (top, right, bottom, left)
       //$cells->setFontWeight('bold');
       //$cells->setBackground('#D9D9D9');
       if ($varios_pellets){
         $cells->setFontSize(16); 
       } else {
         $cells->setFontSize(24);
       }
       });

       $celda = 'M'. ($i+1);
       $sheet->cell($celda, function($cells) use($varios_pellets){
  
       // Set all borders (top, right, bottom, left)
       $cells->setFontWeight('bold');
       $cells->setBackground('#D9D9D9');
       if ($varios_pellets){
         $cells->setFontSize(13); 
       } else {
         $cells->setFontSize(24);
       }


       });
       
       $i= $i+2;
       $varios_pellets = false;

    }
   // Actualizamos los totales 
    // Totalizamos cada toma
    $estadillo_cantidad_total = $estadillo_cantidad_total_primera_toma + $estadillo_cantidad_total_segunda_toma;
                  
   $celdas = 'A1:W' . ($i-1);
   $sheet->cells($celdas, function($cells) {

    // manipulate the range of cells
        $cells->setAlignment('center');
        $cells->setValignment('center');

       }); 

   $celdas = 'C3:W' . ($i-1);
   $sheet->setBorder($celdas, 'thin');
   $sheet->appendRow(array(
                               '',
                               '',
                               $estadillo_cantidad_total . ' Kg.',
                               '',
                               '',
                               $estadillo_cantidad_total_primera_toma . ' Kg.',
                               '',
                               '',
                               '',
                               '',
                               '',
                               '',
                               $estadillo_cantidad_total_segunda_toma . ' Kg.'
                               )); 
   $sheet->setHeight($i, 24);
   $celdas = 'C' . ($i);
   $sheet->cells($celdas, function($cells) {

    // manipulate the range of cells
        $cells->setAlignment('center');
        $cells->setValignment('center');
        $cells->setBorder('thin', 'thin', 'thin', 'thin');
        $cells->setFontWeight('bold');
        $cells->setFontSize(16);

       }); 
   $celdas = 'F' . ($i);
   $sheet->cells($celdas, function($cells) {

    // manipulate the range of cells
        $cells->setAlignment('center');
        $cells->setValignment('center');
        $cells->setBorder('thin', 'thin', 'thin', 'thin');
        $cells->setFontWeight('bold');
        $cells->setFontSize(16);

       }); 
   $celdas = 'M' . ($i);
   $sheet->cells($celdas, function($cells) {

    // manipulate the range of cells
        $cells->setAlignment('center');
        $cells->setValignment('center');
        $cells->setBorder('thin', 'thin', 'thin', 'thin');
        $cells->setFontWeight('bold');
        $cells->setFontSize(16);

       }); 

   // Ahora totalizamos por cada tipo de grano.
   $tipos_granos = DB::select('select diametro_pienso, sum(cantidad) as cantidad
                                 from consumos 
                                where fecha = ? 
                                  and granja= ? 
                             group by diametro_pienso 
                             ORDER BY diametro_pienso', array($fecha_estadillo, $granja));
   $sheet->setHeight($i+2, 18);
   $celdas = 'B' . ($i+2);
   $sheet->setCellValue($celdas, 'Pellet');
   $sheet->cells($celdas, function($cells) {
       $cells->setAlignment('center');
       $cells->setValignment('center');
       $cells->setBorder('thin', 'none', 'thin', 'thin');
       $cells->setFontWeight('bold');
       //$cells->setFontWeight('bold');
   }); 

   $celdas = 'C' . ($i+2);
   $sheet->setCellValue($celdas, 'Kilos');
   $sheet->cells($celdas, function($cells) {
       $cells->setAlignment('center');
       $cells->setValignment('center');
       $cells->setBorder('thin', 'none', 'thin', 'none');
       $cells->setFontWeight('bold');
       //$cells->setFontWeight('bold');
   }); 

   $celdas = 'D' . ($i+2);
   $sheet->setCellValue($celdas, 'Sacos');
   $sheet->cells($celdas, function($cells) {
       $cells->setAlignment('center');
       $cells->setValignment('center');
       $cells->setBorder('thin', 'thin', 'thin', 'none');
       $cells->setFontWeight('bold');
       //$cells->setFontWeight('bold');
   }); 


   $j=1;
   $letra_celda_tipo_grano = 'B';
   $letra_celda_cantidad = 'C';
   $letra_celda_sacos = 'D';
   foreach($tipos_granos as $tipo_grano)
    {
       $sheet->setHeight($i+2+$j, 18);
       $celdas = $letra_celda_tipo_grano . ($i+2+$j);
       $sheet->setCellValue($celdas, $tipo_grano->diametro_pienso);
       $sheet->cells($celdas, function($cells) {

    // manipulate the range of cells
        $cells->setAlignment('right');
        $cells->setValignment('center');
        $cells->setBorder('thin', 'none', 'thin', 'thin');
        //$cells->setFontWeight('bold');
        $cells->setFontSize(16);

       }); 
       

       $celdas = $letra_celda_cantidad . ($i+2+$j);
       $sheet->setCellValue($celdas, $tipo_grano->cantidad);
       $sheet->cells($celdas, function($cells) {

    // manipulate the range of cells
        $cells->setAlignment('right');
        $cells->setValignment('center');
        $cells->setBorder('thin', 'none', 'thin', 'none');
        //$cells->setFontWeight('bold');
        $cells->setFontSize(16);

       }); 
       $celdas = $letra_celda_sacos . ($i+2+$j);
       $sheet->setCellValue($celdas, ($tipo_grano->cantidad / 25));
       $sheet->cells($celdas, function($cells) {

    // manipulate the range of cells
        $cells->setAlignment('right');
        $cells->setValignment('center');
        $cells->setBorder('thin', 'thin', 'thin', 'none');
        //$cells->setFontWeight('bold');
        $cells->setFontSize(16);

       }); 

       $j++;
    }

    // Pie de página

    // Combinamos Celdas
    $rango = 'F' . ($i+3) . ':G' . ($i+4);
    $sheet->mergeCells($rango);
    $sheet->cells($rango, function($cells) {
        $cells->setBorder('thin', 'thin', 'thin', 'thin');
        });
    $rango = 'F' . ($i+5) . ':G' . ($i+6);
    $sheet->mergeCells($rango);
    $sheet->cells($rango, function($cells) {
        $cells->setBorder('thin', 'thin', 'thin', 'thin');
        });
    $rango = 'F' . ($i+7) . ':G' . ($i+9);
    $sheet->mergeCells($rango);
    $sheet->cells($rango, function($cells) {
        $cells->setBorder('thin', 'thin', 'thin', 'thin');
        });
    $rango = 'H' . ($i+3) . ':J' . ($i+4);
    $sheet->mergeCells($rango);
    $sheet->cells($rango, function($cells) {
        $cells->setBorder('thin', 'thin', 'thin', 'thin');
        });
    $rango = 'H' . ($i+7) . ':J' . ($i+9);
    $sheet->mergeCells($rango);
    $sheet->cells($rango, function($cells) {
        $cells->setBorder('thin', 'thin', 'thin', 'thin');
        });
    $rango = 'P' . ($i+2) . ':R' . ($i+3);
    $sheet->mergeCells($rango);
    $sheet->cells($rango, function($cells) {
        //$cells->setBorder('thin', 'thin', 'thin', 'thin');
        });
    $rango = 'T' . ($i+3) . ':W' . ($i+3);
    $sheet->mergeCells($rango);
    $sheet->cells($rango, function($cells) {
        //$cells->setBorder('thin', 'thin', 'thin', 'thin');
        });
    $rango = 'T' . ($i+2) . ':W' . ($i+2);
    $sheet->mergeCells($rango);
    $sheet->cells($rango, function($cells) {
        //$cells->setBorder('thin', 'thin', 'thin', 'thin');
        });
    $rango = 'H' . ($i+5) . ':I' . ($i+5);
    $sheet->mergeCells($rango);
    $sheet->cells($rango, function($cells) {
        $cells->setBorder('thin', 'thin', 'thin', 'thin');
        });
    $rango = 'H' . ($i+6) . ':I' . ($i+6);
    $sheet->mergeCells($rango); 
    $sheet->cells($rango, function($cells) {
        $cells->setBorder('thin', 'thin', 'thin', 'thin');
        });

    $rango = 'Q' . ($i+4) . ':R' . ($i+4);
    $sheet->mergeCells($rango); 
    $sheet->cells($rango, function($cells) {
        $cells->setBorder('thin', 'thin', 'thin', 'thin');
        });
    $rango = 'Q' . ($i+5) . ':R' . ($i+5);
    $sheet->mergeCells($rango); 
    $sheet->cells($rango, function($cells) {
        $cells->setBorder('thin', 'thin', 'thin', 'thin');
        });
    $rango = 'Q' . ($i+6) . ':R' . ($i+6);
    $sheet->mergeCells($rango); 
    $sheet->cells($rango, function($cells) {
        $cells->setBorder('thin', 'thin', 'thin', 'thin');
        });
    $rango = 'Q' . ($i+7) . ':R' . ($i+7);
    $sheet->mergeCells($rango); 
    $sheet->cells($rango, function($cells) {
        $cells->setBorder('thin', 'thin', 'thin', 'thin');
        });

    $rango = 'L' . ($i+4) . ':N' . ($i+4);
    $sheet->mergeCells($rango); 
    $sheet->cells($rango, function($cells) {
        $cells->setBorder('thin', 'thin', 'thin', 'thin');
        });


    
    

    // Escribimos Celdas
    $celdas = 'F' . ($i+3);
    $sheet->setCellValue($celdas, 'Tipo embarcación');
    $sheet->cells($celdas, function($cells) {
        $cells->setAlignment('center');
        $cells->setValignment('center');
        $cells->setBorder('thin', 'thin', 'thin', 'thin');
        //$cells->setFontWeight('bold');
       }); 

    $celdas = 'F' . ($i+5);
    $sheet->setCellValue($celdas, 'Turno');
    $sheet->cells($celdas, function($cells) {
        $cells->setAlignment('center');
        $cells->setValignment('center');
        $cells->setBorder('thin', 'thin', 'thin', 'thin');
        //$cells->setFontWeight('bold');
       }); 
    $celdas = 'F' . ($i+7);
    $sheet->setCellValue($celdas, 'Firma del responsable');
    $sheet->cells($celdas, function($cells) {
        $cells->setAlignment('center');
        $cells->setValignment('center');
        $cells->setBorder('thin', 'thin', 'thin', 'thin');
        //$cells->setFontWeight('bold');
       }); 
    
    $celdas = 'H' . ($i+5);
    $sheet->setCellValue($celdas, 'Mañana');
    $sheet->cells($celdas, function($cells) {
        $cells->setAlignment('center');
        $cells->setValignment('center');
        $cells->setBorder('thin', 'thin', 'thin', 'thin');
        //$cells->setFontWeight('bold');
       }); 

    $celdas = 'J' . ($i+5);
    $sheet->cells($celdas, function($cells) {
    $cells->setAlignment('center');
    $cells->setValignment('center');
    $cells->setBorder('thin', 'thin', 'thin', 'thin');
        //$cells->setFontWeight('bold');
    }); 

    $celdas = 'H' . ($i+6);
    $sheet->setCellValue($celdas, 'Tarde');
    $sheet->cells($celdas, function($cells) {
        $cells->setAlignment('center');
        $cells->setValignment('center');
        $cells->setBorder('thin', 'thin', 'thin', 'thin');
        //$cells->setFontWeight('bold');
       }); 

    $celdas = 'J' . ($i+6);
    $sheet->cells($celdas, function($cells) {
    $cells->setAlignment('center');
    $cells->setValignment('center');
    $cells->setBorder('thin', 'thin', 'thin', 'thin');
        //$cells->setFontWeight('bold');
    }); 

    $celdas = 'P' . ($i+2);
    $sheet->setCellValue($celdas, 'En la casilla "Respuesta" se debe anotar la numeración acorde al comportamiento');
    $sheet->cells($celdas, function($cells) {
        $cells->setAlignment('left');
        $cells->setValignment('distributed');
        //$cells->setBorder('thin', 'thin', 'thin', 'thin');
        //$cells->setFontWeight('bold');
       }); 

    $celdas = 'T' . ($i+2);
    $sheet->setCellValue($celdas, 'La dirección se marca como N,NE, SE, S,...');
    $sheet->cells($celdas, function($cells) {
        $cells->setAlignment('left');
        $cells->setValignment('center');
        //$cells->setBorder('thin', 'thin', 'thin', 'thin');
        //$cells->setFontWeight('bold');
       }); 

    $celdas = 'T' . ($i+3);
    $sheet->setCellValue($celdas, 'La fuerza se marca como nula, muy baja, baja, alta, muy alta.');
    $sheet->cells($celdas, function($cells) {
        $cells->setAlignment('left');
        $cells->setValignment('center');
        //$cells->setBorder('thin', 'thin', 'thin', 'thin');
        //$cells->setFontWeight('bold');
       }); 

    $celdas = 'P' . ($i+4);
    $sheet->setCellValue($celdas, '1');
    $sheet->cells($celdas, function($cells) {
        $cells->setAlignment('center');
        $cells->setValignment('center');
        $cells->setBorder('thin', 'thin', 'thin', 'thin');
        $cells->setFontWeight('bold');
       }); 

    $celdas = 'P' . ($i+5);
    $sheet->setCellValue($celdas, '2');
    $sheet->cells($celdas, function($cells) {
        $cells->setAlignment('center');
        $cells->setValignment('center');
        $cells->setBorder('thin', 'thin', 'thin', 'thin');
        $cells->setFontWeight('bold');
       }); 

    $celdas = 'P' . ($i+6);
    $sheet->setCellValue($celdas, '3');
    $sheet->cells($celdas, function($cells) {
        $cells->setAlignment('center');
        $cells->setValignment('center');
        $cells->setBorder('thin', 'thin', 'thin', 'thin');
        $cells->setFontWeight('bold');
       }); 
    $celdas = 'P' . ($i+7);
    $sheet->setCellValue($celdas, '4');
    $sheet->cells($celdas, function($cells) {
        $cells->setAlignment('center');
        $cells->setValignment('center');
        $cells->setBorder('thin', 'thin', 'thin', 'thin');
        $cells->setFontWeight('bold');
       }); 
    $celdas = 'Q' . ($i+4);
    $sheet->setCellValue($celdas, 'Chapoteo');
    $sheet->cells($celdas, function($cells) {
        $cells->setAlignment('left');
        $cells->setValignment('center');
        $cells->setBorder('thin', 'thin', 'thin', 'thin');
        //$cells->setFontWeight('bold');
       }); 

    $celdas = 'Q' . ($i+5);
    $sheet->setCellValue($celdas, 'Superficie (0-2 m.)');
    $sheet->cells($celdas, function($cells) {
        $cells->setAlignment('left');
        $cells->setValignment('center');
        $cells->setBorder('thin', 'thin', 'thin', 'thin');
        //$cells->setFontWeight('bold');
       }); 

    $celdas = 'Q' . ($i+6);
    $sheet->setCellValue($celdas, 'Por debajo de 2m.');
    $sheet->cells($celdas, function($cells) {
        $cells->setAlignment('left');
        $cells->setValignment('center');
        $cells->setBorder('thin', 'thin', 'thin', 'thin');
        //$cells->setFontWeight('bold');
       }); 
    $celdas = 'Q' . ($i+7);
    $sheet->setCellValue($celdas, 'No se ve el pescado');
    $sheet->cells($celdas, function($cells) {
        $cells->setAlignment('left');
        $cells->setValignment('center');
        $cells->setBorder('thin', 'thin', 'thin', 'thin');
        //$cells->setFontWeight('bold');
       }); 

    $celdas = 'T' . ($i+4);
    $sheet->setCellValue($celdas, 'PARÁMETRO');
    $sheet->cells($celdas, function($cells) {
        $cells->setAlignment('left');
        $cells->setValignment('center');
        $cells->setBorder('thin', 'thin', 'thin', 'thin');
        $cells->setFontWeight('bold');
       });

    $celdas = 'U' . ($i+4);
    $sheet->setCellValue($celdas, 'DIRECCIÓN');
    $sheet->cells($celdas, function($cells) {
        $cells->setAlignment('left');
        $cells->setValignment('center');
        $cells->setBorder('thin', 'thin', 'thin', 'thin');
        $cells->setFontWeight('bold');
       });

    $celdas = 'V' . ($i+4);
    $sheet->setCellValue($celdas, 'FUERZA');
    $sheet->cells($celdas, function($cells) {
        $cells->setAlignment('left');
        $cells->setValignment('center');
        $cells->setBorder('thin', 'thin', 'thin', 'thin');
        $cells->setFontWeight('bold');
       });


    $celdas = 'T' . ($i+5);
    $sheet->setCellValue($celdas, 'Oleaje');
    $sheet->cells($celdas, function($cells) {
        $cells->setAlignment('left');
        $cells->setValignment('center');
        $cells->setBorder('thin', 'thin', 'thin', 'thin');
        $cells->setFontWeight('bold');
       });

    $celdas = 'T' . ($i+6);
    $sheet->setCellValue($celdas, 'Viento');
    $sheet->cells($celdas, function($cells) {
        $cells->setAlignment('left');
        $cells->setValignment('center');
        $cells->setBorder('thin', 'thin', 'thin', 'thin');
        $cells->setFontWeight('bold');
       });

    $celdas = 'U' . ($i+5) ;
    $sheet->cells($celdas, function($cells) {
        $cells->setBorder('thin', 'thin', 'thin', 'thin');
     
       });
    $celdas = 'V' . ($i+5) ;
    $sheet->cells($celdas, function($cells) {
        $cells->setBorder('thin', 'thin', 'thin', 'thin');
     
       });
    $celdas = 'U' . ($i+6) ;
    $sheet->cells($celdas, function($cells) {
        $cells->setBorder('thin', 'thin', 'thin', 'thin');
     
       });
    $celdas = 'V' . ($i+6) ;
    $sheet->cells($celdas, function($cells) {
        $cells->setBorder('thin', 'thin', 'thin', 'thin');
     
       });

    $celdas = 'L' . ($i+4);
    $sheet->setCellValue($celdas, 'Temperatura agua');
    $sheet->cells($celdas, function($cells) {
        $cells->setAlignment('center');
        $cells->setValignment('center');
        $cells->setBorder('thin', 'thin', 'thin', 'thin');
        $cells->setFontWeight('bold');
       });

    $celdas = 'L' . ($i+5);
    $sheet->setCellValue($celdas, '1 m.');
    $sheet->cells($celdas, function($cells) {
        $cells->setAlignment('left');
        $cells->setValignment('center');
        $cells->setBorder('thin', 'thin', 'thin', 'thin');
        
       });
    $celdas = 'M' . ($i+5) ;
    $sheet->cells($celdas, function($cells) {
        $cells->setBorder('thin', 'thin', 'thin', 'thin');
     
       });

    $celdas = 'N' . ($i+5);
    $sheet->setCellValue($celdas, '10 m.');
    $sheet->cells($celdas, function($cells) {
        $cells->setAlignment('right');
        $cells->setValignment('center');
        $cells->setBorder('thin', 'thin', 'thin', 'thin');
        
       });

    $celdas = 'L' . ($i+6) ;
    $sheet->cells($celdas, function($cells) {
        $cells->setBorder('thin', 'thin', 'thin', 'thin');
     
       });
    $celdas = 'M' . ($i+6) ;
    $sheet->cells($celdas, function($cells) {
        $cells->setBorder('thin', 'thin', 'thin', 'thin');
     
       });
    $celdas = 'N' . ($i+6) ;
    $sheet->cells($celdas, function($cells) {
        $cells->setBorder('thin', 'thin', 'thin', 'thin');
     
       });
    $celdas = 'L' . ($i+7) ;
    $sheet->cells($celdas, function($cells) {
        $cells->setBorder('thin', 'thin', 'thin', 'thin');
     
       });
    $celdas = 'M' . ($i+7) ;
    $sheet->cells($celdas, function($cells) {
        $cells->setBorder('thin', 'thin', 'thin', 'thin');
     
       });
    $celdas = 'N' . ($i+7) ;
    $sheet->cells($celdas, function($cells) {
        $cells->setBorder('thin', 'thin', 'thin', 'thin');
     
       });


    
   });
  })->download('xlsx');
       //return Redirect::to('estadillos');
    }
}

 ?>