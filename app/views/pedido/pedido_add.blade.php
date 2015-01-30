@extends('layout.main_layout')

@section('content')

<div class="container">
	
	<h2>Nuevo Pedido</h1>
	{{Form::open(array('url' => 'pedido/add', 'class' =>'form-horizontal'))}}
    <div class="form-group">
      {{Form::label('num_pedido', 'Pedido num:', array('class' => 'col-sm-3 control-label'))}}
      <div class="col-sm-3">
        {{Form::text('num_pedido', '', array('class' => 'form-control input-sm'))}}
      </div>
    </div>

    <div class="form-group">
     {{Form::label('proveedor_id', 'Proveedor:', array('class' => 'col-sm-3 control-label'))}}
       <div class="col-sm-3">
   	     <select name="proveedor_id" id="proveedor_id" class="form-control input-sm">
   		     @foreach($proveedores as $proveedor)
             <option value="{{$proveedor->id}}">{{$proveedor->nombre}}</option>
   		     @endforeach
   	     </select>
       </div>
    </div>

    <div class="form-group">
      {{Form::label('num_contenedor', 'Número Contenedor:', array('class' => 'col-sm-3 control-label'))}}
      <div class="col-sm-3">
        {{Form::text('num_contenedor', '', array('class' => 'form-control input-sm'))}}
      </div>
    </div>

    <div class="form-group">
      {{Form::label('fecha_pedido', 'Fecha Pedido:', array('class' => 'col-sm-3 control-label'))}}
      <div class="col-sm-3">
      	<input type="text" name="fecha_pedido" id="fecha_pedido" class='form-control input-sm'>
        
      </div>
    </div>

    <div class="form-group">
      {{Form::label('fecha_descarga', 'Fecha Descarga:', array('class' => 'col-sm-3 control-label'))}}
      <div class="col-sm-3">
        <input type="text" name="fecha_descarga" id="fecha_descarga" class='form-control input-sm'>
      </div>
    </div>

    <div class="form-group">
      <div class="row">
        <div class="col-md-6 col-md-offset-3">
            {{Form::submit('Guardar', array('class' =>'btn btn-primary'))}}
            {{HTML::link('/pedido', 'Cancelar',  array('class' =>'btn btn-primary'))}}
        </div>
      </div>
    </div>


	{{ Form::close() }}

</div>


@stop