@extends('layout.main_layout')

@section('content')
<div class='contenedor'>
<h1>Nuevo Movimiento de Almacén</h1>
 
{{Form::open(array('url' => 'almacenes/movimientos/nuevo'))}}
    {{Form::label('almacen_id', 'Almacén:')}}
   	<select name="almacen_id" id="almacen_id">
   		@foreach($almacenes as $almacen)
          <option value="{{$almacen->id}}">{{$almacen->nombre}}</option>
   		@endforeach
   	</select>
     
   

    
      {{Form::label('tipo_movimiento', 'Tipo de movimiento:')}}
        <select name="tipo_movimiento" id="tipo_movimiento">
   		  <option value="Entrada">Entrada</option>
          <option value="Salida">Salida</option>
   		</select>

      {{Form::label('descripcion', 'Descripción:')}}
      <input type="text" name="descripcion" id="descripcion">
    {{Form::label('pienso_id', 'Pienso:')}}
   	<select name="pienso_id" id="pienso_id">
   		@foreach($piensos as $pienso)
          <option value="{{$pienso->id}}">{{$pienso->nombre}}</option>
   		@endforeach
   	</select>

      {{Form::label('cantidad', 'Cantidad:')}}
      <input type="text" name="cantidad" id="cantidad">
    
      {{Form::label('fecha_movimiento', 'Fecha del Movimiento:')}}
      <input type="text" name="fecha_movimiento" id="fecha_movimiento">
      
    

    
        {{Form::submit('Guardar', array('class' =>'btn'))}}
     


	{{ Form::close() }}
</div>
@stop