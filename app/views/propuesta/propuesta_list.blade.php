@extends('layout.main_layout')

@section('content')
  

<div class="container">
<h1>Propuestas de Alimentación</h1>
{{HTML::link('/propuesta/add', 'Nueva Propuesta', array('class' =>'btn btn-primary'))}}
<br><br>
 <div class="table-responsive">
  <table class="table table-striped table-bordered">
  	<thead>
  		<tr>
  		  <th class="text-center">Granja</th>
  		  <th class="text-center">Descripción</th>
  		  <th class="text-center">Desde</th>
        <th class="text-center">Hasta</th>
        <th class="text-center">Acciones</th>
      </tr>
  	</thead>

  	<tbody>
  		@foreach($propuestas as $propuesta)
  		  <tr>
  		    <td class="text-left">{{$propuesta->granja}}</td>
  		    <td class="text-left">{{$propuesta->descripcion}} </td>
  		    <td class="text-center">{{date("d-m-Y",strtotime($propuesta->fecha_ini))}}</td>
  		    <td class="text-center">{{date("d-m-Y",strtotime($propuesta->fecha_fin))}}</td>
  		    <td class="text-center">
  		        {{Html::link('propuesta/ver/'. $propuesta->id, 'Ver', array('class' =>'btn btn-primary btn-sm'))}}
          </td>
  		  </tr>
  		@endforeach
  	</tbody>
  </table>

 </div>
 {{HTML::link('/propuesta/add', 'Nueva Propuesta', array('class' =>'btn btn-primary'))}}
</div>




  
@stop