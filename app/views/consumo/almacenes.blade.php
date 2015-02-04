@extends('layout.main_layout')

@section('content')
<div class='container'>
  {{ Form::open(array('url' => 'consumo/almacenes', 'method' => 'post')) }}
  
    <label for='id_almacen'>Almacén</label>
    <select name='id_almacen' id='id_almaccen'>
      @foreach ($almacenes as $almacen)
         <option value='{{$almacen->id}}'>{{$almacen->nombre}}</option>
      @endforeach
    </select>
    <input type='submit' value='Ver'>
  {{ Form::close() }}
	
<table class="table table-striped table-bordered table-condensed">
		
    <thead>
      <tr>
        
        <th colspan="2" class="text-center">{{$granja->nombre}}</th>
        @foreach($semanas as $semana)
         <th colspan="4" class="text-center">{{$semana}} </th>
        @endforeach
		</tr>
  
    <tr>
      <th colspan="2" ></th>
      @for($j=0; $j<8; $j++)
      <th class="text-center">Consumo</th>
      <th class="text-center">Entradas</th>
      <th class="text-center">Stock Final</th>
      <th class="text-center">Pedido</th>
      @endfor
    </tr>
    </thead>
    <tbody>
		@foreach($datos as $fila)
  		<tr>
  			@for($i=1; $i <= count($fila); $i++)
              @if ($fila[$i] < 0)
                <td><b>{{$fila[$i]}}</b></td>
              @else
                <td>{{$fila[$i]}}</td>
              @endif
              
  			@endfor
  			 
  	     </tr>
  		@endforeach
		</tbody>
	</table>
	
	</div>
@stop