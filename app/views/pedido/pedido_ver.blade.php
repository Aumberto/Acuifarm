@extends('layout.main_layout')

@section('content')
<div class="container">
  <h2>{{$pedido->num_pedido}}</h1>
  <h4>{{$proveedor->nombre}}</h4> 
<div class="table-responsive">
<table class="table table-striped table-bordered">
  	<thead>
  		<tr>
  	     <th class="text-center">Código</th>
  		   <th class="text-center">Pienso</th>
  		   <th class="text-center">Cantidad</th>
  		   <th class="text-center">Precio</th>
         <th class="text-center">Total</th>
       	 <th class="text-center">Acciones</th>
      </tr>
  	</thead>
  	<tbody>
  		@foreach($detalles as $detalle)
  		<tr>
  			 <td class="text-center">{{$detalle->codigopienso}}</td>
  			 <td class="text-left">{{$detalle->pienso}}</td>
  			 <td class="text-right">{{$detalle->cantidad}} Kg.</td>
  			 <td class="text-right">{{$detalle->precio}} €</td>
         <td class="text-right">{{$detalle->total}} €</td>
  			 <td class="text-center">
  			   {{HTML::link('/pedidodetalle/delete/'. $detalle->id, 'Eliminar', array('class' =>'btn btn-primary btn-sm'))}}	
         </td>
  		</tr>
  		@endforeach
  		<tr>
  			<td></td>
  			<td class="text-right"><b>Total:</b></td>
  			<td class="text-right"><b>{{$pedidototalcantidad}} Kg</b></td>
  			<td></td>
  			<td class="text-right"><b>{{$pedidototal}} €</b></td>

  		</tr>
  	</tbody>

  </table>
</div>



{{Form::open(array('url' => 'pedidodetalle/add', 'class' =>'form-inline'))}}
     <input type="hidden" name="pedido_id" value="{{$pedido->id}}" />  
    <div class="form-group">
     {{Form::label('pienso_id', 'Pienso', array('class' => 'control-label'))}}
      <select name="pienso_id" id="pienso_id" class="form-control">
   	    @foreach($piensos as $pienso)
          <option value="{{$pienso->id}}">{{$pienso->nombre}}</option>
   	    @endforeach
   	  </select>
    </div>

    <div class="form-group">
      {{Form::label('cantidad', 'Kilos:', array('class' => 'control-label'))}}
      {{Form::text('cantidad', '', array('class' => 'form-control'))}}
    </div>

    {{Form::submit('Añadir', array('class' =>'btn btn-primary'))}}
 
{{Form::close()}}
<br>
{{HTML::link('/pedido', 'Volver',  array('class' =>'btn btn-primary'))}}
</div>


@stop