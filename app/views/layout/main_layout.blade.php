<!doctype html>
<html lang="es">
<head>
	<meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Acuifarm</title>
	
    {{ HTML::style('css/style.css') }}
	  {{ HTML::style('http://code.jquery.com/ui/1.10.1/themes/start/jquery-ui.css') }}
    
    <!-- Inicio Bootstrap -->
    <!-- Latest compiled and minified CSS -->
    {{ HTML::style('https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css')}}
    <!-- Optional theme -->
    {{ HTML::style('css/jumbotron-narrow.css')}}
    <!-- Latest compiled and minified JavaScript -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>
    <!-- Fin Bootstrap -->
	
	 <!-- <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script> -->
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
        $("#fecha_fichero").datepicker();
        
        

       	
      
      });
	</script> 
	<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
	
</head>
<body>
<div class='container'>
  <div class='header'>
  <ul class='nav nav-pills pull-right'>
    <li>{{HTML::link('', 'Inicio')}}</li>
   <li>{{HTML::link('propuesta', 'Propuestas')}}</li>
   <li>{{HTML::link('traslado', 'Traslados')}}</li>
   <li>{{HTML::link('estadillos', 'Estadillos')}}</li>
   <li class="dropdown">
    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Pedidos<span class="caret"></span></a>
     <ul class="dropdown-menu" role="menu">
            <li>{{HTML::link('pedido', 'Listado de Pedidos')}}</li>
            <li>{{HTML::link('pedido/solicitudpago', 'Pedidos a Pagar')}}</li>
            <li>{{HTML::link('pedido/solicitudcarga', 'Pedidos a Cargar')}}</li>
            <li>{{HTML::link('pedido/solicituddescarga', 'Pedidos a Descargar')}}</li>
            
      </ul>
   </li>
   <li>{{HTML::link('consumo/proveedores', 'Stock Proveedores')}}</li>
   <li>{{HTML::link('consumo/almacenes', 'Stock Almacenes')}}</li>
   <li class="dropdown">
     <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Mantenimiento<span class="caret"></span></a>
     <ul class="dropdown-menu" role="menu">
            <li>{{HTML::link('pienso', 'Piensos')}}</li>
            <li>{{HTML::link('proveedor', 'Proveedores')}}</li>
            <li><a href="#">Tablas de Piensos</a></li>
            <li class="divider"></li>
            <li><a href="#">Separated link</a></li>
            <li class="divider"></li>
            <li><a id="downloadButton" href="#">Actualizar Simulación</a></li>
      </ul></li>
  <div class='marca'></div>
<!-- </div> -->
</ul>
<h3 class='text-muted'>Acuifarm</h3>
</div>
<br>
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