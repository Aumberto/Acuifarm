@extends('layout.main_layout')

@section('content')
 <div class="container">
 	<h1>{{$fecha}} </h1>
 	<div class="table-responsive">
 	<table class="table table-striped table-bordered">
    <thead>
    <tr>
    	<th class="text-center">Almacén</th>
    	<th class="text-center">Pienso</th>
    	<th class="text-center">Cantidad</th>
    </tr>
    </thead>
    <tbody>
    @foreach($estado_almacen as $estado)
    <tr>
    <td class="text-center">{{$estado->almacen}}</td>
    <td class="text-center">{{$estado->pienso}}</td>
    <td class="text-center">{{$estado->cantidad}}</td>
    </tr>
    @endforeach
    <tbody>
 	</table>
 </div>
 </div>
@stop