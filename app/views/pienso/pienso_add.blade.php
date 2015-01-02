@extends('layout.main_layout')

@section('content')
 <div class="container">
 	<h1>Nuevo Pienso</h1>
 	{{Form::open(array('url' => 'pienso/add', 'class' =>'form-horizontal'))}}

  <div class="control-group">
   {{Form::label('codigo', 'Código', array('class' => 'control-label'))}}
   <div class="controls">
     {{Form::text('codigo')}}
   </div>
  </div>

  <div class="control-group">
   {{Form::label('nombre', 'Nombre', array('class' => 'control-label'))}}
   <div class="controls">
     {{Form::text('nombre')}}
   </div>
  </div>

  <div class="control-group">
   {{Form::label('proveedor_id', 'Proveedor', array('class' => 'control-label'))}}
   <div class="controls">
   	<select name="proveedor_id" id="proveedor_id">
   		@foreach($proveedores as $proveedor)
          <option value="{{$proveedor->id}}">{{$proveedor->nombre}}</option>
   		@endforeach
   	</select>
     
   </div>
  </div>

  <div class="control-group">
   {{Form::label('diametro_pellet_id', 'Diámetro Pellet', array('class' => 'control-label'))}}
   <div class="controls">
     <select name="diametro_pellet_id" id="diametro_pellet_id">
     	
     </select>
   </div>
  </div>

  <div class="control-group">
   {{Form::label('precio', 'Precio', array('class' => 'control-label'))}}
   <div class="controls">
     {{Form::text('precio')}}
   </div>
  </div>

  <div class="control-group">
   <div class="controls">
     {{Form::submit('Guardar', array('class' =>'btn'))}}
   </div>
  </div>


{{Form::close()}}
 </div>

 <script>
  $(function() {

    //Definimos la función que nos devolverá las jaulas de una granja
    
      var getPellets = function()
      {
        $.post('/acuifarm/public/ajax/pellets','proveedor=' + $("#proveedor_id").val(),function(datos){
            $("#diametro_pellet_id").html('');
            for(var i = 0; i < datos.length; i++)
            {
              $("#diametro_pellet_id").append('<option value="' + datos[i].id + '">' + datos[i].diametro + '</option>');
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
           $('#diametro_pellet_id').html('');
        }else
        {
           getPellets();
        }
      
    });

  });
</script>

@stop