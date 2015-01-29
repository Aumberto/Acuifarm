@extends('layout.main_layout')

@section('content')
<div class="container">
   <h1>Nueva Propuesta de Alimentación</h1>
   {{Form::open(array('url' => 'propuesta/add', 'class' =>'form-horizontal'))}}
    

    <div class="form-group">
     {{Form::label('granja_id', 'Granja', array('class' => 'col-sm-3 control-label'))}}
     <div class="col-sm-2">
   	   <select name="granja_id" id="granja_id" class="form-control input-sm">
   		   @foreach($granjas as $granja)
           <option value="{{$granja->id}}">{{$granja->nombre}}</option>
   		   @endforeach
   	   </select>
     </div>
    </div>

    <div class="form-group">
      {{Form::label('fecha_ini', 'Fecha Inicial:', array('class' => 'col-sm-3 control-label'))}}
      <div class="col-sm-2">
      	<input type="text" name="fecha_ini" id="fecha_ini" class="form-control input-sm">
      </div>
    </div>
    
    <div class="form-group">
      {{Form::label('fecha_fin', 'Fecha Final:', array('class' => 'col-sm-3 control-label'))}}
      <div class="col-sm-2">
        <input type="text" name="fecha_fin" id="fecha_fin" class="form-control input-sm">
      </div>
    </div>

    <div class="form-group">
      <div class="row">
        <div class="col-md-6 col-md-offset-3">
        {{Form::submit('Guardar', array('class' =>'btn btn-primary'))}}
        {{HTML::link('/propuesta', 'Cancelar',  array('class' =>'btn btn-primary'))}}
      </div>
    </div>
</div>


	{{ Form::close() }}

	<script>
     $(function(){
     
       	$("#fecha_ini").datepicker();
       	$("#fecha_fin").datepicker();

       	
      
      });
	</script> 
   
@stop