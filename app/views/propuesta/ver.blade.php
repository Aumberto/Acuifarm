@extends('layout.main_layout')

@section('content')
<div class='container'>
  <h3 align='center'>{{$titulo_cabecera_propuesta}}</h3>
	<div class="row">
  <div class='col-md-5'>
	<h5 class="text-center">{{$mensaje_cabecera_status}} </h5>
	
	<table class="table table-striped table-bordered">
	 <thead>
    <tr>
			<th class="text-center">Unidad</th>
			<th class="text-center">Nº Lote</th>
			<th class="text-center">Nº peces</th>
			<th class="text-center">pm (g)</th>
			<th class="text-center">Biomasa (Kg)</th>
			<th class="text-center">Pienso (Kg)</th>
			<th class="text-center">SFR%</td>
      <th class="text-center"></td>
		</tr>
   </thead>
   <tbody>
		@foreach($resultado_status as $status)
  		<tr class="detalle_propuesta">
  			 <td class="text-center">{{$status->nombre}}</td>
  			 <td class="text-center">{{$status->groupid}}</td>
  			 <td class="text-right">{{number_format($status->stock_count_ini, 0, ',', '.')}}</td>
  			 <td class="text-right">{{number_format($status->stock_avg_ini, 2, ',', '.')}}</td>
             <td class='bio_status text-right' jaula='{{$status->nombre}}'>{{number_format($status->stock_bio_ini, 0, ',', '.')}}</td>
             <td class='cantidad_modelo_status text-right' jaula='{{$status->nombre}}'>{{$status->cantidad_toma_modelo}}</td>
             <td class='sfr_status text-right' jaula='{{$status->nombre}}'>{{$status->sfr}}%</td>
  			     <td><button class="btn_grafica" fechaIni='{{$fechaIni}}' jaula='{{$status->nombre}}'></button> </td>
  	     </tr>
  		@endforeach
    @foreach($total_resultado_status as $trs)
		 <tr>
     <td colspan='2' class="text-right info"><b>Total:</b></td>
     <td class="text-right info"><b>{{number_format($trs->total_stock_ini, 0, ',', '.')}}</b></td>  
     <td class="text-right info"><b>{{number_format($trs->total_avg_ini, 2, ',', '.')}}</b></td>
     <td class="text-right info"><b>{{number_format($trs->total_bio_ini, 0, ',', '.')}}</b></td>
     <td class="text-right info"><b>{{number_format($trs->cantidad_toma, 0, ',', '.')}}</b></td>
     @if ($trs->total_bio_ini == 0)
     <td class="text-right info"><b>0%</b></td>
     @else
     <td class="text-right info"><b>{{number_format(($trs->cantidad_toma)/($trs->total_bio_ini)*100, 2, ',', '.') }}%</b></td>
     @endif
     <td class="text-right info"></td>
     

     </tr>
     @endforeach
   </tbody>
	</table>
	</div>
	
	<div class='col-md-7'>
      <h5 class="text-center">{{$mensaje_cabecera_propuesta}}</h5>
      <table class="table table-striped table-bordered">
      	<thead>
         <tr>
          <th class="text-center">Unidad</th>
      		<th class="text-center">Proveedor</th>
			    <th class="text-center">Pellet</th>
			    <th class="text-center">Pienso (Kg)</th>
			    <th class="text-center">Estrategia (%)</th>
			    <th class="text-center">Sacos</th>
			    <th class="text-center">Total Pienso</th>
			    <th class="text-center">SFR%</th>
			    <th class="text-center" colspan="2">% vs Modelo</th>
      	</tr>
      </thead>

      <tbody>
      	@foreach($resultado_propuesta as $propuesta)
  		  <tr class="detalle_propuesta">
         <td class="text-center">{{$propuesta["nombre"]}}</td>
  			 <td class="text-center">
           <select class='rango' jaula='{{$propuesta["nombre"]}}' lote='{{$propuesta["lote"]}}' fechaIni='{{$fechaIni}}' fechaFin='{{$fechaFin}}'>
            @foreach($rangos as $rango)
              @if($rango->id == $propuesta["rango"])
                <option value="{{$rango->id}}" selected>{{$rango->nombre}}</option>
              @else
                <option value="{{$rango->id}}">{{$rango->nombre}}</option>
              @endif
            @endforeach
           </select>
         </td>
         <td class='diametro_pienso text-center' jaula='{{$propuesta["nombre"]}}'>{{$propuesta["diametro_pienso"]}}</td>
  			 <td class='cantidad_modelo text-right' jaula='{{$propuesta["nombre"]}}'>{{$propuesta["cantidad_recomendada"]}}</td>
  			 
         @if ( number_format(ceil(($propuesta["cantidad_recomendada"]/25))*25, 0, ',', '.') == 0)
  			  <td class="text-center"><input size="3" class='porcentaje' jaula='{{$propuesta["nombre"]}}' lote='{{$propuesta["lote"]}}' fechaIni='{{$fechaIni}}' fechaFin='{{$fechaFin}}' value=''>%</td>
  			 @else
 			    <td class="text-center"> <input size="3" class='porcentaje' jaula='{{$propuesta["nombre"]}}' lote='{{$propuesta["lote"]}}' fechaIni='{{$fechaIni}}' fechaFin='{{$fechaFin}}' value='{{$propuesta["porcentaje_toma"] }}'>%</td>
  			 @endif
         
         <td class='sacos text-right' jaula='{{$propuesta["nombre"]}}'>{{$propuesta["cantidad"] / 25}}</td>
         <td class='cantidad text-right' jaula='{{$propuesta["nombre"]}}'>{{$propuesta["cantidad"]}}</td>
         <td class='sfr text-right' jaula='{{$propuesta["nombre"]}}'></td>
         
         @if ($propuesta["cantidad_recomendada"] == 0)
  			   <td class='porcentaje_modelo text-right' jaula='{{$propuesta["nombre"]}}' lote='{{$propuesta["lote"]}}' fechaIni='{{$fechaIni}}' cantidadmodelo='{{$propuesta["cantidad_recomendada"]}}'> - </td>
  			 @else
  			   <td class='porcentaje_modelo text-right' jaula='{{$propuesta["nombre"]}}' lote='{{$propuesta["lote"]}}' fechaIni='{{$fechaIni}}' cantidadmodelo='{{$propuesta["cantidad_recomendada"]}}'>{{ number_format(($propuesta["cantidad"] /$propuesta["cantidad_recomendada"]) *100, 0, '.', '') }}%</td>
  			 @endif
  			 
         <td><button class="btn_consumo" fechaIni='{{$fechaIni}}' fechaFin='{{$fechaFin}}' jaula='{{$propuesta["nombre"]}}'></button> </td>
  	    </tr>
  		  @endforeach
      <tbody>
    </table>
	</div>
	</div>
  {{HTML::link('/propuesta', 'Volver',  array('class' =>'btn btn-primary'))}}
  </div>
   <div id="dialog" title="Gráficas">
    <div id="tabs">
      <ul>
        <li><a href="#tabs-1">Consumo</a></li>
        <li><a href="#tabs-2">Crecimiento</a></li>
        <li><a href="#tabs-3">Respuestas</a></li>
      </ul>
      <div id="tabs-1">
        <p><strong>Click this tab again to close the content pane.</strong></p>
        <p>Proin elit arcu, rutrum commodo, vehicula tempus, commodo a, risus. Curabitur nec arcu. Donec sollicitudin mi sit amet mauris. Nam elementum quam ullamcorper ante. Etiam aliquet massa et lorem. Mauris dapibus lacus auctor risus. Aenean tempor ullamcorper leo. Vivamus sed magna quis ligula eleifend adipiscing. Duis orci. Aliquam sodales tortor vitae ipsum. Aliquam nulla. Duis aliquam molestie erat. Ut et mauris vel pede varius sollicitudin. Sed ut dolor nec orci tincidunt interdum. Phasellus ipsum. Nunc tristique tempus lectus.</p>
      </div>
      <div id="tabs-2">
        <p><strong>Click this tab again to close the content pane.</strong></p>
        <p>Morbi tincidunt, dui sit amet facilisis feugiat, odio metus gravida ante, ut pharetra massa metus id nunc. Duis scelerisque molestie turpis. Sed fringilla, massa eget luctus malesuada, metus eros molestie lectus, ut tempus eros massa ut dolor. Aenean aliquet fringilla sem. Suspendisse sed ligula in ligula suscipit aliquam. Praesent in eros vestibulum mi adipiscing adipiscing. Morbi facilisis. Curabitur ornare consequat nunc. Aenean vel metus. Ut posuere viverra nulla. Aliquam erat volutpat. Pellentesque convallis. Maecenas feugiat, tellus pellentesque pretium posuere, felis lorem euismod felis, eu ornare leo nisi vel felis. Mauris consectetur tortor et purus.</p>
      </div>
      <div id="tabs-3">
        <p><strong>Click this tab again to close the content pane.</strong></p>
        <p>Duis cursus. Maecenas ligula eros, blandit nec, pharetra at, semper at, magna. Nullam ac lacus. Nulla facilisi. Praesent viverra justo vitae neque. Praesent blandit adipiscing velit. Suspendisse potenti. Donec mattis, pede vel pharetra blandit, magna ligula faucibus eros, id euismod lacus dolor eget odio. Nam scelerisque. Donec non libero sed nulla mattis commodo. Ut sagittis. Donec nisi lectus, feugiat porttitor, tempor ac, tempor vitae, pede. Aenean vehicula velit eu tellus interdum rutrum. Maecenas commodo. Pellentesque nec elit. Fusce in lacus. Vivamus a libero vitae lectus hendrerit hendrerit.</p>
      </div>
    </div>
     
  </div>
  <div id="v_consumos" title="Consumos">
     <p>Gráficas</p>
  </div>
	<script>
    $(function() 
    {
      $( "#tabs" ).tabs({
      collapsible: true
      });
      $('#dialog').dialog({
        autoOpen : false,
        modal: true,
        width: 900,
        position: {my: "center", at: "center"},
        open: function(event, ui) {
                     $(this).css({'max-height': $(document).height()-200, 'overflow-y': 'auto'});
         },
        buttons: {
        Cerrar: function() {
          $( this ).dialog( "close" );
        }
      }
      });
      $('#v_consumos').dialog({
        autoOpen : false,
        modal: true,
        width: 900,
        position: {my: "center", at: "center"},
        open: function(event, ui) {
                     $(this).css({'max-height': $(document).height()-200, 'overflow-y': 'auto'});
         },
        buttons: {
        Cerrar: function() {
          $(this).dialog( "close" );
        }
      }
      });
      // Al cargar la página actualizamos el valor del SFR %
      $('.sfr').each(function(indice, elemento)
         {
            //console.log('El elemento con el índice '+indice+' contiene '+$(elemento).text());
            var jaula = $(elemento).attr('jaula');
            var atributo = 'jaula=' +  jaula;
            var sfr = ( $('.cantidad[' + atributo +']').text() / $('.bio_status[' + atributo +']').text())/10;
            //alert($('.cantidad[' + atributo +']').text() + '/' + ($('.bio_status[' + atributo +']').text())*1000);
            $('.sfr[' + atributo +']').text(sfr.toFixed(2) + '%');
         });

      $('.cantidad_modelo').each(function(indice, elemento)
         {
           var jaula = $(elemento).attr('jaula');
           var atributo = 'jaula=' +  jaula;
           var cantidad = $('.cantidad_modelo_status[' + atributo +']').text();
           $(elemento).text(cantidad);
           //alert(cantidad);
         });

      // Cada vez que modificamos el valor de la casilla porcentaje, actualizamos el resto de casillas dependientes.
      $('.porcentaje').change(function()
         {
            var jaula = $(this).attr('jaula');
            var lote  = $(this).attr('lote');
            var atributo = 'jaula=' +  jaula;
            var fechaIni = $(this).attr('fechaIni');
            var fechaFin = $(this).attr('fechaFin');
            
            
            var sacos = Math.ceil((($(this).val()* $('.cantidad_modelo[' + atributo +']').text())/100)/25);
            var porcentaje_modelo = Math.ceil(((sacos * 25)/ $('.porcentaje_modelo[' + atributo +']').attr('cantidadmodelo'))*100);
            //var porcentaje_propuesta = Math.ceil((( sacos * 25) / $('.cantidad_modelo[' + atributo +']').text())*100);
            var sfr = ( (sacos * 25) / $('.bio_status[' + atributo +']').text()) * 100;
            //$(this).val(porcentaje_propuesta);
            
            $('.sacos[' + atributo +']').text(sacos);
            $('.cantidad[' + atributo +']').text(sacos * 25);
            $('.porcentaje_modelo[' + atributo +']').text(porcentaje_modelo + '%');
            $('.sfr[' + atributo +']').text(sfr.toFixed(2) + '%');
            
            $.post('/acuifarm/public/ajax/cantidadalimentacion',
                   'jaula=' + jaula + '&lote=' + lote + '&fechaIni=' + fechaIni + '&fechaFin=' + fechaFin + '&porcentaje=' + $(this).val() + '&cantidad=' + sacos * 25,
                   function(data)
                   {
                    //alert(data);
                   },
                   'json');
         });
      // Cada vez que cambiemos el proveedor principal de pienso, debemos buscar el tipo de grano que le corresponde al pez y actualizar
      // los datos de alimentación de todo el periodo de la propuesta de alimentación.
      $('.rango').change(function()
         {
           var jaula = $(this).attr('jaula');
           var lote  = $(this).attr('lote');
           var fechaIni = $(this).attr('fechaIni');
           var fechaFin = $(this).attr('fechaFin');
           var atributo = 'jaula=' +  jaula;
           
           //alert('Hola caracola ' + $(this).val() + ' ' + fechaIni + ' ' + fechaFin + ' ' + jaula);
       
           $.post('/acuifarm/public/ajax/alimentacion',
                  'rango=' + $(this).val() + '&lote=' + lote + '&fechaIni=' + fechaIni + '&fechaFin=' + fechaFin + '&jaula=' + jaula,
                  function(data)
                   {
                     //alert(data);
                     $('.diametro_pienso[' + atributo +']').text(data);
                   },
                  'json'
                 );
         });
        
        $("button.btn_consumo").click(function(){
          var jaula = $(this).attr('jaula');
          var fechaIni = $(this).attr('fechaIni');
          var fechaFin = $(this).attr('fechaFin');
          $.ajax({
                   url: "/acuifarm/public/ajax/consumos",
                   data: {'jaula' :  jaula,
                          'fechaini' : fechaIni,
                          'fechafin' : fechaFin},
                   type:'post',
                   dataType: "html",
                   success: function(data){
                                            $('#v_consumos').dialog({position: { 'my': 'center', 'at': 'center' }});
                                            $('#v_consumos').html(data).dialog( "open" );
                                           }
          });
          
        });

        $("button.btn_grafica").click(function(){
          var jaula = $(this).attr('jaula');
          var fechaIni = $(this).attr('fechaIni');
          
          
          $('#tabs-1').html("<div id='container3'></div>");
          $('#tabs-2').html("<div id='container4'></div>");
          
          var options = {
                          chart: {zoomType: 'xy'},
                          credits: {enabled: false},
                          title: {text:'' , x: -20},
                          subtitle: {text: '', x: -20},
                          xAxis: {categories: [{}]},
                          yAxis: [{ min:0, title: { text:'Kg.'}}, { min:0, title: { text:'%'}, opposite: true}],
                          tooltip: {formatter: function() {
                                                            var s = '<b>'+ this.x +'</b>';
                                                            $.each(this.points, function(i, point) {
                                                                s += '<br/>'+point.series.name+': '+point.y;
                                                            });
                                                            return s;},
                          shared: true},
                          series: [{},{},{},{},{}]};
          var options2 ={
                          chart: {zoomType: 'xy'},
                          credits: {enabled: false},
                          title: {text:'' , x: -20},
                          subtitle: {text: '', x: -20},
                          xAxis: {categories: [{}]},
                          yAxis: [{ min:0, title: { text:'grs.'}}, { min:0, title: { text:'%'}, opposite: true}],
                          tooltip: {formatter: function() {
                                                            var s = '<b>'+ this.x +'</b>';
                                                            $.each(this.points, function(i, point) {
                                                                s += '<br/>'+point.series.name+': '+point.y;
                                                            });
                                                            return s;},
                          shared: true},
                          series: [{},{}]};
          $.ajax({
                   url: "/acuifarm/public/ajax/grafica/comparativaconsumo",
                   data: {'jaula' :  jaula,
                          'fechaini' : fechaIni},
                   type:'post',
                   dataType: "json",
                   success: function(data){
                                            //option.chart.type = 'spline';
                                            //alert(data);
                                            options.xAxis.categories = data.categories;
                                            options.xAxis.type = 'datetime';
                                            options.title.text = data.titulo;
                                            options.subtitle.text = data.subtitulo;
                                            options.series[0].name = 'Real Kg.';
                                            options.series[0].data = data.real;
                                            options.series[0].type ='column';
                                            options.series[1].name = 'Estrategia Kg.';
                                            options.series[1].data = data.estrategia;
                                            options.series[1].type = 'column';
                                            options.series[2].name = 'Modelo Kg.';
                                            options.series[2].data = data.modelo;
                                            options.series[2].type = 'column';
                                            options.series[3].yAxis = 1;
                                            options.series[3].name = 'Estrategia %';
                                            options.series[3].data = data.propuestaVsModelo;
                                            options.series[3].type ='spline';
                                            options.series[4].yAxis = 1;
                                            options.series[4].name = 'Real %';
                                            options.series[4].data = data.realVsModelo;
                                            options.series[4].type ='spline';
                                            $('#container3').highcharts(options);
                                            //Definimos las opciones de la gráfica de crecimiento
                                            options2.xAxis.categories = data.categories2;
                                            options2.xAxis.type = 'datetime';
                                            options2.title.text = data.titulo2;
                                            options2.subtitle.text = data.subtitulo2;
                                            options2.series[0].name = 'Peso medio';
                                            options2.series[0].data = data.peso_medio;
                                            options2.series[1].yAxis = 1;
                                            options2.series[1].name = 'SFR';
                                            options2.series[1].data = data.sfr;
                                            $('#container4').highcharts(options2);
                                           }
          });
          $('#dialog').dialog({position: { 'my': 'center', 'at': 'center' }});
          $('#dialog').dialog( "open" );

        });
          

     
      
    });
	</script>
@stop