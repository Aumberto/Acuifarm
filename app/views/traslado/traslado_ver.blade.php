@extends('layout.main_layout')

@section('content')
<div class="container">
  <h1>Traslado: <b>{{$traslado->nombre}}</b></h1>
  <p>Almacén Origen: <b>{{$traslado->almacenorigen->nombre}}</b></p>
  <p>Almacén Destino:<b>{{$traslado->almacendestino->nombre}}</b></p>

<table class="table table-hover">
  	<thead>
  		<tr>
  	     <th>Código</th>
  		   <th>Pienso</th>
  		   <th>Cantidad</th>
  		   <th>Acciones</th>
      </tr>
  	</thead>
  	<tbody>
  		@foreach($detalles as $detalle)
  		<tr>
  			 <td>{{$detalle->codigopienso}}</td>
  			 <td>{{$detalle->pienso}}</td>
  			 <td>{{$detalle->cantidad}}</td>
  			 <td>
  			   {{HTML::link('/trasladodetalle/delete/'. $detalle->id, 'Eliminar', array('class' =>'btn btn-mini btn-primary'))}}	
         </td>
  		</tr>
  		@endforeach
  		<tr>
  			<td></td>
  			<td><b>Total</b></td>
  			<td><b>{{$trasladototalcantidad}} Kg</b></td>
  			<td></td>
  			<td></td>
  		</tr>
  	</tbody>

  </table>




{{Form::open(array('url' => 'trasladodetalle/add', 'class' =>'form-inline'))}}
     <input type="hidden" name="traslado_id" value="{{$traslado->id}}" />  

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
{{HTML::link('/traslado', 'Volver')}}
</div>


@stop