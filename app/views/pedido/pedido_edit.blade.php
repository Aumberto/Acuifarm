@extends('layout.main_layout')

@section('content')
<div class="container">
  
<form action='../edit/{{$pedido->id}}' method='post'>
  <input type='hidden' name='procesar'>

  <label for='num_pedido'>Número de Pedido:</label>
  <input type='text' name='num_pedido' value='{{$pedido->num_pedido}}' disabled><br>

  <label for='num_contenedor'>Número de Contenedor:</label>
  <input type='text' name='num_contenedor' value='{{$pedido->num_contenedor}}'><br>

  <label for='proveedor_id'>Proveedor:</label>
  <select name='proveedor_id' disabled>
    @foreach ($proveedores as $proveedor)
      @if ($proveedor->id == $pedido->proveedor_id)
        <option value='{{$proveedor->id}}' selected>{{$proveedor->nombre}}</option>
      @else
        <option value='{{$proveedor->id}}'>{{$proveedor->nombre}}</option>
      @endif
    @endforeach
  </select><br>

  <label for='importe'>Importe:</label>
  <input type='text' name='importe' value='{{$pedido->importe}}'><br>

  <label for='estado'>Estado:</label>
  <input type='radio' name='estado' value='En preparación' @if ($pedido->estado == 'En preparación') checked='checked' @endif/>En preparación
  <input type='radio' name='estado' value='En tránsito' @if ($pedido->estado == 'En tránsito') checked='checked' @endif/>En tránsito
  <input type='radio' name='estado' value='Pendiente de descarga' @if ($pedido->estado == 'Pendiente de descarga') checked='checked' @endif/>Pendiente de descarga
  <input type='radio' name='estado' value='Descargado' @if ($pedido->estado == 'Descargado') checked='checked' @endif/>Descargado <br>

  <label for='fecha_pedido'>Fecha del Pedido:</label>
  <input type='text' name='fecha_pedido' id='fecha_pedido' value='{{date("d-m-Y",strtotime($pedido->fecha_pedido))}}'><br>

  <label for='fecha_confirmacion'>Fecha de Confirmación del Pedido:</label>
  <input type='text' name='fecha_confirmacion' id='fecha_confirmacion' value='{{date("d-m-Y",strtotime($pedido->fecha_confirmacion))}}'><br>

  <label for='fecha_carga'>Fecha de Carga:</label>
  <input type='text' name='fecha_carga' id='fecha_carga' value='{{date("d-m-Y",strtotime($pedido->fecha_carga))}}'><br>

  <label for='fecha_llegada'>Fecha de llegada al muelle:</label>
  <input type='text' name='fecha_llegada' id='fecha_llegada' value='{{date("d-m-Y",strtotime($pedido->fecha_llegada))}}'><br>

  <label for='fecha_descarga'>Fecha de Descarga:</label>
  <input type='text' name='fecha_descarga' id='fecha_descarga' value='{{date("d-m-Y",strtotime($pedido->fecha_descarga))}}'><br>

  <label for='pagado'>Pagado:</label>
  <input type='checkbox' name='pagado' value='{{$pedido->pagado}}' @if ($pedido->pagado == 1) checked='checked' @endif >

  <label for='fecha_pago'>Fecha del Pago:</label>
  <input type='text' name='fecha_pago' id='fecha_pago' value='{{date("d-m-Y",strtotime($pedido->fecha_pago))}}'><br>

  <input type="submit" value="Actualizar">
  
</form>

{{HTML::link('/pedido', 'Volver')}}
</div>


@stop