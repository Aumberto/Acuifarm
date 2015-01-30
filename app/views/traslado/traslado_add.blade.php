@extends('layout.main_layout')

@section('content')
  

<div class="container">
<h2>Nuevo Traslado</h2>
{{ Form::open(array('url' => 'traslado/add', 'method' => 'Post' , 'class' =>'form-horizontal')) }}
<div class="form-group">
 {{Form::label('trasladonombre', 'Traslado', array('class' => 'col-sm-3 control-label'))}} 
 <div class="col-sm-3">
  {{Form::text('trasladonombre', '', array('class' => 'form-control input-sm'))}}
 </div>
</div>

<div class="form-group">
 {{Form::label('almacenorigen', 'Almacén Origen', array('class' => 'col-sm-3 control-label'))}}
 <div class="col-sm-3">
  <select name='almacenorigen' class="form-control input-sm">
   @foreach ($almacenes as $almacen)
     <option value='{{$almacen->id}}'>{{$almacen->nombre}}</option>
   @endforeach
  </select>
 </div>
</div>

<div class="form-group">
  {{Form::label('almacendestino', 'Almacén Destino', array('class' => 'col-sm-3 control-label'))}}
  <div class="col-sm-3">
    <select name='almacendestino' class="form-control input-sm">
      @foreach ($almacenes as $almacen)
        <option value='{{$almacen->id}}'>{{$almacen->nombre}}</option>
      @endforeach
    </select>
  </div>
</div>

<div class="form-group">
  {{Form::label('fecha_descarga', 'Fecha de descarga:', array('class' => 'col-sm-3 control-label'))}}
  <div class="col-sm-3">
    <input type='text' name='fecha_descarga' id='fecha_descarga' class="form-control input-sm">
  </div>
</div>

<div class="form-group">
      <div class="row">
        <div class="col-md-6 col-md-offset-3">
          {{Form::submit('Crear', array('class' =>'btn btn-primary'))}}
          {{HTML::link('/traslado', 'Cancelar',  array('class' =>'btn btn-primary'))}}
        </div>
      </div>
</div>

{{Form::close() }}
  
</div>




  
@stop