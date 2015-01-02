@extends('layout.main_layout')

@section('content')
  

<div class="container">

	<h1>Listado de Siembras</h1>
  <p>{{HTML::link('/siembras/add', 'Añadir Siembra')}} </p>
  <table class="table table-hover">
  	<thead>
  		<tr>
  		 <th>Granja</th>
  		 <th>Jaula</th>
  		 <th>Lote</th>
       <th>Tabla de Alimentación</th>
  		 <th>Número de peces</th>
  		 <th>Peso medio</th>
       <th>Biomasa</th>
       <th>Fecha de la siembra</th>
  	 </tr>
  	</thead>
  	<tbody>
  		@foreach($siembras as $siembra)
  		<tr>
  			<td>{{$siembra->granja->nombre}}</td>
  			<td>{{$siembra->jaula->nombre}}</td>
  			<td>{{$siembra->lote->nombre}}</td>
  			<td>{{$siembra->cabecerarangos->nombre}}</td>
        <td>{{$siembra->input_count}}</td>
        <td>{{$siembra->input_avg}}</td>
        <td>{{$siembra->input_bio}}</td>
        <td>{{date("d-m-Y",strtotime($siembra->fecha))}}</td>
  			<td>
  				{{Html::link('siembras/editar/'. $siembra->id, 'Modificar', array('class' =>'btn btn-mini btn-primary'))}}
          {{Html::link('siembras/eliminar/'. $siembra->id, 'Eliminar', array('class' =>'btn btn-mini btn-primary'))}}	
  			</td>
  		</tr>
  		@endforeach
  	</tbody>

  </table>
  

</div>




  
@stop