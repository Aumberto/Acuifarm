<?php 

	class PiensoController extends BaseController{

		public function getIndex(){
			// Listado de todos los piensos
			$piensos = Pienso::orderBY('proveedor_id')->orderBY('diametro_pellet_id')->paginate();

			return View::make('pienso.pienso_list')->with('piensos', $piensos);
		}	

		public function getNew(){
           $proveedores = Proveedorpienso::all();
           $pellets = Pellet::all();
           return View::make('pienso.pienso_add', array('proveedores'=>$proveedores, 'pellets'=>$pellets));
		}

		public function getAdd(){
           Pienso::create(Input::all());
  		   return Redirect::to('pienso');
		}

		public function getDelete($id){
			$pienso = Pienso::find($id)->delete();
			return Redirect::to('pienso');
		}
	}

 ?>