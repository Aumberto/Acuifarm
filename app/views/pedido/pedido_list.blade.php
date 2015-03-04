@extends('layout.main_layout')

@section('content')
  

<div class="container">
<h1>Pedidos pendientes de pagar</h1>
{{HTML::link('/pedido/add', 'Nuevo Pedido',  array('class' =>'btn btn-primary'))}}
<br><br>

@foreach($listado_pedidos as $listado_pedido)
<div class="table-responsive">
<h5>{{$listado_pedido['proveedor']}}</h5>
 <table class="table table-striped table-bordered">
    <thead>
      <tr>
        <th class="text-center">Número Pedido</th>
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
      @foreach($listado_pedido['pedidos'] as $detalle_pedido)
        <tr {{$detalle_pedido['clase']}}>
          <td>{{$detalle_pedido['num_pedido']}}</td>
          <td class="text-center">{{date("d-m-Y",strtotime($detalle_pedido['fecha_pedido']))}}</td>
          <td class="text-center">{{date("d-m-Y",strtotime($detalle_pedido['fecha_carga']))}}
             @if( (date("d-m-Y",strtotime($detalle_pedido['fecha_carga'])) <= date("d-m-Y")) and ($detalle_pedido['estado'] == 'En tránsito')) 
              <span class="glyphicon glyphicon-warning-sign"></span>
             @endif
          </td>
          <td class="text-center">{{date("d-m-Y",strtotime($detalle_pedido['fecha_descarga']))}}
             @if( (date("d-m-Y",strtotime($detalle_pedido['fecha_descarga'])) <= date("d-m-Y")) and ($detalle_pedido['estado'] == 'Pendiente de descarga')) 
              <span class="glyphicon glyphicon-warning-sign"></span>
             @endif
          </td>
          <td class="text-center">{{date("d-m-Y",strtotime($detalle_pedido['fecha_pago']))}}
            @if (date("d-m-Y",strtotime($detalle_pedido['fecha_pago'])) <= date("d-m-Y"))
            <span class="glyphicon glyphicon-warning-sign"></span>
            @endif
          </td>
          <td class="text-right">{{$detalle_pedido['importe']}} €</td>
          <td class="text-center"><input type='checkbox' disabled @if ($detalle_pedido['pagado'] == 1) checked @endif ></td>
          <td class="text-center">{{$detalle_pedido['estado']}} </td>
          <td class="text-center">
            {{Html::link('pedido/ver/'. $detalle_pedido['id'], 'Ver', array('class' =>'btn btn-primary btn-sm'))}}
            @if ($detalle_pedido['estado'] <> 'Descargado')  
              {{Html::link('pedido/edit/'. $detalle_pedido['id'], 'Editar', array('class' =>'btn btn-primary btn-sm'))}}
            @else
               {{Html::link('pedido/edit/'. $detalle_pedido['id'], 'Editar', array('class' =>'btn btn-primary btn-sm'))}}
            @endif
            {{Html::link('pedido/delete/'. $detalle_pedido['id'], 'Eliminar', array('class'=>'btn btn-primary btn-sm'))}}
          </td>
        </tr>
      @endforeach
    </tbody>
</table>
</div>

@endforeach
  
{{HTML::link('/pedido/add', 'Nuevo Pedido',  array('class' =>'btn btn-primary'))}}



</div>






  
@stop