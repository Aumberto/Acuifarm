<?php 

class PedidoController extends BaseController{

  public function getIndex(){

  		$pedidos = Pedido::where('pagado', '<>', '1')->orderby('proveedor_id')->orderby('fecha_descarga')->get();
  		$proveedor ='';
      $primerafila = True;
      $pedidos_proveedor_array = array();
      $pedidos_proveedor = array();
      foreach ($pedidos as $pedido) 
      {
        if ($proveedor <> $pedido->proveedor->nombre)
         {
            
            if ($primerafila)
             {
                
                $primerafila = False;
                
             }
             else
             {

                $datos_proveedor = array('proveedor' => $proveedor,
                                         'pedidos' =>  $pedidos_proveedor_array);
                array_push($pedidos_proveedor, $datos_proveedor);
                $pedidos_proveedor_array = array();
             }
             $proveedor = $pedido->proveedor->nombre;
             if ( (($pedido->proveedor_id == 1) and ($pedido->estado == 'Descargado') and ($pedido->pagado == 0)) or 
                  (($pedido->proveedor_id == 2) and ($pedido->estado <> 'En preparación') and ($pedido->pagado == 0))
                )
             {
               $color = "class='danger'";
             }
             else
             {
               $color = " ";
             }
             $datos_pedido = array( 'id'             => $pedido->id,
                                    'pagado'         => $pedido->pagado,
                                    'num_pedido'     => $pedido->num_pedido, 
                                    'fecha_pedido'   => $pedido->fecha_pedido, 
                                    'fecha_carga'    => $pedido->fecha_carga, 
                                    'fecha_descarga' => $pedido->fecha_descarga, 
                                    'fecha_pago'     => $pedido->fecha_pago, 
                                    'importe'        => $pedido->importe, 
                                    'estado'         => $pedido->estado,
                                    'clase'          => $color);
                array_push($pedidos_proveedor_array, $datos_pedido);
         }
         else
         {
             if ( (($pedido->proveedor_id == 1) and ($pedido->estado == 'Descargado') and ($pedido->pagado == 0)) or 
                  (($pedido->proveedor_id == 2) and ($pedido->estado <> 'En preparación') and ($pedido->pagado == 0))
                )
              {
               $color = "class='danger'";
              }
              else
              {
               $color = " ";
              }

             $datos_pedido = array( 'id'             => $pedido->id,
                                    'pagado'         => $pedido->pagado,
                                    'num_pedido'     => $pedido->num_pedido, 
                                    'fecha_pedido'   => $pedido->fecha_pedido, 
                                    'fecha_carga'    => $pedido->fecha_carga, 
                                    'fecha_descarga' => $pedido->fecha_descarga, 
                                    'fecha_pago'     => $pedido->fecha_pago, 
                                    'importe'        => $pedido->importe, 
                                    'estado'         => $pedido->estado,
                                    'clase'          => $color);
             array_push($pedidos_proveedor_array, $datos_pedido);
         }
      }
      $datos_proveedor = array('proveedor' => $proveedor,
                               'pedidos' =>  $pedidos_proveedor_array);
      array_push($pedidos_proveedor, $datos_proveedor);
      //print_r($pedidos_proveedor);
      return View::make('pedido.pedido_list')->with('pedidos', $pedidos)
                                             ->with('listado_pedidos', $pedidos_proveedor);
  }

  public function getAdd(){

       $proveedores = Proveedorpienso::all();
  	   return View::make('pedido.pedido_add')->with('proveedores', $proveedores);
  }

  public function getNew(){
      
       $fecha=Input::get('fecha_pedido');
       list($dia, $mes, $year)=explode("-", $fecha);
       $fecha_pedido=$year."-".$mes."-".$dia;

       $fecha=Input::get('fecha_descarga');
       list($dia, $mes, $year)=explode("-", $fecha);
       $fecha_descarga=$year."-".$mes."-".$dia;
       
       Pedido::create(
        array(
          'num_pedido' => Input::get('num_pedido'),
          'num_contenedor' => 0, //Input::get('num_pedido'),
          'proveedor_id' => Input::get('proveedor_id'),
          'importe' => 0, //Input::get('importe'),
          //'pagado' => Input::get('pagado'),
          //'estado' => Input::get('estado'),
          'fecha_pedido' => $fecha_pedido,
          'fecha_confirmacion' => $fecha_pedido,
          'fecha_carga' => $fecha_pedido,
          'fecha_llegada' => $fecha_pedido,
          'fecha_descarga' => $fecha_descarga,
          'fecha_pago' => $fecha_pedido
          ));

  	   // Obtenemos el id del último pedido insertado
  	   $pedido = Pedido::all();

  	   

  	   return Redirect::to('pedido/ver/'.$pedido->last()->id);
  }

  public function getVer($id){

     $pedido = Pedido::find($id);
     $detalles = Pedido::find($id)->detallepedido;
     
     // Obtenemos el id del proveedor de dicho pedido
  	 $proveedor_id = $pedido->proveedor_id;
  	 $proveedor = Proveedorpienso::find($proveedor_id);
     $piensos = Pienso::where('proveedor_id', '=', $proveedor_id)->orderby('nombre')->get();

     $pedido_total=PedidoDetalle::with('pedido')->where('pedido_id','=',$id)->sum('total');
     if ($pedido_total <> $pedido->importe)
     {
        $pedido->importe = $pedido_total;
        $pedido->save();
     }

     $pedido_total_cantidad=PedidoDetalle::with('pedido')->where('pedido_id','=',$id)->sum('cantidad');
  	 // Obtenemos el listado de piensos de dicho proveedor de pienso

     return View::make('pedido.pedido_ver')
            ->with('pedido', $pedido)
            ->with('piensos', $piensos)
            ->with('proveedor', $proveedor)
            ->with('detalles', $detalles)
            ->with('pedidototal', $pedido_total)
            ->with('pedidototalcantidad', $pedido_total_cantidad);

  }

  public function getDelete($id){
  	$pedido = Pedido::find($id);
    $detallepedido = PedidoDetalle::where('pedido_id', '=', $id)->delete();
    //$detallepedido->delete();
    $pedido->delete();
	return Redirect::to('pedido');
  }

  public function getEdit($id)
  {
    if (isset($_POST['procesar']))
    {  
      //echo 'Procesamos el pedido';

      // Procesamos las fechas 
       $fecha=Input::get('fecha_pedido');
       list($dia, $mes, $year)=explode("-", $fecha);
       $fecha_pedido=$year."-".$mes."-".$dia;

       $fecha=Input::get('fecha_confirmacion');
       list($dia, $mes, $year)=explode("-", $fecha);
       $fecha_confirmacion=$year."-".$mes."-".$dia;

       $fecha=Input::get('fecha_carga');
       list($dia, $mes, $year)=explode("-", $fecha);
       $fecha_carga=$year."-".$mes."-".$dia;

       $fecha=Input::get('fecha_llegada');
       list($dia, $mes, $year)=explode("-", $fecha);
       $fecha_llegada=$year."-".$mes."-".$dia;

       $fecha=Input::get('fecha_descarga');
       list($dia, $mes, $year)=explode("-", $fecha);
       $fecha_descarga=$year."-".$mes."-".$dia;

       $fecha=Input::get('fecha_pago');
       list($dia, $mes, $year)=explode("-", $fecha);
       $fecha_pago=$year."-".$mes."-".$dia;

       $pedido= Pedido::find($id);
       
       $estado_pedido_antes_actualizar = $pedido->estado;
       

       $pedido->num_contenedor = Input::get('num_contenedor'); 
       $pedido->importe = Input::get('importe');
       $pedido->fecha_pedido = $fecha_pedido;
       $pedido->fecha_confirmacion = $fecha_confirmacion;
       $pedido->fecha_carga = $fecha_carga;
       $pedido->fecha_llegada = $fecha_llegada;
       $pedido->fecha_descarga = $fecha_descarga;
       $pedido->fecha_pago = $fecha_pago;
       
       
       if (isset($_POST['pagado']))
       {
         $pedido->pagado = 1;
       }
       else
       {
         $pedido->pagado = 0;
       }

       
       $pedido->estado = Input::get('estado');
       $pedido->save();

       if ((Input::get('estado') == 'Descargado') and ($estado_pedido_antes_actualizar <> 'Descargado'))
       {
         $this->Descargar_Pedido($id);
       }

       
       return Redirect::to('/pedido');
    }
    else
    {
      //mostramos el formulario con los datos de la cabecera del pedido
      $pedido = Pedido::find($id);

      // Localizamos todos los proveedores
      $proveedores = Proveedorpienso::orderby('nombre')->get();
      return View::make('pedido.pedido_edit')
                 ->with('pedido', $pedido)
                 ->with('proveedores', $proveedores);
    }  
  }

 private function Descargar_Pedido($id_pedido)
 {
    
    // Recuperamos los datos del pedido
    $pedido = $pedido= Pedido::find($id_pedido);
    // Recuperamos las filas del pedido
    $pedido_detalles = PedidoDetalle::where('pedido_id', '=', $id_pedido)->get();
    foreach ($pedido_detalles as $pedido_detalle)
    {
      $nuevo_registro = new MovimientosAlmacen();
      $nuevo_registro->almacen_id =1;
      $nuevo_registro->tipo_movimiento = 'Entrada';
      $nuevo_registro->descripcion ='Pedido '. $pedido->num_pedido;
      $nuevo_registro->pienso_id =$pedido_detalle->pienso_id;
      $nuevo_registro->cantidad = $pedido_detalle->cantidad;
      $nuevo_registro->fecha = $pedido->fecha_descarga;
      $nuevo_registro->save();
    }

    return true;
 }
}

 ?>