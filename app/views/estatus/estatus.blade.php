@extends('layout.main_layout')

@section('content')
  
<div class="contenedor">
  <div class="barra_lateral"> 
    <div id="calendar-container"></div>
  </div>
  <div class="contenido">
   <div id="container"></div>
   <div id="container1"></div>
  </div>
</div>

<script type="text/javascript">
$(function () {
        $('#container').highcharts(
        {
            chart: {
                type: 'column'
            },
            title: {
                text: 'Consumo Semanal'
                
            },
            subtitle: {
                text: 'Skretting'
                
            },
            xAxis: {
                categories: ['36', '37', '38', '39', '40', '41']
            },
            yAxis: {
                title: {
                    text: 'Kg.'
                },
                plotLines: [{
                    value: 0,
                    width: 1,
                    color: '#808080'
                }]
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
                name: 'Pellet 1.50 mm',
                data: [900, 1050, 300, 0, 0, 0, 0]
            }, {
                name: 'Pellet 1.90 mm',
                data: [675, 875, 1900, 2275, 1650, 1500, 550]
            }, {
                name: 'Pellet 2.00 mm',
                data: [12600, 10000, 0, 0, 0, 0]
            }, {
                name: 'Pellet 4.00 mm',
                data: [14350, 15000, 18000, 16450, 0, 0]
            }, {
                name: 'Pellet 6.00 mm',
                data: [25725, 28000, 27750, 20000, 0, 0]
            }, {
                name: 'Pellet 8.00 mm',
                data: [25725, 28000, 27750, 20000, 0, 0]
            }, {
                name: 'Pellet 10.00 mm',
                data: [25725, 28000, 27750, 20000, 0, 0]
            }]
        });

      $('#container1').highcharts(
        {
            chart: {
                type: 'spline'
            },
            title: {
                text: 'Consumo Semanal'
                
            },
            subtitle: {
                text: 'Biomar'
                
            },
            xAxis: {
                categories: ['24', '25', '26', '27', '28', '29']
            },
            yAxis: {
                title: {
                    text: 'Kg.'
                },
                plotLines: [{
                    value: 0,
                    width: 1,
                    color: '#808080'
                }]
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
                name: 'Pellet 1,9 mm',
                data: [1050, 2000, 0, 0, 0, 0]
            }, {
                name: 'Pellet 3 mm',
                data: [4025, 5000, 0, 0, 0, 0]
            }, {
                name: 'Pellet 4,5 mm',
                data: [12600, 10000, 0, 0, 0, 0]
            }, {
                name: 'Pellet 6,5 mm',
                data: [14350, 15000, 18000, 16450, 0, 0]
            }, {
                name: 'Pellet 9 mm',
                data: [25725, 28000, 27750, 20000, 0, 0]
            }]
        });

     $('#calendar-container').datepicker({
      numberOfMonths: 3,
      onSelect: function () {
      /* the jQuery UI code sets the width on the element, we need to remove this any time jQuery tries to reset it */
      $('#calendar-container .ui-datepicker-inline').css('width', '');
      }
   });
   
   /* I like the rounded corners on the calendar headers so... */
   $('#calendar-container .ui-datepicker-header').removeClass('ui-corner-right').removeClass('ui-corner-left');
   $('#calendar-container .ui-datepicker-header').addClass('ui-corner-right').addClass('ui-corner-left');
        
    });
    
</script>


  
@stop