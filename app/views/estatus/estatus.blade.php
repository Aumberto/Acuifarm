@extends('layout.main_layout')

@section('content')
  
<div class="container">
  <div class="barra_lateral"> 
    <div id="calendar-container"></div>
  </div>
  
   <div id="container"></div>
   <div id="container1"></div>
   <div id="container2"></div>
   <div id="container5"></div>
  
</div>

<script type="text/javascript">
$(function () {
     var stock_final_skretting = {
                                   chart: {zoomType: 'xy', type: 'column'},
                                   credits: {enabled: false},
                                   title: {text:'' , x: -20},
                                   subtitle: {text: '', x: -20},
                                   xAxis: {categories: [{}]},
                                   yAxis: [{ title: { text:'Kg.'}}],
                                   tooltip: {
                valueSuffix: 'Kg.'
            },
                                   
                                   series: [{},{},{},{},{},{},{}]};
                                   $.ajax({
                   url: "/acuifarm/public/ajax/grafica/status/stockfinal",
                   data: {'proveedor_id' :  1},
                   type:'post',
                   dataType: "json",
                   success: function(data){
                                            //option.chart.type = 'spline';
                                            //alert(data);
                                            stock_final_skretting.xAxis.categories = data.categories;
                                            stock_final_skretting.xAxis.type = 'datetime';
                                            stock_final_skretting.title.text = data.titulo;
                                            stock_final_skretting.subtitle.text = data.subtitulo;
                                            //var i;
                                            //console.log(data.categories);
                                            /*
                                            for (i=0; i<data.stock_teorico.length; i++)
                                             {
                                                //console.log(data.stock_teorico[i].data);
                                                stock_final_skretting.series[i].name = data.stock_teorico[i].name;
                                                stock_final_skretting.series[i].data = data.stock_teorico[i].data;
                                                stock_final_skretting.series[i].color = data.stock_teorico[i].color;
                                             } */
                                            stock_final_skretting.series = data.stock_teorico;
                                            $('#container').highcharts(stock_final_skretting);
                                            
                                           }
          }); 

           var stock_final_biomar = {
                                   chart: {zoomType: 'xy', type: 'column'},
                                   credits: {enabled: false},
                                   title: {text:'' , x: -20},
                                   subtitle: {text: '', x: -20},
                                   xAxis: {categories: [{}]},
                                   yAxis: [{ title: { text:'Kg.'}}],
                                   tooltip: {
                valueSuffix: 'Kg.'
            },
                                   
                                   series: [{},{},{},{},{},{}]};
                                   $.ajax({
                   url: "/acuifarm/public/ajax/grafica/status/stockfinal",
                   data: {'proveedor_id' :  2},
                   type:'post',
                   dataType: "json",
                   success: function(data){
                                            //option.chart.type = 'spline';
                                            //alert(data);
                                            stock_final_biomar.xAxis.categories = data.categories;
                                            stock_final_biomar.xAxis.type = 'datetime';
                                            stock_final_biomar.title.text = data.titulo;
                                            stock_final_biomar.subtitle.text = data.subtitulo;
                                            //var i;
                                            //console.log(data.stock_teorico);
                                            /*
                                            for (i=0; i<data.stock_teorico.length; i++)
                                             {
                                                //console.log(data.stock_teorico[i].data);
                                                stock_final_biomar.series[i].name = data.stock_teorico[i].name;
                                                stock_final_biomar.series[i].data = data.stock_teorico[i].data;
                                                stock_final_biomar.series[i].color = data.stock_teorico[i].color;
                                                //alert(data.stock_teorico[i].name + ' ' + data.stock_teorico[i].data);
                                             }
                                            */
                                            stock_final_biomar.series = data.stock_teorico;
                                            $('#container2').highcharts(stock_final_biomar);
                                            
                                           }
          });
           var contenedores_skretting = {
                                   chart: { type: 'bar'},
                                   credits: {enabled: false},
                                   title: {text:'' },
                                   subtitle: {text: ''},
                                   xAxis: {categories: [{}]},
                                   yAxis: [{ title: { text:'Kg.'}}],
                                   plotOptions: {
                                                  series: {
                                                           stacking: 'normal'
                                                          }
                                   },
                                   tooltip: {
                valueSuffix: 'Kg.'
            },
                                   
                                   series: [{},{},{},{},{},{},{}]};
                                   $.ajax({
                   url: "/acuifarm/public/ajax/grafica/status/contenedores",
                   data: {'proveedor_id' :  1},
                   type:'post',
                   dataType: "json",
                   success: function(data){
                                            //option.chart.type = 'spline';
                                            //alert(data);
                                            contenedores_skretting.xAxis.categories = data.categories;
                                            //stock_final_biomar.xAxis.type = 'datetime';
                                            contenedores_skretting.title.text = data.titulo;
                                            contenedores_skretting.subtitle.text = data.subtitulo;
                                            //var i;
                                            //console.log(data.contenido_contenedores);
                                            
                                            /*
                                            for (i=0; i<data.contenido_contenedores.length; i++)
                                             {
                                                //console.log(data.stock_teorico[i].data);
                                                //alert(data.contenido_contenedores[i].name + ' ' + data.contenido_contenedores[i].data);
                                                contenedores_skretting.series[i].name = data.contenido_contenedores[i].name;
                                                contenedores_skretting.series[i].data = data.contenido_contenedores[i].data;
                                                //console.log(data.contenido_contenedores[i].data);
                                                //contenedores_skretting.series[i].data = 10;
                                                contenedores_skretting.series[i].color = data.contenido_contenedores[i].color;
                                             } */
                                            contenedores_skretting.series = data.contenido_contenedores;
                                            //contenedores_skretting.series[0].name = '1.5';
                                            //contenedores_skretting.series[0].data = [1500];
                                            //contenedores_skretting.series[0].color = '#953735';
                                            $('#container1').highcharts(contenedores_skretting);
                                            
                                           }
          });

    var contenedores_biomar = {
                                   chart: { type: 'bar'},
                                   credits: {enabled: false},
                                   title: {text:'' },
                                   subtitle: {text: ''},
                                   xAxis: {categories: [{}]},
                                   yAxis: [{ title: { text:'Kg.'}}],
                                   plotOptions: {
                                                  series: {
                                                           stacking: 'normal'
                                                          }
                                   },
                                   tooltip: {
                valueSuffix: 'Kg.'
            },
                                   
                                   series: [{},{},{},{},{},{}]};
                                   $.ajax({
                   url: "/acuifarm/public/ajax/grafica/status/contenedores",
                   data: {'proveedor_id' :  2},
                   type:'post',
                   dataType: "json",
                   success: function(data){
                                            //option.chart.type = 'spline';
                                            //alert(data);
                                            contenedores_biomar.xAxis.categories = data.categories;
                                            //stock_final_biomar.xAxis.type = 'datetime';
                                            contenedores_biomar.title.text = data.titulo;
                                            contenedores_biomar.subtitle.text = data.subtitulo;
                                            //var i;
                                            //console.log(data.contenido_contenedores);
                                            
                                            
                                            /*for (i=0; i<data.contenido_contenedores.length; i++)
                                             {
                                                //console.log(data.stock_teorico[i].data);
                                                //alert(data.contenido_contenedores[i].name + ' ' + data.contenido_contenedores[i].data);
                                                contenedores_biomar.series[i].name = data.contenido_contenedores[i].name;
                                                contenedores_biomarg.series[i].data = data.contenido_contenedores[i].data;
                                                //console.log(data.contenido_contenedores[i].data);
                                                //contenedores_skretting.series[i].data = 10;
                                                contenedores_biomar.series[i].color = data.contenido_contenedores[i].color;
                                             } */
                                            contenedores_biomar.series = data.contenido_contenedores;
                                            //contenedores_skretting.series[0].name = '1.5';
                                            //contenedores_skretting.series[0].data = [1500];
                                            //contenedores_skretting.series[0].color = '#953735';
                                            $('#container5').highcharts(contenedores_biomar);
                                            
                                           }
          });
        
        
    /*
    $('#container1').highcharts({
        chart: {
            type: 'bar'
        },
        title: {
            text: 'Contenedores pendientes de descargar'
        },
        subtitle: {
                text: 'Skretting'
                
            },
        xAxis: {
            categories: ['M-018SK', 'M-016SK']
        },
        yAxis: {
            min: 0,
            title: {
                text: 'Total fruit consumption'
            }
        },
        legend: {
            reversed: true
        },
        plotOptions: {
            series: {
                stacking: 'normal'
            }
        },
        series: [{
            name: '1.50',
            data: [0, 0],
            color: '#4F81BD'
        }, {
            name: '1.90',
            data: [0, 0],
            color: '#953735'
        }, {
            name: '2.00',
            data: [1250, 0],
            color: '#77933C'
        }, {
            name: '4.00',
            data: [2500, 5000],
            color: '#604A7B'
        }, {
            name: '6.00',
            data: [15000, 3750],
            color: '#31859C'
        }, {
            name: '8.00',
            data: [5000, 15000],
            color: '#F79646'
        }, {
            name: '10.00',
            data: [0, 0],
            color: '#95B3D7'
        }]
    });*/
     //$('#container2').highcharts(stock_final_skretting);
     $('#calendar-container').datepicker({numberOfMonths: [3,1] });
   
   /* I like the rounded corners on the calendar headers so... */
   //$('#calendar-container .ui-datepicker-header').removeClass('ui-corner-right').removeClass('ui-corner-left');
   //$('#calendar-container .ui-datepicker-header').addClass('ui-corner-right').addClass('ui-corner-left');
        
    });
    
</script>


  
@stop