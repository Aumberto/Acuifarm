@extends('layout.main_layout')

@section('content')


  <div class="container">
  	<h1>Estadillos para el día
      <form action="estadillos" id="formulario_estadillo">
      <input type="text" name="fecha_pedido" id="fecha_pedido" value="{{date("d-m-Y",strtotime($fecha))}}"> 
      </form>
      
    </h1>
    <div class="datos_reales">
      <h1>Procria</h1>
    <table>
      <tr>
        <td>Jaula</td>
        <td>Lote</td>
        <td>Num. Peces</td>
        <td>Peso Medio (gr.)</td>
        <td>Biomasa (Kg.)</td>
        <td>Pienso (Kg.)</td>
        <td>Num. Tomas</td>
        <td>Diámetro</td>
        <td>Cantidad</td>
      </tr>
     @foreach($datos as $dato)
        <tr>
          <td>{{$dato->jaula}}</td>
          <td>{{$dato->lote}}</td>
          <td>{{$dato->stock_count_ini}}</td>
          <td>{{$dato->stock_avg_ini}}</td>
          <td>{{$dato->stock_bio_ini}}</td>
          <td>{{$dato->cantidad_toma}}</td>
          <td><select @if($dato->cantidad_toma <= 0) disabled @endif >
                 @for ($i=0; $i<3; $i++)
                 <option value={{$i}} 
                   @if ($i == $dato->num_tomas) selected @endif >{{$i}}</option>
                 @endfor
              </select></td>
          <td>{{$dato->diametro_pienso}}</td>
          <td>{{$dato->cantidad}}</td>

        </tr>
     @endforeach
    </table>
    </div>
  </div>

  
  <script>
    $(function() 
    {
      $("#fecha_pedido").change(function()
      {
        $("#formulario_estadillo").submit();
      });
    
    });
  </script>
  

@stop