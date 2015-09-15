@extends('layout.main_layout')

@section('content')
  

<div class="container">
<h1>Rangos de alimentación</h1>
{{HTML::link('/rangos/add', 'Nuevo Rango', array('class' =>'btn btn-primary'))}}
<br><br>
<table class="table table-striped table-bordered">
    <thead>
      <tr>
        <th class="text-center">Nombre</th>
        <th class="text-center">Descripción</th>
        <th class="text-center">Acciones</th>
      </tr>
    </thead>
    <tbody>
      @foreach($cabecera_rangos as $cabecera_rango)
        <tr>
          <td class="text-left">{{$cabecera_rango->nombre}}</td>
          <td class="text-left">{{$cabecera_rango->descripcion}}</td>
          <td class="text-center">
            {{Html::link('rangos/cabecera/ver/'. $cabecera_rango->id, 'Ver', array('class' =>'btn btn-primary btn-sm'))}}
            {{Html::link('rangos/cabecera/delete/'. $cabecera_rango->id, 'Eliminar', array('class'=>'btn btn-primary btn-sm'))}}
            </td>
        </tr>
      @endforeach
    </tbody>

  </table>
  {{Form::open(array('url' => 'rangos/cabecera/add', 'class' =>'form-inline'))}}
   
  <div class="form-group">
    {{Form::label('rango_nombre', 'Rango:', array('class' => 'control-label input-sm'))}}
    {{Form::text('rango_nombre', '', array('class' => 'form-control input-sm'))}}
  </div>
  <div class="form-group">
    {{Form::label('rango_descripcion', 'Descripción:', array('class' => 'control-label input-sm'))}}
    {{Form::text('rango_descripcion', '', array('class' => 'form-control input-sm'))}}
  </div>
  
  <div class="form-group">
    {{Form::submit('Añadir', array('class' =>'btn btn-primary btn-sm'))}}
    {{Form::close()}}
  </div>
  
  
<br>

</div>
  
</div>




  
@stop