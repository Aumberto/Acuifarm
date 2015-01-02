@extends('layout.main_layout')

@section('content')
  

<div class="container">

	<h1>Listado de Semanas</h1>
  <p>{{HTML::link('/semana/add', 'Añadir Semana')}} </p>
  <table class="table table-hover">
  	<thead>
  		<tr>
  		 <th>Año</th>
  		 <th>Semana</th>
  		 <th>Lunes</th>
  		 <th>Domingo</th>
  		 <th>Acciones</th>
  	    </tr>
  	</thead>
  	<tbody>
  		@foreach($semanas as $semana)
  		<tr>
  			<td>{{$semana->year}}</td>
  			<td>{{$semana->week}}</td>
  			<td>{{$semana->first_day}}</td>
  			<td>{{$semana->last_day}}</td>
  			<td>
  				<button class="btn btn-mini btn-primary" type="button">Ver </button>
  				<button class="btn btn-mini btn-primary" type="button">Editar</button>
  				
  			</td>
  		</tr>
  		@endforeach
  	</tbody>

  </table>
  {{ $semanas->links()}}

</div>




  
@stop