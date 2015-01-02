@extends('layout.main_layout')

@section('content')

   <h1>Nueva Propuesta de Alimentación</h1>
   {{Form::open(array('url' => 'propuesta/add', 'class' =>'form-horizontal'))}}
    

    <div class="control-group">
   {{Form::label('granja_id', 'Granja', array('class' => 'control-label'))}}
   <div class="controls">
   	<select name="granja_id" id="granja_id">
   		@foreach($granjas as $granja)
          <option value="{{$granja->id}}">{{$granja->nombre}}</option>
   		@endforeach
   	</select>
     
   </div>
  </div>

    <div class="control-group">
      {{Form::label('fecha_ini', 'Fecha Inicial:', array('class' => 'control-label'))}}
      <div class="controls">
      	<input type="text" name="fecha_ini" id="fecha_ini">
        
      </div>
    </div>
    <div class="control-group">
      {{Form::label('fecha_fin', 'Fecha Final:', array('class' => 'control-label'))}}
      <div class="controls">
        
        <input type="text" name="fecha_fin" id="fecha_fin">
      </div>
    </div>

    <div class="control-group">
      <div class="controls">
        {{Form::submit('Guardar', array('class' =>'btn'))}}
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