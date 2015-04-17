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
                                            
                                            for (i=0; i<data.stock_teorico.length; i++)
                                             {
                                                //console.log(data.stock_teorico[i].data);
                                                stock_final_skretting.series[i].name = data.stock_teorico[i].name;
                                                stock_final_skretting.series[i].data = data.stock_teorico[i].data;
                                                stock_final_skretting.series[i].color = data.stock_teorico[i].color;
                                             }
                                            
                                            $('#container2').highcharts(stock_final_skretting);
                                            
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
                                            //console.log(data.categories);
                                            
                                            for (i=0; i<data.stock_teorico.length; i++)
                                             {
                                                //console.log(data.stock_teorico[i].data);
                                                stock_final_biomar.series[i].name = data.stock_teorico[i].name;
                                                stock_final_biomar.series[i].data = data.stock_teorico[i].data;
                                                stock_final_biomar.series[i].color = data.stock_teorico[i].color;
                                             }
                                            
                                            $('#container5').highcharts(stock_final_biomar);
                                            
                                           }
          });
        
        $('#container').highcharts(
        {
            chart: {zoomType: 'xy',
                type: 'column'
            },
            title: {
                text: 'Stock final diario'
                
            },
            subtitle: {
                text: 'Skretting'
                
            },
            xAxis: {
                categories: ['15/04/15', '16/04/15', '17/04/15', '18/04/15', '19/04/15', '20/04/15']
            },
            yAxis: {
                title: {
                    text: 'Kg.'
                }
            },
            tooltip: {
                valueSuffix: 'Kg.'
            },
            legend: {
                layout: 'vertical',
                align: 'right',
                verticalAlign: 'middle',
                borderWidth: 0
            },
            series: [{
                name: '1.50',
                data: [2500, 2500, 2500, 2500, 2500, 2500],
                color: '#4F81BD'
            }, {
                name: '1.90',
                data: [250, 175, 100, 25, 25, 25],
                color: '#953735'
            }, {
                name: '2.00',
                data: [2375, 2175, 1950, 1725, 1425, 1125],
                color: '#77933C'
            }, {
                name: '4.00',
                data: [1975, 825, -325, (-1475), (-2650), -4050],
                color: '#604A7B'
            }, {
                name: '6.00',
                data: [19050, 16100, 13150, 10200, -2500, -4300],
                color: '#31859C'
            }, {
                name: '8.00',
                data: [16625, 14025, 11425, 8825, 6225, 3625],
                color: '#F79646'
            }, {
                name: '10.00',
                data: [9475, 8525, 7575, 6625, 5675, 4725],
                color: '#95B3D7'
            }]
        });

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
    });
     //$('#container2').highcharts(stock_final_skretting);
     $('#calendar-container').datepicker({numberOfMonths: [3,1] });
   
   /* I like the rounded corners on the calendar headers so... */
   //$('#calendar-container .ui-datepicker-header').removeClass('ui-corner-right').removeClass('ui-corner-left');
   //$('#calendar-container .ui-datepicker-header').addClass('ui-corner-right').addClass('ui-corner-left');
        
    });
    
</script>


  
@stop