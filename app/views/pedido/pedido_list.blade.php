@extends('layout.main_layout')

@section('content')
  

<div class="container">
<h1>Pedidos pendientes de pagar</h1>
{{HTML::link('/pedido/add', 'Nuevo Pedido')}}
<table class="table table-hover">
  	<thead>
  		<tr>
  		  <th>Número Pedido</th>
  		  <th>Proveedor</th>
  		  <th>Fecha Pedido</th>
        <th>Fecha Carga</th>
        <th>Fecha Descarga</th>
        <th>Fecha Pago</th>
        <th>Importe</th>
  		  <th>Pagado</th>
  		  <th>Estado</th>
  		  <th>Acciones</th>
       
  	    </tr>
  	</thead>
  	<tbody>
  		@foreach($pedidos as $pedido)
  		  <tr>
  		    <td>{{$pedido->num_pedido}}</td>
  		    <td>{{$pedido->proveedor->nombre}} </td>
  		    <td>{{date("d-m-Y",strtotime($pedido->fecha_pedido))}}</td>
          <td>{{date("d-m-Y",strtotime($pedido->fecha_carga))}}</td>
  		    <td>{{date("d-m-Y",strtotime($pedido->fecha_descarga))}}</td>
          <td>{{date("d-m-Y",strtotime($pedido->fecha_pago))}}</td>
          <td>{{$pedido->importe}} €</td>
          <td><input type='checkbox' disabled @if ($pedido->pagado == 1) checked @endif ></td>
          <td>{{$pedido->estado}} </td>
  		    <td>
  		      {{Html::link('pedido/ver/'. $pedido->id, 'Ver', array('class' =>'btn btn-mini btn-primary'))}}
            @if ($pedido->estado <> 'Descargado')  
              {{Html::link('pedido/edit/'. $pedido->id, 'Editar', array('class' =>'btn btn-mini btn-primary'))}}
            @else
               {{Html::link('pedido/edit/'. $pedido->id, 'Editar', array('class' =>'btn btn-mini btn-primary'))}}
            @endif
  		      {{Html::link('pedido/delete/'. $pedido->id, 'Eliminar', array('class'=>'btn btn-mini btn-primary'))}}
          </td>
  		  </tr>
  		@endforeach
  	</tbody>

  </table>
  
</div>




  
@stop