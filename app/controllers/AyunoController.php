<?php 
 class AyunoController extends BaseController
 {
     public function getIndex()
     {
     	// Obtenemos los datos simulados del día actual
     	  $fecha = Input::get('fecha_pedido');
     	  if (!isset($fecha)) 
     	  {
             //echo "Esta variable no está definida, así que se imprimirá";
             $fecha_ayuno = date("Y-m-d");
          }
          else
          {
              //$fecha = "2014-12-20";
              //$fecha=Input::get('fecha_pedido');
              list($dia, $mes, $year)=explode("-", $fecha);
              $fecha_ayuno=$year."-".$mes."-".$dia;
          }
          
          $datos = ProduccionSimuladas::where('date', '=', $fecha_ayuno)->orderby('site')->orderby('unitname')->get();

          $granja ='';
          $primerafila = True;
          $ayuno_granja_array = array();
          $ayuno_granja = array();
          foreach ($datos as $linea_ayuno) 
           {
             if ($granja <> $linea_ayuno->site)
               {
                 if ($primerafila)
                   {
                     $primerafila = False;
                   }
                 else
                   {
                     $datos_granja = array('granja' => $granja,
                                            'ayuno_granja'   => $ayuno_granja_array);
                     array_push($ayuno_granja, $datos_granja);
                     $ayuno_granja_array = array();
                   }
                 $granja = $linea_ayuno->site;
                 $datos_ayuno = array( 'id'             => $linea_ayuno->id,
                                       'unitname'         => $linea_ayuno->unitname,
                                       'groupid'       => $linea_ayuno->groupid,
                                       'stock_count_ini' => $linea_ayuno->stock_count_ini,
                                       'stock_avg_ini' => $linea_ayuno->stock_avg_ini,
                                       'ayuno' => $linea_ayuno->ayuno);
                 array_push($ayuno_granja_array, $datos_ayuno);
               }
             else
               {
                 $datos_ayuno = array( 'id'             => $linea_ayuno->id,
                                       'unitname'         => $linea_ayuno->unitname,
                                       'groupid'       => $linea_ayuno->groupid,
                                       'stock_count_ini' => $linea_ayuno->stock_count_ini,
                                       'stock_avg_ini' => $linea_ayuno->stock_avg_ini,
                                       'ayuno' => $linea_ayuno->ayuno);
                 array_push($ayuno_granja_array, $datos_ayuno);
               }
           }
          $datos_granja = array('granja' => $granja,
                                'ayuno_granja'   => $ayuno_granja_array);
          array_push($ayuno_granja, $datos_granja);


          return View::make('ayuno.ayuno_diario')->with('datos', $datos)
                                                 ->with('fecha', $fecha_ayuno)
                                                 ->with('ayuno_granja', $ayuno_granja);
     }

     

     
 }
 ?>