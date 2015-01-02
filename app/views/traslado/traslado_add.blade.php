@extends('layout.main_layout')

@section('content')
  

<div class="container">
<h1>Traslados</h1>
{{ Form::open(array('url' => 'traslado/add', 'method' => 'Post')) }}

{{Form::label('trasladonombre', 'Traslado')}} 
{{Form::text('trasladonombre')}} <br>

{{Form::label('almacenorigen', 'Almacén Origen')}}
<select name='almacenorigen'>
  @foreach ($almacenes as $almacen)
  <option value='{{$almacen->id}}'>{{$almacen->nombre}}</option>
  @endforeach
</select> <br>

{{Form::label('almacendestino', 'Almacén Destino')}}
<select name='almacendestino'>
  @foreach ($almacenes as $almacen)
  <option value='{{$almacen->id}}'>{{$almacen->nombre}}</option>
  @endforeach
</select> <br>

{{Form::label('fecha_descarga', 'Fecha de descarga:')}}
<input type='text' name='fecha_descarga' id='fecha_descarga'> <br>

{{Form::submit('Crear')}}
{{ Form::close() }}
  
</div>




  
@stop