@extends('layout.main_layout')

@section('content')
 <div class="container">
 	<h1>Stock de almacenes a {{$fecha}}</h1>
 	

 @foreach($stock_almacen as $stock)
<div class="table-responsive">
<h3>{{$stock['almacen']}}</h3>
 <table class="table table-striped table-bordered">
    <thead>
      <tr>
        <th class="text-center">Pienso</th>
        <th class="text-center">Cantidad</th>
      </tr>
    </thead>
    <tbody>
      @foreach($stock['stock'] as $detalle_stock)
        <tr>
          <td class="text-center">{{$detalle_stock['pienso']}}</td>
          <td class="text-right">{{$detalle_stock['cantidad']}} Kg.</td>
          
        </tr>
      @endforeach
    </tbody>
</table>
</div>

@endforeach
 </div>
@stop