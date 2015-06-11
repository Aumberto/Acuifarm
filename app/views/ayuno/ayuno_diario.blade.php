@extends('layout.main_layout')

@section('content')


  <div class="container">
  	
      <form action="ayunos" id="formulario_ayuno" class="form-horizontal">
      <div class="form-group">  
        {{Form::label('fecha_pedido', 'Ayunos del día ', array('class' => 'col-sm-2 control-label'))}}
        <div class="col-sm-2">
      <input type="text" class="form-control input-sm" name="fecha_pedido" id="fecha_pedido" value="{{date("d-m-Y",strtotime($fecha))}}"> 
      </div>
      </div>
      </form>
      
   
    

@foreach($ayuno_granja as $ayuno)
<div class="table-responsive">
<h3>{{$ayuno['granja']}}</h3>
 <table class="table table-striped table-bordered">
    <thead>
      <tr>
        <th class="text-center">Jaula</th>
        <th class="text-center">Lote</th>
        <th class="text-center">Número de peces</th>
        <th class="text-center">Peso Medio</th>
        <th class="text-center">Ayuno</th>
      </tr>
    </thead>
    <tbody>
      @foreach($ayuno['ayuno_granja'] as $detalle_ayuno)
        <tr>
          <td class="text-center">{{$detalle_ayuno['unitname']}}</td>
          <td class="text-center">{{$detalle_ayuno['groupid']}}</td>
          <td class="text-center">{{$detalle_ayuno['stock_count_ini']}}</td>
          <td class="text-center">{{$detalle_ayuno['stock_avg_ini']}}</td>
          <td class="text-center"><input type="checkbox" class='ayuno' value='{{$detalle_ayuno['ayuno']}}' @if ($detalle_ayuno['ayuno'] == 1) checked="checked" @endif data-id='{{$detalle_ayuno['id']}}'></td>
        </tr>
      @endforeach
    </tbody>
</table>
</div>

@endforeach

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