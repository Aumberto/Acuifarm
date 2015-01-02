@extends('layout.main_layout')

@section('content')
<div class="container">
  @foreach($granjas as $granja)
     <h1>{{$granja->nombre}}</h1>
     @foreach($granja->jaulas as $jaula)
      <p>{{$jaula->nombre}} </p>
     @endforeach
  @endforeach
</div>   
@stop