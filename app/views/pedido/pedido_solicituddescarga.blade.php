@extends('layout.main_layout')

@section('content')
  

<div class="container">
<form action="../pedido/solicituddescarga" id="formulario_solicitudpago" class="form-inline">
        <div class="form-group"> 
          <label for="semana_id" class="input-lg">Pedidos a descargar durante la semana</label>
         
          <select class="form-control" name ="semana_id" id="semana_id">
              @foreach($listado_semanas as $listado_semana)
                <option value='{{$listado_semana->id}}'@if($listado_semana->id == $semana->id) selected @endif>{{$listado_semana->week}}</option>
              @endforeach
              
          </select>
        </div>
        <div class="form-group"> 
         <label class="input-lg"> (Del {{date("d-m-Y",strtotime($semana->first_day))}} al {{date("d-m-Y",strtotime($semana->last_day))}})</label>
        </div>
</form>

<br><br>

@foreach($listado_pedidos as $listado_pedido)
<div class="table-responsive">
<h5>{{$listado_pedido['proveedor']}}</h5>
 <table class="table table-striped table-bordered">
    <thead>
      <tr>
        <th class="text-center">Número Pedido</th>
        <th class="text-center">Número de Contenedor</th>
        <th class="text-center">Fecha llegada al muelle</th>
        <th class="text-center">Fecha de Descarga</th>
        
        
        
        
      </tr>
    </thead>
    <tbody>
      @foreach($listado_pedido['pedidos'] as $detalle_pedido)
        <tr>
          <td>{{$detalle_pedido['num_pedido']}}</td>
          <td class="text-center">{{$detalle_pedido['num_contenedor']}}</td>
          <td class="text-center">{{date("d-m-Y",strtotime($detalle_pedido['fecha_llegada']))}}</td>
          <td class="text-center">{{date("d-m-Y",strtotime($detalle_pedido['fecha_descarga']))}}</td>
          
          
          
        </tr>
      @endforeach
    </tbody>
</table>
</div>

@endforeach
  




</div>

<script>
    $(function() 
    {
      $("#semana_id").change(function()
      {
        $("#formulario_solicitudpago").submit();
      });

      
    
    });
  </script>






  
@stop