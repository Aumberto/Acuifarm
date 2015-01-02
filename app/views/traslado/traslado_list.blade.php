@extends('layout.main_layout')

@section('content')
  

<div class="container">
<h1>Traslados</h1>
{{HTML::link('/traslado/add', 'Nuevo Traslado')}}
<table class="table table-hover">
  	<thead>
  		<tr>
  		  <th>Traslado</th>
  		  <th>Almacén Origen</th>
  		  <th>Almacén Destino</th>
        <th>Fecha Descarga</th>
        <th>Estado</th>
  		  <th>Acciones</th>
      </tr>
  	</thead>
  	<tbody>
  		@foreach($traslados as $traslado)
  		  <tr>
  		    <td>{{$traslado->nombre}}</td>
  		    <td>{{$traslado->almacenorigen->nombre}}</td>
          <td>{{$traslado->almacendestino->nombre}}</td>
  		    <td>{{date("d-m-Y",strtotime($traslado->fecha_traslado))}}</td>
          <td>{{$traslado->estado}} </td>
  		    <td>
  		      {{Html::link('traslado/ver/'. $traslado->id, 'Ver', array('class' =>'btn btn-mini btn-primary'))}}
            @if ($traslado->estado <> 'Descargado')  
              {{Html::link('traslado/entrada/'. $traslado->id, 'Dar entrada', array('class' =>'btn btn-mini btn-primary'))}}
            @else
               
            @endif
  		      {{Html::link('traslado/delete/'. $traslado->id, 'Eliminar', array('class'=>'btn btn-mini btn-primary'))}}
            </td>
  		  </tr>
  		@endforeach
  	</tbody>

  </table>
  
</div>




  
@stop