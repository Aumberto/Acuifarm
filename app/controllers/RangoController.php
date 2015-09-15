<?php 

	Class RangoController extends BaseController{

		public function getIndex()
		{
			$cabecera_rangos = CabeceraRangos::orderby('nombre')->get();
			//$autor = $books->author;
            //echo $books;
            //echo $autor;
			return View::make('rangos.rango_list')->with('cabecera_rangos', $cabecera_rangos);
		}

		public function getVer($id){

            $cabecera_rango = CabeceraRangos::find($id);
     		$detalle_rango = DetalleRangos::where('cabecera_rango_id', '=', $id)
     		                              ->orderby('pm_min')
     		                              ->orderby('pm_transito')
     		                              ->orderby('pm_max')
     		                              ->get();
     
     		
  	 		// Obtenemos el listado de pellets

  	 		$pellets=Pellet::orderby('diametro')->orderby('proveedor_pienso_id')->get();

     		return View::make('rangos.rango_ver')
            		   ->with('cabecera_rango', $cabecera_rango)
            		   ->with('detalle_rango', $detalle_rango)
            		   ->with('pellets', $pellets);

        }

        public function getDeleteDetalle($id){
           $rangodetalle = DetalleRangos::find($id);
		   $id_rango = $rangodetalle->cabecera_rango_id;
		   $rangodetalle->delete();

		   return Redirect::to('rangos/cabecera/ver/'. $id_rango);
        }

        public function getDeleteCabecera($id){
           $rangocabecera = CabeceraRangos::find($id);
		   $rango_detalle = DetalleRangos::where('cabecera_rango_id', '=', $id);
		   $rango_detalle->delete();
		   $rangocabecera->delete();

		   return Redirect::to('rangos');
        }

        public function getAddDetalle(){
           $rango_id = Input::get('rango_id');
		   $tamanio_pellet_id = Input::get('tamanio_pellet_id');
		   $pm_min = Input::get('pm_min');
		   $pm_transito = Input::get('pm_transito');
		   $pm_max = Input::get('pm_max');

		   $tamanio_pellet = Pellet::find($tamanio_pellet_id);   
			
		   // Añadimos el detalle
		   DetalleRangos::create(
				                 array(
					                   'cabecera_rango_id' => $rango_id,
					                   'tamanio_pellet_id' => $tamanio_pellet_id,
					                   'pm_min' => $pm_min,
					                   'pm_max' => $pm_max,
					                   'pm_transito' => $pm_transito
					            ));
		   
		   return Redirect::to('rangos/cabecera/ver/'. $rango_id);
		}

		public function getAddCabecera(){
           
		   $rango_nombre = Input::get('rango_nombre');
		   $rango_descripcion = Input::get('rango_descripcion');
		   	
		   // Añadimos el detalle
		   CabeceraRangos::create(
				                 array(
					                   'nombre' => $rango_nombre,
					                   'descripcion' => $rango_descripcion
					            ));
		   $rango = CabeceraRangos::all();
		   return Redirect::to('rangos/cabecera/ver/'. $rango->last()->id);
		}
	}

 ?>