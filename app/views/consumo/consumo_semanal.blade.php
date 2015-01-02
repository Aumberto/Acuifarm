@extends('layout.main_layout')

@section('content')

  
  {{Form::open(array('url' => 'consumo/semanal', 'class' =>'form-inline'))}}
   {{Form::label('anyo', 'Año', array('class' => 'control-label'))}}
     <select name="anyo" id="anyo" class="input-small">
       @for ($j=2014; $j <= 2016 ; $j++)
         @if($j== $anyo)
           <option value={{$j}} selected>{{$j}}</option>
         @else
           <option value={{$j}}>{{$j}}</option>
         @endif
       @endfor
     </select>
  {{Form::label('semana', 'Semana', array('class' => 'control-label'))}}
  <select name="semana" id="semana" class="input-mini">
      @for($i = 1; $i<54; $i++)
      @if ($i <= 9)
        {{$valor = "0" . $i}}
      @else
        {{$valor = $i}}
      @endif
        @if ($valor == $semana)
          <option value={{$valor}} selected>{{$valor}}</option>
        @else
          <option value={{$valor}}>{{$valor}}</option>
        @endif
       
       @endfor
     </select>
     {{Form::submit('Ver', array('class' =>'btn'))}}
  {{Form::close()}}

<table class="table table-hover">
  <thead>
  <tr>
    <td><h4 align="center">Granja</h4></td>
    <td><h4 align="center">Jaula</h4></td>
    <td><h4 align="center">Lote</h4></td>
    <td><h4 align="center">Proveedor</h4></td>
  @for ($j=0; $j<7 ; $j++)
  <td align="center" colspan="4">
    <h4 align="center">{{$dias_letras[$j]}}</h4><p align="center">{{$dias_numeros[$j]}}</p>
  </td>
  @endfor
</tr>
</thead>

  @foreach($consumos as $consumo)
  <tr>
     
     @for($i = 0; $i< count($consumo)-1; $i++)
        <td>{{$consumo[$i]}}</td>
     @endfor
     <td>
     {{HTML::link('consumo/delete/semanal/'. $consumo[count($consumo)-1], 'Eliminar', array('class'=>'btn btn-mini btn-primary'))}}
     {{HTML::link('consumo/edit/semanal/'. $consumo[count($consumo)-1], 'Modificar', array('class'=>'btn btn-mini btn-primary'))}}
     </td>
  </tr>
  
     
  @endforeach

</table>  

  


  {{Form::open(array('url' => 'consumo/add', 'class' =>'form-inline'))}}
     <input type="hidden" name="anyo" value="{{$anyo}}" /> 
     <input type="hidden" name="semana" value="{{$semana}}" />   

     
      <select name="granja_id" id="granja_id" class="input-medium">
   	    <option value="">Granja..</option>
        @foreach($granjas as $granja)
          <option value="{{$granja->id}}">{{$granja->nombre}}</option>
   	    @endforeach
   	  </select>

   	  
      <select name="jaula_id" id="jaula_id" class="input-small">
   	    
       <!-- @foreach($jaulas as $jaula)
          <option value="{{$jaula->id}}">{{$jaula->nombre}}</option>
   	    @endforeach
        -->
        <option value=""></option>
   	  </select>

   	  <select name="lote_id" id="lote_id" class="input-small">
   	    @foreach($lotes as $lote)
          <option value="{{$lote->id}}">{{$lote->nombre}}</option>
   	    @endforeach
   	  </select>

     <select name="proveedor_id" id="proveedor_id" class="input-medium">
   	    <option value="">Proveedor..</option>
        @foreach($proveedores as $proveedor)
          <option value="{{$proveedor->id}}">{{$proveedor->nombre}} mm</option>
   	    @endforeach
   	  </select>

      <select name="pellet_id" id="pellet_id" class="input-medium">
   	    <!-- @foreach($pellets as $pellet)
          <option value="{{$pellet->id}}">{{$pellet->diametro}} mm</option>
   	    @endforeach
      -->
        <option value=""></option>
   	  </select>

    
    <input type="text", name="cantidad" class="input-small" placeholder="Cantidad">
    
    {{Form::submit('Añadir', array('class' =>'btn'))}}
 
{{Form::close()}}

   
<script>
  $(function() {

    //Definimos la función que nos devolverá las jaulas de una granja
    var getJaulas = function()
      {
        $.post('/acuifarm/public/ajax/jaulas','granja=' + $("#granja_id").val(),function(datos){
            $("#jaula_id").html('');
            for(var i = 0; i < datos.length; i++)
            {
              $("#jaula_id").append('<option value="' + datos[i].id + '">' + datos[i].nombre + '</option>');
            }   
            /*
            $("#jaula_id").children("option").each(function(indice, elemento)
            {
               //console.log($(elemento).text(), " ", indice, " ", elemento, " ");
               if ($(elemento).text() == $("#prueba").val())
               {
                 $(elemento).attr("selected", true);
                }

             });          
            */

                       
        }, 'json');
      }

      var getPellets = function()
      {
        $.post('/acuifarm/public/ajax/pellets','proveedor=' + $("#proveedor_id").val(),function(datos){
            $("#pellet_id").html('');
            for(var i = 0; i < datos.length; i++)
            {
              $("#pellet_id").append('<option value="' + datos[i].id + '">' + datos[i].diametro + '</option>');
            }   
            /*
            $("#jaula_id").children("option").each(function(indice, elemento)
            {
               //console.log($(elemento).text(), " ", indice, " ", elemento, " ");
               if ($(elemento).text() == $("#prueba").val())
               {
                 $(elemento).attr("selected", true);
                }

             });          
            */

                       
        }, 'json');
      }




    $('#granja_id').change(function(){
      if(!$('#granja_id').val())
        {
           $('#jaula_id').html('');
        }else
        {
           getJaulas();
        }
      
    });

    $('#proveedor_id').change(function(){
      if(!$('#proveedor_id').val())
        {
           $('#pellet_id').html('');
        }else
        {
           getPellets();
        }
      
    });

  });
</script>

@stop



   