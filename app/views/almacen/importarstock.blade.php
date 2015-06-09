@extends('layout.main_layout')

@section('content')
<div class="container">
  
  <h2>Importación Automática de Stock de pienso</h1>
  {{Form::open(array('url' => 'almacenes/importarstock', 'files' => true , 'class' =>'form-horizontal'))}}
    <div class="form-group">
      {{Form::label('fecha_fichero', 'Fecha:', array('class' => 'col-sm-3 control-label'))}}
      <div class="col-sm-3">
        <input type="text" name="fecha_fichero" id="fecha_fichero" class='form-control input-sm'>
      </div>
    </div>

    <div class="form-group">
      {{Form::label('granja', 'Granja:', array('class' => 'col-sm-3 control-label'))}}
      <div class="col-sm-3">
        <select  class="form-control input-sm" name="granja" id="granja">
           <option value="Melenara">Melenara</option>
           <option value="Procria">Procria</option>
    
        </select>
      </div>
    </div>

    <div class="form-group">
      {{Form::label('fichero', 'Archivo:', array('class' => 'col-sm-3 control-label'))}}
      <div class="col-sm-3">
        {{Form::file('fichero', array('class' =>'btn btn-primary'))}}
      </div>
    </div>

    <div class="form-group">
      <div class="row">
        <div class="col-md-6 col-md-offset-3">
            {{Form::submit('Enviar', array('class' =>'btn btn-primary'))}}
            {{HTML::link('/almacenes/stock', 'Cancelar',  array('class' =>'btn btn-primary'))}}
        </div>
      </div>
    </div>


  {{ Form::close() }}

</div>
@stop