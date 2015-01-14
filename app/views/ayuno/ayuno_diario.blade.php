@extends('layout.main_layout')

@section('content')


  <div class="container">
  	<h1>Ayunos para el día
      <form action="ayunos" id="formulario_ayuno">
      <input type="text" name="fecha_pedido" id="fecha_pedido" value="{{date("d-m-Y",strtotime($fecha))}}"> 
      </form>
      
    </h1>
    <div class="datos_reales">
    <table>
      @foreach($datos as $dato)
      <tr>
        <td>{{$dato->site}}</td>
        <td>{{$dato->unitname}}</td>
        <td>{{$dato->groupid}} </td> 
        <td>{{$dato->stock_count_ini}} </td>
        <td>{{$dato->stock_avg_ini}} gr. </td>
        <td><input type="checkbox" class='ayuno' value='{{$dato->ayuno}}' @if ($dato->ayuno == 1) checked="checked" @endif data-id='{{$dato->id}}'></td>
      </tr>
      @endforeach
    </table>
    </div>
  </div>
  <div id='cargando'><div id='imagen-cargando'>Actualizando <img src="/acuifarm/public/images/ajax-loader.gif"></div></div>
  <script>
    $(function() 
    {
      $("#fecha_pedido").change(function(){
        //alert($(this).val());
        $("#formulario_ayuno").submit();
        //$.post('/acuifarm/public/ayunos',
        //  $("#formulario_ayuno").serialize());
      });
      $("#cargando").css('display', 'none');
      $(".ayuno").click(function(){
           var id = $(this).attr('data-id');
           var ayuno;
           if ($(this).is(":checked")){
               //alert('Verdadero en el id ' + id );
               ayuno = 1;
           }
           else
           {
                //alert('falso en el id ' + id);
                ayuno = 0;
           }
           // Hacemos la petición por ajax
           //$.post('/acuifarm/public/ajax/ayunos',
           //        'id=' + id + '&ayuno=' + ayuno,
           //        function(data)
           //        {
                    //alert(data);
           //        },
           //        'json');
           $("#cargando").css('display', 'block');
           $.ajax({
            type: 'POST',
            url: '/acuifarm/public/ajax/ayunos',
            data: { id: id, ayuno: ayuno},
            datatype : 'JSON'
           })
           .done(function(){
            $("#cargando").css('display', 'none');
           });
           
      });
    });
  </script>
  

@stop