@extends('layout.main_layout')

@section('content')

<div class="container">
	
	<h1>Nuevo Pedido</h1>
	{{Form::open(array('url' => 'pedido/add', 'class' =>'form-horizontal'))}}
    <div class="control-group">
      {{Form::label('num_pedido', 'Pedido num:', array('class' => 'control-label'))}}
      <div class="controls">
        {{Form::text('num_pedido')}}
      </div>
    </div>

    <div class="control-group">
   {{Form::label('proveedor_id', 'Proveedor', array('class' => 'control-label'))}}
   <div class="controls">
   	<select name="proveedor_id" id="proveedor_id">
   		@foreach($proveedores as $proveedor)
          <option value="{{$proveedor->id}}">{{$proveedor->nombre}}</option>
   		@endforeach
   	</select>
     
   </div>
  </div>

    <div class="control-group">
      {{Form::label('num_contenedor', 'Número Contenedor:', array('class' => 'control-label'))}}
      <div class="controls">
        {{Form::text('num_contenedor')}}
      </div>
    </div>

    <div class="control-group">
      {{Form::label('fecha_pedido', 'Fecha Pedido:', array('class' => 'control-label'))}}
      <div class="controls">
      	<input type="text" name="fecha_pedido" id="fecha_pedido">
        
      </div>
    </div>
    <div class="control-group">
      {{Form::label('fecha_descarga', 'Fecha Descarga:', array('class' => 'control-label'))}}
      <div class="controls">
        
        <input type="text" name="fecha_descarga" id="fecha_descarga">
      </div>
    </div>

    <div class="control-group">
      <div class="controls">
        {{Form::submit('Guardar', array('class' =>'btn'))}}
      </div>
    </div>


	{{ Form::close() }}

</div>


@stop