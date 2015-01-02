@extends('layout.main_layout')

@section('content')
<div class="container">
  <h1>Pedido {{$pedido->num_pedido}} </h1>
  <p>Proveedor {{$proveedor->nombre}} </p>

<table class="table table-hover">
  	<thead>
  		<tr>
  	     <th>Código</th>
  		   <th>Pienso</th>
  		   <th>Cantidad</th>
  		   <th>Precio</th>
         <th>Total</th>
       	 <th>Acciones</th>
      </tr>
  	</thead>
  	<tbody>
  		@foreach($detalles as $detalle)
  		<tr>
  			 <td>{{$detalle->codigopienso}}</td>
  			 <td>{{$detalle->pienso}}</td>
  			 <td>{{$detalle->cantidad}}</td>
  			 <td>{{$detalle->precio}} €</td>
         <td>{{$detalle->total}} €</td>
  			 <td>
  			   {{HTML::link('/pedidodetalle/delete/'. $detalle->id, 'Eliminar', array('class' =>'btn btn-mini btn-primary'))}}	
         </td>
  		</tr>
  		@endforeach
  		<tr>
  			<td></td>
  			<td><b>Total</b></td>
  			<td><b>{{$pedidototalcantidad}} Kg</b></td>
  			<td></td>
  			<td><b>{{$pedidototal}} €</b></td>

  		</tr>
  	</tbody>

  </table>




{{Form::open(array('url' => 'pedidodetalle/add', 'class' =>'form-inline'))}}
     <input type="hidden" name="pedido_id" value="{{$pedido->id}}" />  

     {{Form::label('pienso_id', 'Pienso', array('class' => 'control-label'))}}
      <select name="pienso_id" id="pienso_id">
   	    @foreach($piensos as $pienso)
          <option value="{{$pienso->id}}">{{$pienso->nombre}}</option>
   	    @endforeach
   	  </select>

    {{Form::label('cantidad', 'Kilos:', array('class' => 'control-label'))}}
    {{Form::text('cantidad')}}
    {{Form::submit('Añadir', array('class' =>'btn'))}}
 
{{Form::close()}}
{{HTML::link('/pedido', 'Volver')}}
</div>


@stop