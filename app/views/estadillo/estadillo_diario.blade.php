@extends('layout.main_layout')

@section('content')


  <div class="container">
  	
      <form action="estadillos" id="formulario_estadillo" class="form-horizontal">
        <div class="form-group"> 
         {{Form::label('fecha_pedido', 'Estadillos del día ', array('class' => 'col-sm-2 control-label'))}} 
          <div class="col-sm-2">
            <input type="text" class="form-control input-sm" name="fecha_pedido" id="fecha_pedido" value="{{date("d-m-Y",strtotime($fecha))}}"> 
          </div>
        </div>
      </form>
      
    
    <div class="table-responsive">
      <h5>Procria</h5>
    <table class="table table-striped table-bordered">
      <thead>
       <tr>
        <th class="text-center">Jaula</th>
        <th class="text-center">Lote</th>
        <th class="text-center">Num. Peces</th>
        <th class="text-center">Peso Medio (gr.)</thd>
        <th class="text-center">Biomasa (Kg.)</th>
        <th class="text-center">Pienso (Kg.)</th>
        <th class="text-center">Num. Tomas</th>
        <th class="text-center">Diámetro</th>
        <th class="text-center">Cantidad</th>
       </tr>
      </thead>
      <tbody>
     @foreach($datos as $dato)
        <tr>
          <td class="text-center">{{$dato->jaula}}</td>
          <td class="text-center">{{$dato->lote}}</td>
          <td class="text-right">{{$dato->stock_count_ini}}</td>
          <td class="text-right">{{$dato->stock_avg_ini}}</td>
          <td class="text-right">{{$dato->stock_bio_ini}}</td>
          <td class="text-right">{{$dato->cantidad_toma}}</td>
          <td class="text-center"><select @if($dato->cantidad_toma <= 0) disabled @endif >
                 @for ($i=0; $i<3; $i++)
                 <option value={{$i}} 
                   @if ($i == $dato->num_tomas) selected @endif >{{$i}}</option>
                 @endfor
              </select></td>
          <td class="text-center">{{$dato->diametro_pienso}}</td>
          <td class="text-right">{{$dato->cantidad}}</td>

        </tr>
     @endforeach
   </tbody>
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