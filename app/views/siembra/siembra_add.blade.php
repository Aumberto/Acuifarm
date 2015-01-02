@extends('layout.main_layout')

@section('content')
  

<div class="container">

	<h1>Nueva Siembra</h1>
  
  {{Form::open(array('url' => 'siembras/add', 'class' =>'form-horizontal'))}}
  <p>
  <label for="granja_id">Granja: </label>
  <select name="granja_id" id="granja_id">
    <option value="0">Granja</option>
    @foreach($granjas as $granja)
       <option value="{{$granja->id}}">{{$granja->nombre}}</option>
    @endforeach
  </select>
  </p>

  <p>
  <label for="jaula_id">Jaula: </label>
  <select name="jaula_id" id="jaula_id">
    <option value="0">Jaula</option>
    @foreach($jaulas as $jaula)
     <option value="{{$jaula->id}}">{{$jaula->nombre}}</option>
    @endforeach
  </select>
  </p>
  
  <p>
  <label for="lote_id">Lote:</label>
  <select name="lote_id" id="lote_id">
    <option value="0">Lote</option>
    @foreach($lotes as $lote)
     <option value="{{$lote->id}}">{{$lote->nombre}}</option>
    @endforeach
  </select>
  </p>

  <p>
  <label for="tabla_alimentacion_id">Tabla de Alimentación</label>
  <select name="tabla_alimentacion_id" id="tabla_alimentacion_id">
    <option value="0">Tabla de alimentación</option>
    @foreach($tablas_alimentacion as $tabla_alimentacion)
     <option value="{{$tabla_alimentacion->id}}">{{$tabla_alimentacion->nombre}}</option>
    @endforeach
  </select>
  </p>

  <p>
  <label for="input_count">Número de peces:</label>
  <input type="text" name="input_count" id="input_count">
  </p>

  <p>
  <label for="input_avg">Peso medio:</label>
  <input type="text" name="input_avg" id="input_avg">
  </p>

  <p>
  <label for="input_bio">Biomasa:</label>
  <input type="text" name="input_bio" id="input_bio">
  </p>

  <p>
  <label for="fecha_siembra">Fecha:</label>
  <input type="text" name="fecha_siembra" id="fecha_siembra">
  </p>


  {{Form::submit('Guardar', array('class' =>'btn'))}}
  {{Form::close()}}


</div>




  
@stop