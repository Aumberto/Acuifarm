@extends('layout.main_layout')

@section('content')
<div>
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
{{Form::open(array('url' => 'consumo/save/semanal', 'class' =>'form-inline'))}}        
     

<input type="hidden" name="week" value="{{$week}}" />
<input type="hidden" name="year" value="{{$year}}" />
<input type="hidden" name="id_lunes" value="{{$id_lunes}}" />
<input type="hidden" name="id_martes" value="{{$id_martes}}" />
<input type="hidden" name="id_miercoles" value="{{$id_miercoles}}" />
<input type="hidden" name="id_jueves" value="{{$id_jueves}}" />
<input type="hidden" name="id_viernes" value="{{$id_viernes}}" />
<input type="hidden" name="id_sabado" value="{{$id_sabado}}" />
<input type="hidden" name="id_domingo" value="{{$id_domingo}}" />
<tr>
	<td>{{Form::label('granja', $granja, array('class' => 'control-label'))}}</td>
	<td>{{Form::label('jaula', $jaula, array('class' => 'control-label'))}}</td>
	<td>
		<select name="lote_id" id="lote_id" class="input-small">
   		@foreach($lotes as $lote)
   		  @if($lote->id == $lote_id)
            <option value="{{$lote->id}}" selected>{{$lote->nombre}}</option>
   		  @else
   		    <option value="{{$lote->id}}">{{$lote->nombre}}</option>
   		  @endif
          
   		@endforeach
   	</select>
   </td>
   	<td>
   		<select name="proveedor_id" id="proveedor_id" class="input-medium">
   		@foreach($proveedores as $proveedor)
   		  @if($proveedor->nombre == $proveedorpienso)
            <option value="{{$proveedor->id}}" selected>{{$proveedor->nombre}}</option>
   		  @else
   		    <option value="{{$proveedor->id}}">{{$proveedor->nombre}}</option>
   		  @endif
          
   		@endforeach
   	</select>
   	</td>
    <td>
      {{$cantidad_recomendada_lunes}}
    </td>
    <td>
      {{$porcentaje_estrategia_lunes}}
    </td>
   	<td>
   		<input type="text" name="cantidad_lunes" id="cantidad_lunes" value={{$cantidad_lunes}} class="input-mini">
   	</td>
   	<td>
   		<select name="pellet_lunes" id="pellet_lunes" class="input-small">
   		@foreach($pellets as $pellet)
   		  @if($pellet->diametro == $pellet_lunes)
            <option value="{{$pellet->id}}" selected>{{$pellet->diametro}}</option>
   		  @else
   		    <option value="{{$pellet->id}}">{{$pellet->diametro}}</option>
   		  @endif
          
   		@endforeach
   		
   	</td>
    <td>
      Cantidad recomendada lunes
    </td>
    <td>
      Porcentaje Propuesta lunes
    </td>
   	<td>
   		<input type="text" name="cantidad_martes" id="cantidad_martes" value={{$cantidad_martes}} class="input-mini">
   	</td>
   	<td>
   		<select name="pellet_martes" id="pellet_martes" class="input-small">
   		@foreach($pellets as $pellet)
   		  @if($pellet->diametro == $pellet_martes)
            <option value="{{$pellet->id}}" selected>{{$pellet->diametro}}</option>
   		  @else
   		    <option value="{{$pellet->id}}">{{$pellet->diametro}}</option>
   		  @endif
          
   		@endforeach
   		
   	</td>
    <td>
      Cantidad recomendada lunes
    </td>
    <td>
      Porcentaje Propuesta lunes
    </td>
   	<td>
   		<input type="text" name="cantidad_miercoles" id="cantidad_miercoles" value={{$cantidad_miercoles}} class="input-mini">
   	</td>
   	<td>
   		<select name="pellet_miercoles" id="pellet_miercoles" class="input-small">
   		@foreach($pellets as $pellet)
   		  @if($pellet->diametro == $pellet_miercoles)
            <option value="{{$pellet->id}}" selected>{{$pellet->diametro}}</option>
   		  @else
   		    <option value="{{$pellet->id}}">{{$pellet->diametro}}</option>
   		  @endif
          
   		@endforeach
   		
   	</td>
    <td>
      Cantidad recomendada lunes
    </td>
    <td>
      Porcentaje Propuesta lunes
    </td>
   	   	<td>
   		<input type="text" name="cantidad_jueves" id="cantidad_jueves" value={{$cantidad_jueves}} class="input-mini">
   	</td>
   	<td>
   		<select name="pellet_jueves" id="pellet_jueves" class="input-small">
   		@foreach($pellets as $pellet)
   		  @if($pellet->diametro == $pellet_jueves)
            <option value="{{$pellet->id}}" selected>{{$pellet->diametro}}</option>
   		  @else
   		    <option value="{{$pellet->id}}">{{$pellet->diametro}}</option>
   		  @endif
          
   		@endforeach
   		
   	</td>
    <td>
      Cantidad recomendada lunes
    </td>
    <td>
      Porcentaje Propuesta lunes
    </td>
   	<td>
   		<input type="text" name="cantidad_viernes" id="cantidad_viernes" value={{$cantidad_viernes}} class="input-mini">
   	</td>
   	<td>
   		<select name="pellet_viernes" id="pellet_viernes" class="input-small">
   		@foreach($pellets as $pellet)
   		  @if($pellet->diametro == $pellet_viernes)
            <option value="{{$pellet->id}}" selected>{{$pellet->diametro}}</option>
   		  @else
   		    <option value="{{$pellet->id}}">{{$pellet->diametro}}</option>
   		  @endif
          
   		@endforeach
   		
   	</td>

   	<td>
   		<input type="text" name="cantidad_sabado" id="cantidad_sabado" value={{$cantidad_sabado}} class="input-mini">
   	</td>
   	<td>
   		<select name="pellet_sabado" id="pellet_sabado" class="input-small">
   		@foreach($pellets as $pellet)
   		  @if($pellet->diametro == $pellet_sabado)
            <option value="{{$pellet->id}}" selected>{{$pellet->diametro}}</option>
   		  @else
   		    <option value="{{$pellet->id}}">{{$pellet->diametro}}</option>
   		  @endif
          
   		@endforeach
   		
   	</td>

   	<td>
   		<input type="text" name="cantidad_domingo" id="cantidad_domingo" value={{$cantidad_domingo}} class="input-mini">
   	</td>
   	<td>
   		<select name="pellet_domingo" id="pellet_domingo" class="input-small">
   		@foreach($pellets as $pellet)
   		  @if($pellet->diametro == $pellet_domingo)
            <option value="{{$pellet->id}}" selected>{{$pellet->diametro}}</option>
   		  @else
   		    <option value="{{$pellet->id}}">{{$pellet->diametro}}</option>
   		  @endif
          
   		@endforeach
   		
   	</td>
	
	<td>{{Form::submit('Guardar', array('class' =>'btn'))}}</td>
</tr>

</table>
{{Form::close()}}
</div>
<script>
  $(function() {

    var getPellets = function()
      {
        $.post('/acuifarm/public/ajax/pellets','proveedor=' + $("#proveedor_id").val(),function(datos){
            $("#pellet_lunes").html('');
            $("#pellet_martes").html('');
            $("#pellet_miercoles").html('');
            $("#pellet_jueves").html('');
            $("#pellet_viernes").html('');
            $("#pellet_sabado").html('');
            $("#pellet_domingo").html('');
            for(var i = 0; i < datos.length; i++)
            {
              $("#pellet_lunes").append('<option value="' + datos[i].id + '">' + datos[i].diametro + '</option>');
              $("#pellet_martes").append('<option value="' + datos[i].id + '">' + datos[i].diametro + '</option>');
              $("#pellet_miercoles").append('<option value="' + datos[i].id + '">' + datos[i].diametro + '</option>');
              $("#pellet_jueves").append('<option value="' + datos[i].id + '">' + datos[i].diametro + '</option>');
              $("#pellet_viernes").append('<option value="' + datos[i].id + '">' + datos[i].diametro + '</option>');
              $("#pellet_sabado").append('<option value="' + datos[i].id + '">' + datos[i].diametro + '</option>');
              $("#pellet_domingo").append('<option value="' + datos[i].id + '">' + datos[i].diametro + '</option>');
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

    $('#proveedor_id').change(function(){
      if(!$('#proveedor_id').val())
        {
           $('#pellet_lunes').html('');
        }else
        {
           getPellets();
        }
      
    });

  });
</script>

@stop