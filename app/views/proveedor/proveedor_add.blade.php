@extends('layout.main_layout')

@section('content')
<div class="container">
	
<h1>Nuevo Proveedor</h1>
<p></p>
{{Form::open(array('url' => 'proveedor/add', 'class' =>'form-horizontal'))}}
  <div class="control-group">
   {{Form::label('nombre', 'Nombre', array('class' => 'control-label'))}}
   <div class="controls">
     {{Form::text('nombre')}}
   </div>
  </div>

  <div class="control-group">
   {{Form::label('direccion', 'Dirección', array('class' => 'control-label'))}}
   <div class="controls">
     {{Form::text('direccion')}}
   </div>
  </div>

  <div class="control-group">
   {{Form::label('localidad', 'Ciudad', array('class' => 'control-label'))}}
   <div class="controls">
     {{Form::text('localidad')}}
   </div>
  </div>

  <div class="control-group">
   {{Form::label('cp', 'Código Postal', array('class' => 'control-label'))}}
   <div class="controls">
     {{Form::text('cp')}}
   </div>
  </div>

  <div class="control-group">
   {{Form::label('pais', 'País', array('class' => 'control-label'))}}
   <div class="controls">
     {{Form::text('pais')}}
   </div>
  </div>

  <div class="control-group">
   {{Form::label('telefono', 'Teléfono', array('class' => 'control-label'))}}
   <div class="controls">
     {{Form::text('telefono')}}
   </div>
  </div>

  <div class="control-group">
   {{Form::label('fax', 'Fax', array('class' => 'control-label'))}}
   <div class="controls">
     {{Form::text('fax')}}
   </div>
  </div>

  <div class="control-group">
   {{Form::label('email', 'Correo Electrónico', array('class' => 'control-label'))}}
   <div class="controls">
     {{Form::text('email')}}
   </div>
  </div>

  <div class="control-group">
   {{Form::label('web', 'Página Web', array('class' => 'control-label'))}}
   <div class="controls">
     {{Form::text('web')}}
   </div>
  </div>

  <div class="control-group">
   <div class="controls">
     {{Form::submit('Guardar', array('class' =>'btn'))}}
   </div>
  </div>


{{Form::close()}}

</div>


@stop