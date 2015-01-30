@extends('layout.main_layout')

@section('content')
<div class="container">
  <h2><b>{{$traslado->nombre}}</b></h2>
  <h4>De: <b>{{$traslado->almacenorigen->nombre}} </b> a: <b> {{$traslado->almacendestino->nombre}}</b></h4>

<table class="table table-striped table-bordered">
  	<thead>
  		<tr>
  	     <th class="text-center">Código</th>
  		   <th class="text-center">Pienso</th>
  		   <th class="text-center">Cantidad</th>
  		   <th class="text-center">Acciones</th>
      </tr>
  	</thead>
  	<tbody>
  		@foreach($detalles as $detalle)
  		<tr>
  			 <td class="text-left">{{$detalle->codigopienso}}</td>
  			 <td class="text-left">{{$detalle->pienso}}</td>
  			 <td class="text-right">{{$detalle->cantidad}} Kg.</td>
  			 <td class="text-center">
  			   {{HTML::link('/trasladodetalle/delete/'. $detalle->id, 'Eliminar', array('class' =>'btn btn-primary btn-sm'))}}	
         </td>
  		</tr>
  		@endforeach
  		<tr>
  			<td colspan="2" class="text-right"><b>Total:</b></td>
  			<td class="text-right"><b>{{$trasladototalcantidad}} Kg</b></td>
  			<td></td>
  			
  		</tr>
  	</tbody>

  </table>




{{Form::open(array('url' => 'trasladodetalle/add', 'class' =>'form-inline'))}}
   <input type="hidden" name="traslado_id" value="{{$traslado->id}}" />  
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
{{HTML::link('/traslado', 'Volver', array('class' =>'btn btn-primary'))}}
</div>


@stop