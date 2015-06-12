<?php 

class PropuestaAlimentacionController extends BaseController{

  public function getIndex(){

  	 $propuestas = CabeceraPropuestaAlimentacion::orderBy('fecha_ini', 'desc')->orderBy('granja')->get();
  	 return View::make('propuesta.propuesta_list')->with('propuestas', $propuestas);
  }

  public function getAdd(){
  	$granjas = Granja::all();
  	return View::make('propuesta.nueva')->with('granjas', $granjas);
  }

  public function getNew(){
    
    $pc = new ProduccionController();

    // Recuperamos los datos del formulario.
    $fecha=Input::get('fecha_ini');
    list($dia, $mes, $year)=explode("-", $fecha);
    $fecha_ini=$year."-".$mes."-".$dia;
    

    $fecha=Input::get('fecha_fin');
    list($dia, $mes, $year)=explode("-", $fecha);
    $fecha_fin=$year."-".$mes."-".$dia;

    $granja_id = Input::get('granja_id');
    $granja = Granja::find($granja_id);

    //echo $fecha_ini . ' ' . $fecha_fin;

  	// Buscamos para la granja que vamos a crear, los datos de producción más actuales.
  	$ultima_fecha_produccion_real = DB::table('produccion_real')->where('date', '<=' , $fecha_ini)
                                                                ->where('site', '=', $granja->nombre)
                                                                ->orderBy('date', 'desc')
  	                                                            ->first();
  	//Echo 'Último dato de produccion' . $ultima_fecha_produccion_real->date;
    //Calculamos la diferencia de dias entre ambas fechas
    $datetime1 = new DateTime($fecha_ini);
    $datetime2 = new DateTime($ultima_fecha_produccion_real->date);
  	$interval = $datetime1->diff($datetime2);
    

    // Nueva parte para calcular la diferencia de dias
    $diferencia = strtotime($fecha_ini) - strtotime($ultima_fecha_produccion_real->date);
    $diferencia = floor($diferencia/86400) - 1;
    //echo ' ' . $diferencia;

    //echo ' Diferencia de días' . $interval->format('%R%a días');
    
    // Elaboramos la cabecera de la columna status
    if ($diferencia == 0)
    {
    	//Recuperamos los datos de la 
      $fecha_cabecera = new DateTime($ultima_fecha_produccion_real->date);
    	$mensaje_cabecera_status = 'Status ' . $granja->nombre . ' ' . date_format($fecha_cabecera, 'd/m/y');

    }
    else
    {
      $fecha_cabecera = new DateTime($fecha_ini);
    	$mensaje_cabecera_status = 'Status ' . $granja->nombre . ' ' . date_format($fecha_cabecera, 'd/m/y') . '(' . $diferencia . ' dias simulados)';
    }

    //Elaboramos la cabecera de la columna de la propuesta
    $mensaje_cabecera_propuesta = 'Propuesta de alimentación para ' . $granja->nombre;
    $propuesta_ini = new DateTime($fecha_ini);
    $propuesta_fin = new DateTime($fecha_fin);
    if ($propuesta_ini->format('W') == $propuesta_fin->format('W') )
    {
         $mensaje_cabecera_propuesta = $mensaje_cabecera_propuesta . '. Semana nº ' . $propuesta_ini->format('W');
    }
    else
    {
         $mensaje_cabecera_propuesta = $mensaje_cabecera_propuesta . '. Semanas ' . $propuesta_ini->format('W') . ' y ' . $propuesta_fin->format('W');
    }
    
    $propuesta_cabecera = new PropuestaAlimentacionCabecera;
    $propuesta_cabecera->granja      = $granja->nombre;
    $propuesta_cabecera->granja_id   = $granja_id;
    $propuesta_cabecera->descripcion = $mensaje_cabecera_propuesta;
    $propuesta_cabecera->fecha_ini   = $propuesta_ini;
    $propuesta_cabecera->fecha_fin   = $propuesta_fin;
    $propuesta_cabecera->save();


    $titulo_cabecera_propuesta  = $mensaje_cabecera_propuesta . ' Desde el ' . date_format($propuesta_ini, 'd/m/y')  . ' al ' . date_format($propuesta_fin, 'd/m/y') ;


    $resultado_status = DB::select('select jaulas.nombre, ps.groupid, ps.stock_count_ini, ps.stock_avg_ini, ps.stock_bio_ini,
                                        ps.cantidad_toma_modelo, ps.sfr
                                   from produccion_simulado ps right join jaulas on ps.unitname = jaulas.nombre
                                        and ps.date = ?
                                  where jaulas.granja_id = ?', array($fecha_ini, $granja_id));

    $total_resultado_status = DB::select('Select sum(stock_count_ini) as total_stock_ini, sum(stock_bio_ini) as total_bio_ini, 
                                                   (sum(stock_bio_ini)/sum(stock_count_ini))*1000 as total_avg_ini, sum(cantidad_toma) as cantidad_toma
                                              from produccion_simulado
                                             where date = ?
                                               and site = ?', array($fecha_ini, $granja->nombre));

    //print_r($resultado_status);
    // Eliminamos todos los consumos que hay desde fecha inicial hasta fecha final
    
    $consumos_simulados = Consumo::where('granja_id', '=', $granja_id)
                                 ->where('fecha', '>=', $fecha_ini)
                                 ->where('fecha', '<=', $fecha_fin)
                                 ->get();
    
   
        //Mostramos el resultado de la propuesta
    $resultado_propuesta = DB::select('Select jaulas.nombre, vista_consumos.lote, vista_consumos.proveedor, vista_consumos.diametro_pienso, 
                                              vista_consumos.porcentaje_toma, vista_consumos.cantidad_toma_modelo as cantidad_recomendada, vista_consumos.cantidad_toma as cantidad, 
                                              (select jlr.cabecera_rango_id 
                                                 from jaula_lote_rango jlr, jaulas j, lotes l
                                                where jlr.jaula_id = j.id
                                                  and jlr.lote_id = l.id
                                                  and jlr.fecha_inicio <= ?
                                                  and j.nombre = jaulas.nombre
                                                  and l.nombre = vista_consumos.lote 
                                                  order by j.nombre, l.nombre, jlr.created_at desc
                                                  limit 1) as cabecera_rango_id
                                         from (Select c.jaula, c.lote, c.proveedor, c.diametro_pienso, min(ps.porcentaje_toma) as porcentaje_toma, 
                                                      min(ps.cantidad_toma_modelo) as cantidad_toma_modelo, min(ps.cantidad_toma) as cantidad_toma
                                                 from consumos c , produccion_simulado ps  
                                                where c.fecha >= ? 
                                                  and c.fecha <= ?
                                                  and c.jaula = ps.unitname 
                                                  and c.fecha = ps.date
                                                  and c.granja = ps.site
                                                  and c.granja_id = ?
                                             group by c.jaula, c.lote, c.proveedor, c.diametro_pienso) vista_consumos right join jaulas on vista_consumos.jaula = jaulas.nombre
                                        where jaulas.granja_id = ?
                                        order by jaulas.nombre, vista_consumos.diametro_pienso ', array($propuesta_cabecera->fecha_ini,
                                                                                                        $propuesta_cabecera->fecha_ini, 
                                                                                                        $propuesta_cabecera->fecha_fin, 
                                                                                                        $propuesta_cabecera->granja_id, 
                                                                                                        $propuesta_cabecera->granja_id));

    //Formateamos la salida
    $salida_jaula = '';
    $salida_lote = '';
    $salida_pienso = '';
    $salida_cantidad_recomendada = 0;
    $salida_cantidad = 0;
    $salida_porcentaje_toma = 0;
    $salida_proveedor = '';
    $resultados_propuesta = array();
    $primera_linea = TRUE;
    foreach ($resultado_propuesta as $rs)
     {  //echo 'Jaula: ' . $rs->nombre;
        if (($salida_jaula == $rs->nombre))
        {
           $salida_pienso = $salida_pienso . ' + ' . $rs->diametro_pienso;
        }
        else
        {
          if ($primera_linea)
          { 
            $salida_jaula = $rs->nombre;
            $salida_lote =  $rs->lote;
            $salida_pienso = $rs->diametro_pienso;
            $salida_proveedor = $rs->proveedor;
            $salida_rangos = $rs->cabecera_rango_id;
            $salida_cantidad_recomendada = $rs->cantidad_recomendada;
            $salida_porcentaje_toma = $rs->porcentaje_toma;
            $salida_cantidad = $rs->cantidad;
            $primera_linea = FALSE;
          }
          else
           { $consumo_linea = [ 
                                         'nombre'               => $salida_jaula,
                                         'lote'                 => $salida_lote,
                                         'proveedor'            => $salida_proveedor,
                                         'rango'                => $salida_rangos,
                                         'diametro_pienso'      => $salida_pienso,
                                         'cantidad_recomendada' => $salida_cantidad_recomendada,
                                         'porcentaje_toma'      => $salida_porcentaje_toma,
                                         'cantidad'             => $salida_cantidad,
                                       ];
            array_push($resultados_propuesta, $consumo_linea);
            $salida_jaula = $rs->nombre;
            $salida_lote =  $rs->lote;
            $salida_pienso = $rs->diametro_pienso;
            $salida_cantidad_recomendada = $rs->cantidad_recomendada;
            $salida_porcentaje_toma = $rs->porcentaje_toma;
            $salida_cantidad = $rs->cantidad;
            $salida_proveedor = $rs->proveedor;
            $salida_rangos = $rs->cabecera_rango_id;
           }
        }
     }
     $consumo_linea = [ 
                                         'nombre'               => $salida_jaula,
                                         'lote'                 => $salida_lote,
                                         'proveedor'            => $salida_proveedor,
                                         'rango'                => $salida_rangos,
                                         'diametro_pienso'      => $salida_pienso,
                                         'cantidad_recomendada' => $salida_cantidad_recomendada,
                                         'porcentaje_toma'      => $salida_porcentaje_toma,
                                         'cantidad'             => $salida_cantidad,
                                       ];
            array_push($resultados_propuesta, $consumo_linea);
    //var_dump($resultados_propuesta);
    //var_dump($resultado_propuesta);
    //Recuperamos todos los proveedores
    $proveedores = Proveedorpienso::orderBy('nombre')->get();
    $rangos      = CabeceraRangos::orderBy('nombre')->get();

    return View::make('propuesta.ver')->with('mensaje_cabecera_status', $mensaje_cabecera_status)
                                      ->with('mensaje_cabecera_propuesta', $mensaje_cabecera_propuesta)
                                      ->with('titulo_cabecera_propuesta', $titulo_cabecera_propuesta)
                                      ->with('resultado_status', $resultado_status)
                                      ->with('total_resultado_status', $total_resultado_status)
                                      ->with('resultado_propuesta', $resultados_propuesta)
                                      ->with('proveedores', $proveedores)
                                      ->with('rangos', $rangos)
                                      ->with('fechaIni', $fecha_ini)
                                      ->with('fechaFin', $fecha_fin);
  }

  public function getVer($id)
  {
    //Localizamos le propuesta de alimentación
    $propuesta_alimentacion = PropuestaAlimentacionCabecera::find($id);
    
    // Buscamos para la granja los datos de producción más actuales.
    $ultima_fecha_produccion_real = DB::table('produccion_real')->where('site', '=', $propuesta_alimentacion->granja)
                                                                ->orderBy('date', 'desc')
                                                                ->first();
    //Echo 'Último dato de produccion' . $ultima_fecha_produccion_real->date;
    //Calculamos la diferencia de dias entre ambas fechas
    $datetime1 = new DateTime($propuesta_alimentacion->fecha_ini);
    $datetime2 = new DateTime($ultima_fecha_produccion_real->date);
    $interval = $datetime1->diff($datetime2);
    echo $datetime2->format('Y-m-d');
    //var_dump($datetime1);
    //var_dump($datetime2);
    //var_dump($interval);
    

    // Nueva parte para calcular la diferencia de dias
    $diferencia = strtotime($propuesta_alimentacion->fecha_ini) - strtotime($ultima_fecha_produccion_real->date);
    $diferencia = floor($diferencia/86400) - 1;
    $diferencia = $interval->format('%R%a');
    //echo ' ' . $diferencia;

    //echo ' Diferencia de días' . $interval->format('%R%a días');
    
    // Elaboramos la cabecera de la columna status
    if ($diferencia >= 0) 
    {
      //Recuperamos los datos de la 
      $fecha_cabecera = new DateTime($propuesta_alimentacion->fecha_ini);
      $mensaje_cabecera_status = 'Status ' . $propuesta_alimentacion->granja . ' ' . date_format($fecha_cabecera, 'd/m/y');
      
      // Elaboramos la columna del estatus. Seleccionamos datos reales
      $resultado_status = DB::select('select jaulas.nombre, pr.groupid, pr.stock_count_ini, pr.stock_avg_ini, pr.stock_bio_ini,
                                             ps.cantidad_toma_modelo, ps.sfr
                                        from produccion_real pr right join jaulas on pr.unitname = jaulas.nombre
                                                                                 and pr.date = ?
                                             left join produccion_simulado ps on  pr.unitname = ps.unitname 
                                                                              and  pr.site     = ps.site
                                                                              and  pr.date     = ps.date 
                                       where jaulas.granja_id = ?', array($propuesta_alimentacion->fecha_ini, $propuesta_alimentacion->granja_id));

      $total_resultado_status = DB::select('Select sum(stock_count_ini) as total_stock_ini, sum(stock_bio_ini) as total_bio_ini, 
                                                   (sum(stock_bio_ini)/sum(stock_count_ini))*1000 as total_avg_ini, sum(feeduse) as cantidad_toma
                                              from produccion_real
                                             where date = ?
                                               and site = ?', array($propuesta_alimentacion->fecha_ini, $propuesta_alimentacion->granja));


    }
    else
    {
      $fecha_cabecera = new DateTime($propuesta_alimentacion->fecha_ini);
      $mensaje_cabecera_status = 'Status ' . $propuesta_alimentacion->granja . ' ' . date_format($fecha_cabecera, 'd/m/y') . '(' . $diferencia . ' dias simulados)';

      // Elaboramos la columna del estatus. Seleccionamos datos simulados
      $resultado_status = DB::select('select jaulas.nombre, ps.groupid, ps.stock_count_ini, ps.stock_avg_ini, ps.stock_bio_ini,
                                             ps.cantidad_toma_modelo, ps.sfr
                                        from produccion_simulado ps right join jaulas on ps.unitname = jaulas.nombre
                                                                                     and ps.date = ?
                                       where jaulas.granja_id = ?', array($propuesta_alimentacion->fecha_ini, $propuesta_alimentacion->granja_id));

      $total_resultado_status = DB::select('Select sum(stock_count_ini) as total_stock_ini, sum(stock_bio_ini) as total_bio_ini, 
                                                   (sum(stock_bio_ini)/sum(stock_count_ini))*1000 as total_avg_ini, sum(cantidad_toma) as cantidad_toma
                                              from produccion_simulado
                                             where date = ?
                                               and site = ?', array($propuesta_alimentacion->fecha_ini, $propuesta_alimentacion->granja));
    }

    //Elaboramos la cabecera de la columna de la propuesta
    $mensaje_cabecera_propuesta = $propuesta_alimentacion->descripcion;
    $fecha_titulo_cabecera_ini  = new DateTime($propuesta_alimentacion->fecha_ini);
    $fecha_titulo_cabecera_fin  = new DateTime($propuesta_alimentacion->fecha_fin);
    $titulo_cabecera_propuesta  = $mensaje_cabecera_propuesta . ' Desde el ' . date_format($fecha_titulo_cabecera_ini, 'd/m/y')  . ' al ' . date_format($fecha_titulo_cabecera_fin, 'd/m/y') ;
    
    /*
    // Elaboramos la columna del estatus
    $resultado_status = DB::select('select jaulas.nombre, ps.groupid, ps.stock_count_ini, ps.stock_avg_ini, ps.stock_bio_ini,
                                        ps.cantidad_toma_modelo, ps.sfr
                                   from produccion_simulado ps right join jaulas on ps.unitname = jaulas.nombre
                                        and ps.date = ?
                                  where jaulas.granja_id = ?', array($propuesta_alimentacion->fecha_ini, $propuesta_alimentacion->granja_id));
    */
    //Mostramos el resultado de la propuesta
    $resultado_propuesta = DB::select('Select jaulas.nombre, vista_consumos.lote, vista_consumos.proveedor, vista_consumos.diametro_pienso, 
                                              vista_consumos.porcentaje_toma, vista_consumos.cantidad_toma_modelo as cantidad_recomendada, vista_consumos.cantidad_toma as cantidad, 
                                              (select jlr.cabecera_rango_id 
                                                 from jaula_lote_rango jlr, jaulas j, lotes l
                                                where jlr.jaula_id = j.id
                                                  and jlr.lote_id = l.id
                                                  and jlr.fecha_inicio <= ?
                                                  and j.nombre = jaulas.nombre
                                                  and l.nombre = vista_consumos.lote 
                                                  order by j.nombre, l.nombre, jlr.created_at desc
                                                  limit 1) as cabecera_rango_id
                                         from (Select c.jaula, c.lote, c.proveedor, c.diametro_pienso, min(ps.porcentaje_toma) as porcentaje_toma, 
                                                      min(ps.cantidad_toma_modelo) as cantidad_toma_modelo, min(ps.cantidad_toma) as cantidad_toma
                                                 from consumos c , produccion_simulado ps  
                                                where c.fecha >= ? 
                                                  and c.fecha <= ?
                                                  and c.jaula = ps.unitname 
                                                  and c.fecha = ps.date
                                                  and c.granja = ps.site
                                                  and c.granja_id = ?
                                             group by c.jaula, c.lote, c.proveedor, c.diametro_pienso) vista_consumos right join jaulas on vista_consumos.jaula = jaulas.nombre
                                        where jaulas.granja_id = ?
                                        order by jaulas.nombre, vista_consumos.diametro_pienso ', array($propuesta_alimentacion->fecha_ini,
                                                                                                        $propuesta_alimentacion->fecha_ini, 
                                                                                                        $propuesta_alimentacion->fecha_fin, 
                                                                                                        $propuesta_alimentacion->granja_id, 
                                                                                                        $propuesta_alimentacion->granja_id));

    //Formateamos la salida
    $salida_jaula = '';
    $salida_lote = '';
    $salida_pienso = '';
    $salida_cantidad_recomendada = 0;
    $salida_cantidad = 0;
    $salida_porcentaje_toma = 0;
    $salida_proveedor = '';
    $salida_rangos = '';
    $resultados_propuesta = array();
    $primera_linea = TRUE;
    foreach ($resultado_propuesta as $rs)
     {  //echo 'Jaula: ' . $rs->nombre;
        if (($salida_jaula == $rs->nombre))
        {
           $salida_pienso = $salida_pienso . ' + ' . $rs->diametro_pienso;
        }
        else
        {
          if ($primera_linea)
          { 
            $salida_jaula = $rs->nombre;
            $salida_lote =  $rs->lote;
            $salida_pienso = $rs->diametro_pienso;
            $salida_proveedor = $rs->proveedor;
            $salida_rangos = $rs->cabecera_rango_id;
            $salida_cantidad_recomendada = $rs->cantidad_recomendada;
            $salida_porcentaje_toma = $rs->porcentaje_toma;
            $salida_cantidad = $rs->cantidad;
            $primera_linea = FALSE;
          }
          else
           { $consumo_linea = [ 
                                         'nombre'               => $salida_jaula,
                                         'lote'                 => $salida_lote,
                                         'proveedor'            => $salida_proveedor,
                                         'rango'                => $salida_rangos,
                                         'diametro_pienso'      => $salida_pienso,
                                         'cantidad_recomendada' => $salida_cantidad_recomendada,
                                         'porcentaje_toma'      => $salida_porcentaje_toma,
                                         'cantidad'             => $salida_cantidad,
                                       ];
            array_push($resultados_propuesta, $consumo_linea);
            $salida_jaula = $rs->nombre;
            $salida_lote =  $rs->lote;
            $salida_pienso = $rs->diametro_pienso;
            $salida_cantidad_recomendada = $rs->cantidad_recomendada;
            $salida_porcentaje_toma = $rs->porcentaje_toma;
            $salida_cantidad = $rs->cantidad;
            $salida_proveedor = $rs->proveedor;
            $salida_rangos = $rs->cabecera_rango_id;
           }
        }
     }
     $consumo_linea = [ 
                                         'nombre'               => $salida_jaula,
                                         'lote'                 => $salida_lote,
                                         'proveedor'            => $salida_proveedor,
                                         'rango'                => $salida_rangos,
                                         'diametro_pienso'      => $salida_pienso,
                                         'cantidad_recomendada' => $salida_cantidad_recomendada,
                                         'porcentaje_toma'      => $salida_porcentaje_toma,
                                         'cantidad'             => $salida_cantidad,
                                       ];
            array_push($resultados_propuesta, $consumo_linea);
    //var_dump($resultados_propuesta);
    //var_dump($resultado_propuesta);
    //Recuperamos todos los proveedores
    $proveedores = Proveedorpienso::orderBy('nombre')->get();
    $rangos      = CabeceraRangos::orderBy('nombre')->get();

    return View::make('propuesta.ver')->with('mensaje_cabecera_status', $mensaje_cabecera_status)
                                      ->with('mensaje_cabecera_propuesta', $mensaje_cabecera_propuesta)
                                      ->with('titulo_cabecera_propuesta', $titulo_cabecera_propuesta)
                                      ->with('resultado_status', $resultado_status)
                                      ->with('total_resultado_status', $total_resultado_status)
                                      ->with('resultado_propuesta', $resultados_propuesta)
                                      ->with('proveedores', $proveedores)
                                      ->with('rangos', $rangos)
                                      ->with('fechaIni', $propuesta_alimentacion->fecha_ini)
                                      ->with('fechaFin', $propuesta_alimentacion->fecha_fin);
  }

    
}

 ?>