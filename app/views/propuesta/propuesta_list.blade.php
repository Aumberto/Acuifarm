@extends('layout.main_layout')

@section('content')
  

<div class="container">
<h1>Propuestas de Alimentación</h1>
{{HTML::link('/propuesta/add', 'Nueva Propuesta')}}
<table class="table table-hover">
  	<thead>
  		<tr>
  		  <th>Granja</th>
  		  <th>Descripción</th>
  		  <th>Desde</th>
        <th>Hasta</th>
      </tr>
  	</thead>
  	<tbody>
  		@foreach($propuestas as $propuesta)
  		  <tr>
  		    <td>{{$propuesta->granja}}</td>
  		    <td>{{$propuesta->descripcion}} </td>
  		    <td>{{date("d-m-Y",strtotime($propuesta->fecha_ini))}}</td>
  		    <td>{{date("d-m-Y",strtotime($propuesta->fecha_fin))}}</td>
  		    <td>
  		        {{Html::link('propuesta/ver/'. $propuesta->id, 'Ver', array('class' =>'btn btn-mini btn-primary'))}}
          </td>
  		  </tr>
  		@endforeach
  	</tbody>

  </table>
  
</div>




  
@stop