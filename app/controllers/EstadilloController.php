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
                $nuevo_estadillo->num_tomas= 1;
                $nuevo_estadillo->porcentaje_primera_toma=100;
                $nuevo_estadillo->save();
              }
           }
          // Si no existe, lo creamos
          $datos_estadillos = DB::select('select j.nombre as jaula, g.nombre as granja, ifnull(ps.groupid,"-") as lote, ifnull(ps.stock_count_ini,0) as stock_count_ini, 
                                                 ifnull(ps.stock_avg_ini,0) as stock_avg_ini, ifnull(ps.stock_bio_ini,0) as stock_bio_ini, ifnull(ps.cantidad_toma,0) as cantidad_toma, 
                                                 ifnull(c.pienso,"-") as pienso, ifnull(c.diametro_pienso,"-") as diametro_pienso, ifnull(c.cantidad,0) as cantidad,
                                                 ifnull(e.num_tomas, 0) as num_tomas, ifnull(e.porcentaje_primera_toma, 0) as porcentaje_primera_toma
                                            from jaulas j left join produccion_simulado ps on j.nombre = ps.unitname and ps.date =  ? 
                                                 left join consumos c on j.nombre = c.jaula and c.fecha =  ? 
                                                 left join estadillos e on j.id = e.jaula_id and e.fecha = ? , granjas g
                                           where j.granja_id = 2
                                             and j.granja_id = g.id
                                        order by j.nombre', array($fecha_estadillo, $fecha_estadillo, $fecha_estadillo));
          
          return View::make('estadillo.estadillo_diario')->with('fecha', $fecha_estadillo)
                                                         ->with('datos', $datos_estadillos);
     }

     

     
 }
 ?>