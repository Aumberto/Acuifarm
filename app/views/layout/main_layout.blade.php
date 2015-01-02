<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Acuifarm</title>
	
    {{ HTML::style('css/style.css') }}
	{{ HTML::style('http://code.jquery.com/ui/1.10.1/themes/start/jquery-ui.css') }}

	
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
	{{ HTML::script('http://code.jquery.com/ui/1.10.1/jquery-ui.js') }}
	
    <script src="http://code.highcharts.com/highcharts.js"></script>
    <script src="http://code.highcharts.com/modules/exporting.js"></script>
	
	
	<script>
     $(function(){
      //Array para dar formato en español
      $.datepicker.regional['es'] =
        { closeText: 'Cerrar',
           prevText: 'Previo',
           nextText: 'Próximo',
           monthNames: ['Enero','Febrero','Marzo','Abril','Mayo','Junio',
                        'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'],
           monthNamesShort: ['Ene','Feb','Mar','Abr','May','Jun',
                             'Jul','Ago','Sep','Oct','Nov','Dic'],
           monthStatus: 'Ver otro mes', yearStatus: 'Ver otro año',
           dayNames: ['Domingo','Lunes','Martes','Miércoles','Jueves','Viernes','Sábado'],
           dayNamesShort: ['Dom','Lun','Mar','Mie','Jue','Vie','Sáb'],
           dayNamesMin: ['Do','Lu','Ma','Mi','Ju','Vi','Sa'],
           dateFormat: 'dd-mm-yy', firstDay: 1,
           weekHeader: 'Sm',
           showWeek: true,
           initStatus: 'Selecciona la fecha', isRTL: false};
        $.datepicker.setDefaults($.datepicker.regional['es']); 
       	$("#fecha_descarga").datepicker();
       	$("#fecha_pedido").datepicker();
        $("#fecha_movimiento").datepicker();
        $("#fecha_confirmacion").datepicker();
        $("#fecha_carga").datepicker();
        $("#fecha_llegada").datepicker();
        $("#fecha_pago").datepicker();
        $("#fecha_siembra").datepicker();
        
        

       	
      
      });
	</script> 
	
	
</head>
<body>

<div class='menudes'>
  {{HTML::link('propuesta', 'Propuesta')}}
  {{HTML::link('traslado', 'Traslados')}}
  {{HTML::link('pedido', 'Pedidos')}}
  {{HTML::link('consumo/proveedores', 'Stock Proveedores')}}
  {{HTML::link('consumo/almacenes', 'Stock Almacenes')}}
  <a id="downloadButton" href="#">Actualizar Simulación</a>
  <div class='marca'></div>
</div>

@yield('content')

<div id="dialog" title="File Download">
  <div class="progress-label">Actualizando datos simulados...</div>
  <div id="progressbar"></div>
</div>
<script>
  $(function() {
    var progressTimer,
      progressbar = $( "#progressbar" ),
      progressLabel = $( ".progress-label" ),
      dialogButtons = [{
        text: "Cancel Download",
        click: closeDownload
      }],
      dialog = $( "#dialog" ).dialog({
        autoOpen: false,
        closeOnEscape: false,
        modal: true,
        resizable: false,
        open: function() {
          progressTimer = 0;
        }
        
        
      }),
      downloadButton = $( "#downloadButton" )
          .on( "click", function() {
          
          dialog.dialog( "open" );
          $.ajax({
                   url: "/acuifarm/public/ajax/actualizarsimulacion",
                   type:'post',
                   dataType: "html",
                   success: function(data){
                                          progressbar.progressbar( "value", 100 );
                                           }
          });
        });
 
    progressbar.progressbar({
      value: false,
      
      complete: function() {
        progressLabel.text( "Finalizado" );
        dialog.dialog( "option", "buttons", [{
          text: "Close",
          click: closeDownload
        }]);
        $(".ui-dialog button").last().focus();
      }
    });
 
    function progress() {
      var val = progressbar.progressbar( "value" ) || 0;
 
      progressbar.progressbar( "value", val + Math.floor( Math.random() * 3 ) );
 
      if ( val <= 99 ) {
        progressTimer = setTimeout( progress, 50 );
      }
    }
 
    function closeDownload() {
      clearTimeout( progressTimer );
      dialog
        .dialog( "close" );
      progressbar.progressbar( "value", false );
      
    }
  });
  </script>  

	
</body>
</html>