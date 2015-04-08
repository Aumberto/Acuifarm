@extends('layout.main_layout')

@section('content')

<div class="container">
	
	<h2>Nuevo Pedido</h1>
	{{Form::open(array('url' => 'pedido/add', 'class' =>'form-horizontal'))}}
    <div class="form-group">
      {{Form::label('num_pedido', 'Pedido num:', array('class' => 'col-sm-3 control-label'))}}
      <div class="col-sm-3">
        {{Form::text('num_pedido', '', array('class' => 'form-control input-sm'))}}
      </div>
    </div>

    <div class="form-group">
     {{Form::label('proveedor_id', 'Proveedor:', array('class' => 'col-sm-3 control-label'))}}
       <div class="col-sm-3">
   	     <select name="proveedor_id" id="proveedor_id" class="form-control input-sm">
   		     @foreach($proveedores as $proveedor)
             <option value="{{$proveedor->id}}">{{$proveedor->nombre}}</option>
   		     @endforeach
   	     </select>
       </div>
    </div>

    <div class="form-group">
      {{Form::label('num_contenedor', 'Número Contenedor:', array('class' => 'col-sm-3 control-label'))}}
      <div class="col-sm-3">
        {{Form::text('num_contenedor', '', array('class' => 'form-control input-sm'))}}
      </div>
    </div>

    <div class="form-group">
      {{Form::label('fecha_pedido', 'Fecha Pedido:', array('class' => 'col-sm-3 control-label'))}}
      <div class="col-sm-3">
      	<input type="text" name="fecha_pedido" id="fecha_pedido" class='form-control input-sm'>
        
      </div>
    </div>

    <div class="form-group">
      {{Form::label('fecha_carga', 'Fecha Carga:', array('class' => 'col-sm-3 control-label'))}}
      <div class="col-sm-3">
        <input type="text" name="fecha_carga" id="fecha_carga" class='form-control input-sm'>
      </div>
    </div>

    <div class="form-group">
      {{Form::label('fecha_descarga', 'Fecha Descarga:', array('class' => 'col-sm-3 control-label'))}}
      <div class="col-sm-3">
        <input type="text" name="fecha_descarga" id="fecha_descarga" class='form-control input-sm'>
      </div>
    </div>

    <div class="form-group">
      <div class="row">
        <div class="col-md-6 col-md-offset-3">
            {{Form::submit('Guardar', array('class' =>'btn btn-primary'))}}
            {{HTML::link('/pedido', 'Cancelar',  array('class' =>'btn btn-primary'))}}
        </div>
      </div>
    </div>
    <input id="fecha_llegada" name="fecha_llegada" type="hidden" value="">

	{{ Form::close() }}

</div>
<script>
    $(function() 
    {
      



      $("#fecha_carga").change(function()
      {
        //alert('Cambiamos la fecha de carga');
        // La fecha de llegada automáticamente cambia al primer viernes siguiente a la fecha de carga
        var fecha = $(this).val()
        var elem = fecha.split('-');
        dia = elem[0];
        mes = elem[1];
        año = elem[2];
        //alert(dia);
        //alert(mes);
        var Hoy = new Date(año + '/' + mes + '/' + dia);
        //alert(Hoy.getDay())
        if (Hoy.getDay() < 5)
         {
           //alert(Hoy.getDay())
           var numdias = (5 - Hoy.getDay())+8+2;
           //var nuevafecha = new Date(Hoy + milisegundos);
           
         }else{
           var numdias = (6 - Hoy.getDay())+6+8+2;
         }
         nuevafecha = new Date(Hoy.setDate(Hoy.getDate()+numdias));
         var dd = nuevafecha.getDate();
         var mm = nuevafecha.getMonth() + 1;
         var yyyy = nuevafecha.getFullYear();
         if (dd<10){
            dd = '0'+dd;
          }
         if (mm<10){
            mm = '0' +mm;
          }
         //alert(dd+ '-' +mm+ '-' +yyyy );
         $("#fecha_descarga").val(dd+ '-' +mm+ '-' +yyyy);
         $("#fecha_descarga").change();

      });

     $("#fecha_descarga").change(function()
      {
        //alert('Cambiamos la fecha de carga');
        // La fecha de llegada automáticamente cambia al primer viernes siguiente a la fecha de carga
        var fecha = $(this).val()
        var elem = fecha.split('-');
        dia = elem[0];
        mes = elem[1];
        año = elem[2];
        //alert(dia);
        //alert(mes);
        
         nuevafecha = new Date(Hoy.setDate(Hoy.getDate()-6));
         var dd = nuevafecha.getDate();
         var mm = nuevafecha.getMonth() + 1;
         var yyyy = nuevafecha.getFullYear();
         if (dd<10){
            dd = '0'+dd;
          }
         if (mm<10){
            mm = '0' +mm;
          }
         //alert(dd+ '-' +mm+ '-' +yyyy );
         $("#fecha_llegada").val(dd+ '-' +mm+ '-' +yyyy);
         

      });

      
    
    });
  </script>


@stop