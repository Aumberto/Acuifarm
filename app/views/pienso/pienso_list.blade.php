@extends('layout.main_layout')

@section('content')
  

<div class="contenedor">
	<h1>Listado de Piensos</h1>
  <p>{{HTML::link('/pienso/add', 'Añadir Pienso')}} </p>
  <table>
  	<thead>
  		<tr>
  		 <th>Código</th>
  		 <th>Nombre</th>
  		 <th>Tamaño de pellet</th>
       <th>Precio</th>
       <th>Proveedor</th>
  		 <th>Acciones</th>
       
  	    </tr>
  	</thead>
  	<tbody>
  		@foreach($piensos as $pienso)
  		<tr>
  			<td>{{$pienso->codigo}}</td>
  			<td>{{$pienso->nombre}} </td>
  			<td>{{$pienso->pellet->diametro}} mm</td>
        <td>{{$pienso->precio}} €</td>
  			<td>{{$pienso->proveedor->nombre}} </td>
  			<td>
  				{{Html::link('pienso/ver/'. $pienso->id, 'Ver', array('class' =>'btn btn-mini btn-primary'))}}
          {{Html::link('pienso/edit/'. $pienso->id, 'Editar', array('class' =>'btn btn-mini btn-primary'))}}
  				{{Html::link('pienso/delete/'. $pienso->id, 'Eliminar', array('class'=>'btn btn-mini btn-primary'))}}
        </td>
  		</tr>
  		@endforeach
  	</tbody>

  </table>
{{ $piensos->links()}}

</div>




  
@stop