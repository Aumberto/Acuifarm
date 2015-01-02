@extends('layout.main_layout')

@section('content')
 <div class='contenedor'>
 	<h1>{{$fecha}} </h1>
 	<div class='datos_reales'>
 	<table>
    <tr>
    	<td>Almacén</td>
    	<td>Pienso</td>
    	<td>Cantidad</td>
    </tr>
    @foreach($estado_almacen as $estado)
    <tr>
    <td>{{$estado->almacen}}</td>
    <td>{{$estado->pienso}}</td>
    <td>{{$estado->cantidad}}</td>
    </tr>
    @endforeach
 	</table>
 </div>
 </div>
@stop