@extends('layout.main_layout')

@section('content')
<div class='container'>
 
   <table class="table table-striped table-bordered table-condensed">
		<thead>
    <tr>
        
        <th colspan="3" class="text-center">Datos a partir del {{$fecha}} </th>
        @foreach($semanas as $semana)
         <th colspan="3" class="text-center"> Semana {{$semana}} </th>
        @endforeach
		</tr>
  
    <tr>
      <th colspan="2"></th>
      <th class="text-center">Stock Inicial</th>
      @for($j=0; $j<7; $j++)
      <th class="text-center">Consumo</th>
      <th class="text-center">Entradas</th>
      <th class="text-center">Stock Final</th>
      
      @endfor
    </tr>
    </thead>
    <tbody>
		@foreach($datos as $fila)
  		<tr>
  			@for($i=1; $i <= count($fila); $i++)
              @if ($fila[$i] < 0)
                <td class="danger"><b>{{$fila[$i]}}</b></td>
              @else
                 @if ((($i == 5) and $fila[$i] > 0) or (($i == 8) and $fila[$i] > 0) or (($i == 11) and $fila[$i] > 0) or (($i == 14) and $fila[$i] > 0) or
                      (($i == 17) and $fila[$i] > 0) or (($i == 20) and $fila[$i] > 0) or (($i == 23) and $fila[$i] > 0)) 
                    <td class="success"><b>{{$fila[$i]}}</b></td>
                 @else
                    <td>{{$fila[$i]}}</td>
                 @endif
              @endif
              
  			@endfor
  			 
  	     </tr>
  		@endforeach
		</tbody>
	</table>
  
	

	</div>
  <select id='id_semana'>
    @foreach ($semanas_listado as $semana_listado)
      <option value='{{$semana_listado->id}}'>Semana {{$semana_listado->week}} ({{$semana_listado->year}})</option>
    @endforeach
  </select>

<div id='slider0' class='slider'></div>
  <p><label for="amount0">BIOMAR IBERIA, S.A. 1.50  Número de Palets:</label>
     <input type="text" id="amount0" readonly style="border:0; color:#f6931f; font-weight:bold;">
  </p>

<div id='slider1' class='slider'></div>
  <p><label for="amount1">BIOMAR IBERIA, S.A. 1.90  Número de Palets:</label>
     <input type="text" id="amount1" readonly style="border:0; color:#f6931f; font-weight:bold;">
  </p>
<div id='slider2' class='slider'></div>
  <p><label for="amount2">BIOMAR IBERIA, S.A. 3.00  Número de Palets:</label>
     <input type="text" id="amount2" readonly style="border:0; color:#f6931f; font-weight:bold;">
  </p>
<div id='slider3' class='slider'></div>
  <p><label for="amount3">BIOMAR IBERIA, S.A. 4.50  Número de Palets:</label>
     <input type="text" id="amount3" readonly style="border:0; color:#f6931f; font-weight:bold;">
  </p>
<div id='slider4' class='slider'></div>
  <p><label for="amount4">BIOMAR IBERIA, S.A. 6.50  Número de Palets:</label>
     <input type="text" id="amount4" readonly style="border:0; color:#f6931f; font-weight:bold;">
  </p>
<div id='slider5' class='slider'></div>
  <p><label for="amount5">BIOMAR IBERIA, S.A. 9.00  Número de Palets:</label>
     <input type="text" id="amount5" readonly style="border:0; color:#f6931f; font-weight:bold;">
  </p>
<div id='slider6' class='slider'></div>
  <p><label for="amount6">DIBAQ DIPROTEG S.A. 3.00  Número de Palets:</label>
     <input type="text" id="amount6" readonly style="border:0; color:#f6931f; font-weight:bold;">
  </p>
<div id='slider7' class='slider'></div>
  <p><label for="amount7">DIBAQ DIPROTEG S.A. 4.50  Número de Palets:</label>
     <input type="text" id="amount7" readonly style="border:0; color:#f6931f; font-weight:bold;">
  </p>
<div id='slider8' class='slider'></div>
  <p><label for="amount8">DIBAQ DIPROTEG S.A. 7.00  Número de Palets:</label>
     <input type="text" id="amount8" readonly style="border:0; color:#f6931f; font-weight:bold;">
  </p>
<div id='slider9' class='slider'></div>
  <p><label for="amount9">DIBAQ DIPROTEG S.A. 9.00  Número de Palets:</label>
     <input type="text" id="amount9" readonly style="border:0; color:#f6931f; font-weight:bold;">
  </p>
<div id='slider10' class='slider'></div>
  <p><label for="amount10">SKRETTING ESPAÑA, S.A. 1.50  Número de Palets:</label>
     <input type="text" id="amount10" readonly style="border:0; color:#f6931f; font-weight:bold;">
  </p>
<div id='slider11' class='slider'></div>
  <p><label for="amount11">SKRETTING ESPAÑA, S.A. 1.90  Número de Palets:</label>
     <input type="text" id="amount11" readonly style="border:0; color:#f6931f; font-weight:bold;">
  </p>
<div id='slider12' class='slider'></div>
  <p><label for="amount12">SKRETTING ESPAÑA, S.A. 2.00  Número de Palets:</label>
     <input type="text" id="amount12" readonly style="border:0; color:#f6931f; font-weight:bold;">
  </p>
<div id='slider13' class='slider'></div>
  <p><label for="amount13">SKRETTING ESPAÑA, S.A. 4.00  Número de Palets:</label>
     <input type="text" id="amount13" readonly style="border:0; color:#f6931f; font-weight:bold;">
  </p>
<div id='slider14' class='slider'></div>
  <p><label for="amount14">SKRETTING ESPAÑA, S.A. 6.00  Número de Palets:</label>
     <input type="text" id="amount14" readonly style="border:0; color:#f6931f; font-weight:bold;">
  </p>
  <div id='slider15' class='slider'></div>
  <p><label for="amount15">SKRETTING ESPAÑA, S.A. 8.00  Número de Palets:</label>
     <input type="text" id="amount15" readonly style="border:0; color:#f6931f; font-weight:bold;">
  </p>
  <div id='slider16' class='slider'></div>
  <p><label for="amount16">SKRETTING ESPAÑA, S.A. 10.00  Número de Palets:</label>
     <input type="text" id="amount16" readonly style="border:0; color:#f6931f; font-weight:bold;">
  </p>
  <script>
  $.fn.addRange = function (min, max, color) {
  if (color == 'naranja')
  {
  this.prepend('<div class=range></div>');
  $range = this.find('.range');
  $range.css("left", min * 1.53846154 + '%');
  //$range.css("right", 100 - (max * 0.65) + '%');
  $range.css("right", 100 - (max * 1.53846154) + '%');
  } 
  else if (color == 'verde_claro')
  {
   this.prepend('<div class=range_verde_claro></div>');
   $range = this.find('.range_verde_claro');
   $range.css("left", min * 1.53846154 + '%');
   $range.css("right", 100 - (max * 1.53846154) + '%');
  }else if (color == 'verde_oscuro')
  {
   this.prepend('<div class=range_verde_oscuro></div>');
   $range = this.find('.range_verde_oscuro');
   $range.css("left", min * 1.53846154 + '%');
   $range.css("right", 100 - (max * 1.53846154) + '%');
  }else if (color == 'rojo')
  {
   this.prepend('<div class=range_rojo></div>');
   $range = this.find('.range_rojo');
   $range.css("left", 0 + '%');
   $range.css("right", 100 - (max * 1.53846154) + '%');
  }
};

//var $slider = $('#slider0');
//$slider.slider({
//      value:3,
//      min: 0,
//      max: 66,
//      step: 1,
//    slide: function( event, ui ) {
//        $( "#amount" ).val(ui.value );
//      }});$( "#amount" ).val($( "#slider" ).slider( "value" ) );
//$slider.addRange(7, 20, 'naranja');
//$slider.addRange(20, 33, 'verde_claro');
//$slider.addRange(33, 65, 'verde_oscuro');

var $slider0 = $('#slider0');
$slider0.slider();

$('#id_semana').change(function()
         {
                        
            $.post('/acuifarm/public/ajax/pedidos',
                   'id_semana=' + $(this).val(),
                   function(data)
                   {
                    for (var i = 0; i< data.length; i++)
                    {
                      var stock_final = parseInt(data[i].stock_real) + parseInt(data[i].pedidos) + parseInt(data[i].pedidosobsoletos) + parseInt(data[i].pedidos_acumulados) - parseInt(data[i].consumo_simulado) - parseInt(data[i].consumo_simulado_acumulado);
                      //alert(stock_final);
                      // Si el stock_final es < 0, el intervalo rojo será hasta que lleguemos a cero
                      if (stock_final < 0)
                      {
                        var max_rojo = Math.ceil((stock_final * -1)/1250);
                        //alert(max_rojo);
                        
                      }
                      else
                      {
                        var max_rojo = 0;
                        //var max_naranja = 10;
                        //var max_verde_claro = 20;
                      }
                      //var max_naranja = Math.ceil(((stock_final - parseInt(data[i].consumo_simulado_siguiente_semana)) * -1)/1250);
                      var max_naranja = Math.ceil((parseInt(data[i].consumo_simulado_siguiente_semana) - stock_final)/1250)
                      var max_verde_claro = Math.ceil(((parseInt(data[i].consumo_simulado_siguiente_semana)*2) - stock_final)/1250)
                      //alert('rojo:' + max_rojo + ' naranja: ' + max_naranja + ' verde_claro: ' + max_verde_claro);
                      //var ${slider}+i =
                      eval("var $slider" + i + " = $('#slider" + i + "')");
                      eval("$slider" + i + ".slider({value:" + max_rojo +", min: 0, max: 65, step:1, slide: function( event, ui ) {$('#amount" + i + " ').val(ui.value);}});$('#amount" + i + "').val($('#slider" + i + "' ).slider('value'))");
                      eval("$slider" + i + ".addRange(0, " + max_rojo + ", 'rojo')");
                      eval("$slider" + i + ".addRange(" + max_rojo + ", " + max_naranja + ", 'naranja')");
                      eval("$slider" + i + ".addRange(" + max_naranja + ", " + max_verde_claro + ", 'verde_claro')");
                      eval("$slider" + i + ".addRange(" + max_verde_claro + ", 65, 'verde_oscuro')");
                    }
                      //alert(data.nombre);
                  
                    //alert(data);
                   },
                   'json');
         });

  </script>
  
@stop