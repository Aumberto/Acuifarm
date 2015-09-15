@extends('layout.main_layout')

@section('content')
  

<div class="container">
<h1>{{$cabecera_rango->nombre}}</h1>
<br><br>
<table class="table table-striped table-bordered">
    <thead>
      <tr>
        <th class="text-center">Diámetro de Pellet</th>
        <th class="text-center">Pm. Inicial</th>
        <th class="text-center">Pm. Tránsito</th>
        <th class="text-center">Pm. Final</th>
        <th class="text-center">Acciones</th>
      </tr>
    </thead>
    <tbody>
      @foreach($detalle_rango as $detalle)
        <tr>
          <td class="text-left">{{$detalle->pellet->diametro}} ({{$detalle->pellet->proveedor->nombre}})</td>
          <td class="text-left">{{$detalle->pm_min}}</td>
          <td class="text-left">{{$detalle->pm_transito}}</td>
          <td class="text-left">{{$detalle->pm_max}}</td>
          <td class="text-center">
            {{HTML::link('rangos/detalle/delete/'. $detalle->id, 'Eliminar', array('class'=>'btn btn-primary btn-sm'))}}
            </td>
        </tr>
      @endforeach
    </tbody>

  </table>
</div>

{{Form::open(array('url' => 'rangos/detalle/add', 'class' =>'form-inline'))}}
  <input type="hidden" name="rango_id" value="{{$cabecera_rango->id}}" />  
   <div class="form-group">
    {{Form::label('tamanio_pellet_id', 'Pellet', array('class' => 'control-label input-sm'))}}
      <select name="tamanio_pellet_id" id="tamanio_pellet_id" class="form-control input-sm">
      @foreach($pellets as $pellet)
       <option value="{{$pellet->id}}">{{$pellet->diametro}} ({{$pellet->proveedor->nombre}})</option>
      @endforeach
      </select>
  </div>

  <div class="form-group">
    {{Form::label('pm_min', 'pm. min.:', array('class' => 'control-label input-sm'))}}
    {{Form::text('pm_min', '', array('class' => 'form-control input-sm'))}}
  </div>
  <div class="form-group">
    {{Form::label('pm_transito', 'pm. trans.:', array('class' => 'control-label input-sm'))}}
    {{Form::text('pm_transito', '', array('class' => 'form-control input-sm'))}}
  </div>
  <div class="form-group">
    {{Form::label('pm_max', 'pm. máx.:', array('class' => 'control-label input-sm'))}}
    {{Form::text('pm_max', '', array('class' => 'form-control input-sm'))}}
  </div>
  <div class="form-group">
    {{Form::submit('Añadir', array('class' =>'btn btn-primary btn-sm'))}}
    {{Form::close()}}
  </div>
  
  
<br>
{{HTML::link('/rangos', 'Volver',  array('class' =>'btn btn-primary'))}}
</div>


  
@stop