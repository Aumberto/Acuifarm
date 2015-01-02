@extends('layout.main_layout')

@section('content')
  

<div class="container">

	<h1>Listado de Proveedores</h1>
  <p>{{HTML::link('/proveedor/add', 'Añadir Proveedor')}} </p>
  <table class="table table-hover">
  	<thead>
  		<tr>
  		 <th>Proveedor</th>
  		 <th><i class="icon-shopping-cart"></i> Teléfono</th>
  		 <th><i class="icon-shopping-cart"></i> Fax</th>
  		 <th><i class="icon-envelope"></i> E-mail</th>
  		 <th>Acciones</th>
  	    </tr>
  	</thead>
  	<tbody>
  		@foreach($proveedores as $proveedor)
  		<tr>
  			<td>{{$proveedor->nombre}}</td>
  			<td>{{$proveedor->telefono}} </td>
  			<td>{{$proveedor->fax}} </td>
  			<td>{{$proveedor->email}} </td>
  			<td>
  				<button class="btn btn-mini btn-primary" type="button">Ver </button>
  				<button class="btn btn-mini btn-primary" type="button">Editar</button>
  				{{Html::link('proveedor/delete/'. $proveedor->id, 'Eliminar', array('class'=>'btn btn-mini btn-primary'))}}
  				<button class="btn btn-mini btn-primary" type="button">Listados de Pienso</button>
  			</td>
  		</tr>
  		@endforeach
  	</tbody>

  </table>
  {{ $proveedores->links()}}

</div>




  
@stop