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
    @foreach($estadillos_granja as $estadillo_granja)
     <div class="table-responsive">
       <h5>{{$estadillo_granja['granja']}} <span class="glyphicon glyphicon-print" fecha="{{date("d-m-Y",strtotime($fecha))}}" granja="{{$estadillo_granja['granja']}}"><a href="/acuifarm/public/ajax/estadillos/excel/{{date("d-m-Y",strtotime($fecha))}}/{{$estadillo_granja['granja']}}">Imprimir</a></span></h5>
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
        <th class="text-center">% Primera Toma</th>
        <th class="text-center">Diámetro</th>
        <th class="text-center">Cantidad</th>
       </tr>
      </thead>
      <tbody>
       @foreach($estadillo_granja['jaulas'] as $jaulas)
          <tr>
            <td rowspan={{count($jaulas['consumos'])}} class="text-center">{{$jaulas['jaula']}}</td>
            <td rowspan={{count($jaulas['consumos'])}} class="text-center">{{$jaulas['lote']}}</td>
            <td rowspan={{count($jaulas['consumos'])}} class="text-right">{{$jaulas['stock_count_ini']}}</td>
            <td rowspan={{count($jaulas['consumos'])}} class="text-right">{{$jaulas['stock_avg_ini']}}</td>
            <td rowspan={{count($jaulas['consumos'])}} class="text-right">{{$jaulas['stock_bio_ini']}}</td>
            <td rowspan={{count($jaulas['consumos'])}} class="text-right">{{$jaulas['cantidad_toma']}}</td>
            <td rowspan={{count($jaulas['consumos'])}} class="text-center">
              <select class="num_tomas" estadillo="{{$jaulas['estadillo_id']}}" @if($jaulas['num_tomas'] <= 0) disabled @endif >
                 @for ($i=0; $i<3; $i++)
                 <option value={{$i}} 
                   @if ($i == $jaulas['num_tomas']) selected @endif >{{$i}}</option>
                 @endfor
              </select></td>
            <td rowspan={{count($jaulas['consumos'])}} class="text-center">
               <input class="porcentaje_primera_toma" estadillo="{{$jaulas['estadillo_id']}}" type="text" value="{{$jaulas['porcentaje_primera_toma']}}"></td>
            @for($i=0; $i < count($jaulas['consumos']); $i++)
               @if ($i==1)
                 </tr><tr>
               @endif
               <td class="text-center">{{$jaulas['consumos'][$i]['pellet']}}</td>
               <td class="text-right">{{$jaulas['consumos'][$i]['cantidad']}}</td>

            @endfor
           
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
      $("#fecha_pedido").change(function()
      {
        $("#formulario_estadillo").submit();
      });

      $(".num_tomas").change(function()
        {
          var id = $(this).attr('estadillo');
          var atributo = 'estadillo=' +  id;
          var porcentaje = $('.porcentaje_primera_toma[' + atributo +']').val();
          
          $.post('/acuifarm/public/ajax/estadillos',
                  'idestadillo=' + id + '&numtomas=' + $(this).val(),
                  function(data)
                   {
                     
                     $('.porcentaje_primera_toma[' + atributo +']').val(data.porcentaje);
                   },
                  'json'
                 );
          //alert(porcentaje);
        });

      $("#glyphicon-print").click(function(){
        //salert($(this).attr('fecha') + ' ' + $(this).attr('granja'));
        $.post('/acuifarm/public/ajax/estadillos/excel',
                  'fecha=' + $(this).attr('fecha') + '&granja=' + $(this).attr('granja'));
      });
    
    });
  </script>
  

@stop