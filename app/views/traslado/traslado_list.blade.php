@extends('layout.main_layout')

@section('content')
  

<div class="container">
<h1>Traslados</h1>
{{HTML::link('/traslado/add', 'Nuevo Traslado', array('class' =>'btn btn-primary'))}}
<br><br>
<table class="table table-striped table-bordered">
  	<thead>
  		<tr>
  		  <th class="text-center">Traslado</th>
  		  <th class="text-center">Almacén Origen</th>
  		  <th class="text-center">Almacén Destino</th>
        <th class="text-center">Fecha Descarga</th>
        <th class="text-center">Estado</th>
  		  <th class="text-center">Acciones</th>
      </tr>
  	</thead>
  	<tbody>
  		@foreach($traslados as $traslado)
  		  <tr>
  		    <td class="text-left">{{$traslado->nombre}}</td>
  		    <td class="text-left">{{$traslado->almacenorigen->nombre}}</td>
          <td class="text-left">{{$traslado->almacendestino->nombre}}</td>
  		    <td class="text-center">{{date("d-m-Y",strtotime($traslado->fecha_traslado))}}</td>
          <td class="text-center">{{$traslado->estado}} </td>
  		    <td class="text-center">
  		      {{Html::link('traslado/ver/'. $traslado->id, 'Ver', array('class' =>'btn btn-primary btn-sm'))}}
            @if ($traslado->estado <> 'Descargado')  
              {{Html::link('traslado/entrada/'. $traslado->id, 'Dar entrada', array('class' =>'btn btn-primary btn-sm'))}}
            @else
               
            @endif
  		      {{Html::link('traslado/delete/'. $traslado->id, 'Eliminar', array('class'=>'btn btn-primary btn-sm'))}}
            </td>
  		  </tr>
  		@endforeach
  	</tbody>

  </table>
  {{HTML::link('/traslado/add', 'Nuevo Traslado', array('class' =>'btn btn-primary'))}}
</div>




  
@stop