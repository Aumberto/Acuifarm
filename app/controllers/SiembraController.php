<?php 
 class SiembraController extends BaseController
 {
     public function getIndex()
     {
          // Mostramos un listado de todas las siembras cuya.
          $siembras = Siembra::all();

          return View::make('siembra.siembra_list')->with('siembras', $siembras);
     	
     }

     public function getAdd()
     {
          // Función para añadir siembras
          // Recuperamos todas las granjas
          $granjas = Granja::all();

          // Recuperamos todas las jaulas
          $jaulas = Jaula::orderby('granja_id')->orderby('nombre')->get();

          // Recuperamos todos las tabla de alimentación
          $tablas_alimentacion = CabeceraRangos::all();

          // Recuperamos todos los lotes
          $lotes = Lote::orderby('nombre')->get();

          return View::make('siembra.siembra_add')->with('granjas', $granjas)
                                                  ->with('jaulas', $jaulas)
                                                  ->with('lotes', $lotes)
                                                  ->with('tablas_alimentacion', $tablas_alimentacion);
     }

     public function getNew()
     {
          //Capturamos los datos del formulario
          $fecha=Input::get('fecha_siembra');
          list($dia, $mes, $year)=explode("-", $fecha);
          $fecha_siembra=$year."-".$mes."-".$dia;
          
          $granja_id             = Input::get('granja_id');
          $jaula_id              = Input::get('jaula_id');
          $lote_id                  = Input::get('lote_id');
          $tabla_alimentacion_id = Input::get('tabla_alimentacion_id');
          $input_count           = Input::get('input_count');
          $input_avg             = Input::get('input_avg');
          $input_bio             = Input::get('input_bio');

          $granja = Granja::find($granja_id);
          $jaula  = Jaula::find($jaula_id);
          $lote   = Lote::find($lote_id);


          // Buscamos si la jaula está vacía en la fecha seleccionada
          $produccion_simulada = ProduccionSimuladas::where('site', '=', $granja->nombre)
                                                    ->where('unitname', '=', $jaula->nombre)
                                                    ->where('date', '=', $fecha_siembra)
                                                    ->get();
          
          if (count($produccion_simulada) <= 0)
           {
               // La jaula está vacía
               //echo 'La jaula está vacía';
               $fecha_ini = new DateTime($fecha_siembra);
               $fecha_temp = new DateTime($fecha_siembra);
               $fecha_temp->modify('+1 day');
               $dias_actualizar = 180; //$produccion->DiasActualiza($fecha_siembra);
               $fecha_fin = $fecha_temp->modify('+' . $dias_actualizar . ' day');

               // Asignamos el rango de Pellet
               // Generamos el registro en la tabla de jaula_lote_rango 
               $nuevo_registro_jaula_lote_rango = new JaulaLoteRango();
               $nuevo_registro_jaula_lote_rango->jaula_id          = $jaula_id;
               $nuevo_registro_jaula_lote_rango->lote_id           = $lote_id;
               $nuevo_registro_jaula_lote_rango->cabecera_rango_id = $tabla_alimentacion_id;
               $nuevo_registro_jaula_lote_rango->fecha_inicio      = $fecha_ini;
               $nuevo_registro_jaula_lote_rango->save();


               // Insertamos los datos en la tabla de simulación de producción
               $produccion = new ProduccionController();
               $produccion->actualizarSimulacionIntervalo($granja->nombre, $jaula->nombre, $lote->nombre, $input_count, $input_avg, $fecha_ini, $fecha_fin);

               // Insertamos los datos de consumos
               $fecha_ini = new DateTime($fecha_siembra);
               $fecha_temp = new DateTime($fecha_siembra);
               $fecha_temp->modify('+1 day');
               $dias_actualizar = 180; //$produccion->DiasActualiza($fecha_siembra);
               $fecha_fin = $fecha_temp->modify('+' . $dias_actualizar . ' day');

               $consumo = new ConsumoController();
               $consumo->ActualizarConsumoSimuladoIII($jaula->nombre, $fecha_ini, $fecha_fin, 0);

               // Insertamos los datos en la tabla de siembras.
               $nuevo_registro_siembras = new Siembra();
               $nuevo_registro_siembras->granja_id = $granja_id ;
               $nuevo_registro_siembras->jaula_id = $jaula_id;
               $nuevo_registro_siembras->lote_id = $lote_id;
               $nuevo_registro_siembras->cabecera_rangos_id = $tabla_alimentacion_id;
               $nuevo_registro_siembras->input_count = $input_count;
               $nuevo_registro_siembras->input_avg = $input_avg;
               $nuevo_registro_siembras->input_bio = $input_bio ;
               $nuevo_registro_siembras->fecha = $fecha_siembra;
               $nuevo_registro_siembras->save();

               
               
           }
           else
           {
               // La jaula no está vacía
               //echo 'La jaula no está vacía';
           }  
       
           return Redirect::to('siembras');
     }

     public function getEditar($id)
     {

     }

     public function getEliminar($id)
     {
        $siembra = Siembra::find($id);
        $jaula = $siembra->jaula->nombre;
        $lote = $siembra->lote->nombre;
        
        // Debemos eliminar los datos de esa jaula y lote desde hoy en adelante.
        /*
        $datos_a_eliminar = DB::select('Delete from produccion_simulado 
                                                          where date >=? 
                                                            and unitname = ? 
                                                            and groupid = ?', array($siembra->fecha, 'J022', '1469')); */

        $produccion_simulada_a_eliminar = ProduccionSimuladas::where('date', '>=', $siembra->fecha)
                                                             ->where('unitname', '=', $siembra->jaula->nombre)
                                                             ->where('groupid', '=', $siembra->lote->nombre)
                                                             ->delete();
        // Debemos eliminar los datos de consumo de esa jaula y lote desde hoy en adelante.
        /*
        $datos_a_eliminar = DB::select('Delete from consumos
                                              where fecha >= ?
                                              and jaula = ?
                                              and lote = ?', array($siembra->fecha, $siembra->jaula->nombre, $siembra->lote->nombre)); */

        $consumos_a_eliminar = Consumo::where('fecha', '>=', $siembra->fecha)
                                       ->where('jaula', '=', $siembra->jaula->nombre)
                                       ->where('lote', '=', $siembra->lote->nombre)
                                       ->delete();

        // Eliminamos el registro en la tabla de rangos.
        /*                               
        $datos_a_eliminar = DB::select('Delete from jaula_lote_rango
                                              where fecha_inicio >= ?
                                              and jaula_id = ?
                                              and lote_id = ?
                                              and cabecera_rango_id = ?', array($siembra->fecha, $siembra->jaula_id, $siembra->lote_id, $siembra->cabecera_rango_id)); */
         
        $jaula_lote_rango_a_eliminar = JaulaLoteRango::where('fecha_inicio', '=', $siembra->fecha)
                                                     ->where('jaula_id', '=', $siembra->jaula_id)
                                                     ->where('lote_id', '=', $siembra->lote_id)
                                                     ->where('cabecera_rango_id', '=', $siembra->cabecera_rangos_id)
                                                     ->delete();
        
        // Eliminamos el registro en la tabla de siembras
        $siembra->delete();
        return Redirect::to('siembras');
     }
 }
 ?>