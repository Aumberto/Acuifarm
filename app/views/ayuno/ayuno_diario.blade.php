@extends('layout.main_layout')

@section('content')


  <div class="container">
  	<h1>Ayunos para el día {{Form::open(array('url' => 'ayunos', 'class' =>'form-horizontal'))}} 
      <input type="text" name="fecha_pedido" id="fecha_pedido" value="{{date("d-m-Y",strtotime($fecha))}}"> 
      {{Form::submit('...', array('class' =>'btn'))}}
      {{Form::close()}}
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
  <script>
    $(function() 
    {
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
           $.post('/acuifarm/public/ajax/ayunos',
                   'id=' + id + '&ayuno=' + ayuno,
                   function(data)
                   {
                    //alert(data);
                   },
                   'json');
           
      });
    });
  </script>
  

@stop