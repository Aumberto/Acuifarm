@extends('layout.main_layout')

@section('content')
<div class="container">
  
<form action='../edit/{{$pedido->id}}' method='post' class="form-horizontal">
  <input type='hidden' name='procesar'>
  <div class="form-group">
      <label for='num_pedido' class="col-sm-3 control-label">Número de Pedido:</label>
      <div class="col-sm-2">
        <input type="text" class="form-control input-sm" name="num_pedido" id="num_pedido" placeholder="Número de Pedido:" value='{{$pedido->num_pedido}}' disabled>
      </div>
  </div>

<div class="form-group">

  <label class="col-sm-3 control-label" for='num_contenedor'>Número de Contenedor:</label>
  <div class="col-sm-2">
   <input type='text' class="form-control input-sm" name='num_contenedor' id='num_contenedor'value='{{$pedido->num_contenedor}}'>
  </div>
</div>

<div class="form-group">

  <label class="col-sm-3 control-label" for='proveedor_id'>Proveedor:</label>
  <div class="col-sm-3">
  <select class="form-control input-sm" name='proveedor_id' disabled>
    @foreach ($proveedores as $proveedor)
      @if ($proveedor->id == $pedido->proveedor_id)
        <option value='{{$proveedor->id}}' selected>{{$proveedor->nombre}}</option>
      @else
        <option value='{{$proveedor->id}}'>{{$proveedor->nombre}}</option>
      @endif
    @endforeach
  </select>
  </div>
</div>

<div class="form-group">
  <label class="col-sm-3 control-label"  for='importe'>Importe:</label>
  <div class="col-sm-2">
   <input class="form-control input-sm" type='text' name='importe' value='{{$pedido->importe}}'>
  </div>
</div>

<div class="form-group">
  <label class="col-sm-3 control-label" for='estado'>Estado:</label>
  <div class="col-sm-8">
    <input  type='radio' name='estado' value='En preparación' @if ($pedido->estado == 'En preparación') checked='checked' @endif/> En preparación
    <input  type='radio' name='estado' value='En tránsito' @if ($pedido->estado == 'En tránsito') checked='checked' @endif/> En tránsito
    <input  type='radio' name='estado' value='Pendiente de descarga' @if ($pedido->estado == 'Pendiente de descarga') checked='checked' @endif/> Pendiente de descarga
    <input  type='radio' name='estado' value='Descargado' @if ($pedido->estado == 'Descargado') checked='checked' @endif/> Descargado 
  </div>
</div>

<div class="form-group">
  <label class="col-sm-3 control-label" for='fecha_pedido'>Fecha del Pedido:</label>
  <div class="col-sm-2">
    <input class="form-control input-sm" type='text' name='fecha_pedido' id='fecha_pedido' value='{{date("d-m-Y",strtotime($pedido->fecha_pedido))}}'>
  </div>
</div>

<div class="form-group">
  <label class="col-sm-3 control-label" for='fecha_confirmacion'>Fecha de Confirmación del Pedido:</label>
  <div class="col-sm-2">
  <input class="form-control input-sm" type='text' name='fecha_confirmacion' id='fecha_confirmacion' value='{{date("d-m-Y",strtotime($pedido->fecha_confirmacion))}}'>
</div>
</div>

<div class="form-group">
  <label class="col-sm-3 control-label" for='fecha_carga'>Fecha de Carga:</label>
  <div class="col-sm-2">
  <input class="form-control input-sm" type='text' name='fecha_carga' id='fecha_carga' value='{{date("d-m-Y",strtotime($pedido->fecha_carga))}}'>
</div>
</div>

<div class="form-group">
  <label class="col-sm-3 control-label" for='fecha_llegada'>Fecha de llegada al muelle:</label>
  <div class="col-sm-2">
  <input class="form-control input-sm" type='text' name='fecha_llegada' id='fecha_llegada' value='{{date("d-m-Y",strtotime($pedido->fecha_llegada))}}'>
</div>
</div>

<div class="form-group">
  <label class="col-sm-3 control-label" for='fecha_descarga'>Fecha de Descarga:</label>
  <div class="col-sm-2">
  <input class="form-control input-sm" type='text' name='fecha_descarga' id='fecha_descarga' value='{{date("d-m-Y",strtotime($pedido->fecha_descarga))}}'>
</div>
</div>

<div class="form-group">
  
  <label class="col-sm-3 control-label" for='pagado'>Pagado:</label>
  <div class="col-sm-2">
  <input type='checkbox' name='pagado' value='{{$pedido->pagado}}' @if ($pedido->pagado == 1) checked='checked' @endif >

</div>
</div>

<div class="form-group">
  <label class="col-sm-3 control-label" for='fecha_pago'>Fecha del Pago:</label>
  <div class="col-sm-2">
  <input class="form-control input-sm" type='text' name='fecha_pago' id='fecha_pago' value='{{date("d-m-Y",strtotime($pedido->fecha_pago))}}'>
</div>
</div>
  
<div class="form-group">
  <div class="row">
    <div class="col-md-6 col-md-offset-3">
    <input class="btn btn-primary" type="submit" value="Actualizar">
    
    {{HTML::link('/pedido', 'Cancelar',  array('class' =>'btn btn-primary'))}}
    </div>
  </div>
</div>
</form>


</div>


@stop