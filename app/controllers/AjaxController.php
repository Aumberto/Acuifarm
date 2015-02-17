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
                                                         
                                                    group by pp.nombre, tp.diametro ) ,0) as pedidos_acumulados
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
                                                                                                            $fecha_inicial_semana_actual));



      //return json_encode($consulta);
      return Response::json($consulta);
    }
}

 ?>