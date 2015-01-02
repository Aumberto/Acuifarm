@extends('layout.main_layout')

@section('content')
<div class='contenedor'>
  {{ Form::open(array('url' => 'consumo/almacenes', 'method' => 'post')) }}
  
    <label for='id_almacen'>Almacén</label>
    <select name='id_almacen' id='id_almaccen'>
      @foreach ($almacenes as $almacen)
         <option value='{{$almacen->id}}'>{{$almacen->nombre}}</option>
      @endforeach
    </select>
    <input type='submit' value='Ver'>
  {{ Form::close() }}
	<div class='datos_reales2'>
<table border='1'>
		<tr>
        
        <td colspan="2" >{{$granja->nombre}}</td>
        @foreach($semanas as $semana)
         <td colspan="4" align='center'>{{$semana}} </td>
        @endforeach
		</tr>
    <tr>
      <td colspan="2" ></td>
      @for($j=0; $j<8; $j++)
      <td>Consumo</td>
      <td>Entradas</td>
      <td>Stock Final</td>
      <td>Pedido</td>
      @endfor
    </tr>
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
		
	</table>
	</div>
	</div>
@stop