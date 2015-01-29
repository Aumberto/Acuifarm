@extends('layout.main_layout')

@section('content')
  

<div class="container">
<h1>Pedidos pendientes de pagar</h1>
{{HTML::link('/pedido/add', 'Nuevo Pedido',  array('class' =>'btn btn-primary'))}}
<br><br>
<div class="table-responsive">
<table class="table table-striped table-bordered">
  	<thead>
  		<tr>
  		  <th class="text-center">Número Pedido</th>
  		  <th class="text-center">Proveedor</th>
  		  <th class="text-center">Fecha Pedido</th>
        <th class="text-center">Fecha Carga</th>
        <th class="text-center">Fecha Descarga</th>
        <th class="text-center">Fecha Pago</th>
        <th class="text-center">Importe</th>
  		  <th class="text-center">Pagado</th>
  		  <th class="text-center">Estado</th>
  		  <th class="text-center">Acciones</th>
       
  	    </tr>
  	</thead>
  	<tbody>
  		@foreach($pedidos as $pedido)
  		  <tr>
  		    <td>{{$pedido->num_pedido}}</td>
  		    <td>{{$pedido->proveedor->nombre}} </td>
  		    <td class="text-center">{{date("d-m-Y",strtotime($pedido->fecha_pedido))}}</td>
          <td class="text-center">{{date("d-m-Y",strtotime($pedido->fecha_carga))}}</td>
  		    <td class="text-center">{{date("d-m-Y",strtotime($pedido->fecha_descarga))}}</td>
          <td class="text-center">{{date("d-m-Y",strtotime($pedido->fecha_pago))}}</td>
          <td class="text-right">{{$pedido->importe}} €</td>
          <td class="text-center"><input type='checkbox' disabled @if ($pedido->pagado == 1) checked @endif ></td>
          <td class="text-center">{{$pedido->estado}} </td>
  		    <td class="text-center">
  		      {{Html::link('pedido/ver/'. $pedido->id, 'Ver', array('class' =>'btn btn-primary btn-sm'))}}
            @if ($pedido->estado <> 'Descargado')  
              {{Html::link('pedido/edit/'. $pedido->id, 'Editar', array('class' =>'btn btn-primary btn-sm'))}}
            @else
               {{Html::link('pedido/edit/'. $pedido->id, 'Editar', array('class' =>'btn btn-primary btn-sm'))}}
            @endif
  		      {{Html::link('pedido/delete/'. $pedido->id, 'Eliminar', array('class'=>'btn btn-primary btn-sm'))}}
          </td>
  		  </tr>
  		@endforeach
  	</tbody>

  </table>
  </div>
  
  {{HTML::link('/pedido/add', 'Nuevo Pedido',  array('class' =>'btn btn-primary'))}}
</div>




  
@stop