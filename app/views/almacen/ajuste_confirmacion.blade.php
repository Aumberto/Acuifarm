@extends('layout.main_layout')

@section('content')
 <div class='container'>
 	<h1>{{$fecha}} </h1>
 	<div class="table-responsive">
 	<table class="table table-striped table-bordered">
    <thead>
     <tr>
    	<th class="text-center">Almacén</th>
    	<th class="text-center">Pienso</th>
    	<th class="text-center">Cantidad Fishtalk</th>
        <th class="text-center">Cantidad Acuifarm</th>
        <th class="text-center">Cantidad a ajustar</th>
     </tr>
    </thead>
    <tbody>
    @foreach($resultado as $resultados)
    <tr>
    <td class="text-left">{{$resultados->almacen->nombre}}</td>
    <td class="text-left">{{$resultados->pienso->nombre}}</td>
    <td class="text-right">{{$resultados->cantidad_fishtalk}}</td>
    <td class="text-right">{{$resultados->cantidad_acuifarm}}</td>
    <td class="text-right">{{$resultados->diferencia}}</td>
    <td class="text-center"> {{HTML::link('pedido/ver/'. $resultados->id, 'Ver', array('class' =>'btn btn-primary btn-sm'))}} </td>
    </tr>
    @endforeach
</tbody>
 	</table>
 </div>
 </div>
@stop