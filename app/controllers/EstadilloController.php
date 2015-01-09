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
             $fecha_ayuno = date("Y-m-d");
          }
          else
          {
              //$fecha = "2014-12-20";
              //$fecha=Input::get('fecha_pedido');
              list($dia, $mes, $year)=explode("-", $fecha);
              $fecha_ayuno=$year."-".$mes."-".$dia;
          }
          //Recuperamos los datos del estadillo para la granja y el día concreto
          $datos = ProduccionSimuladas::where('date', '=', $fecha_ayuno)->orderby('site')->orderby('unitname')->get();
          return View::make('ayuno.ayuno_diario')->with('datos', $datos)
                                                 ->with('fecha', $fecha_ayuno);
     }

     

     
 }
 ?>