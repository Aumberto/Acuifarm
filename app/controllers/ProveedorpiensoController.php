<?php 

  Class ProveedorpiensoController extends BaseController {

  	public function getIndex(){
  		$proveedores = Proveedorpienso::paginate();
  		return View::make('proveedor.proveedor_list')->with('proveedores', $proveedores);
  	}

  	public function addProveedor(){

  		Proveedorpienso::create(Input::all());
  		return Redirect::to('proveedor');
  	}

  	public function newProveedor(){
  		return View::make('proveedor.proveedor_add');
  	}

  	public function getDelete($id){

  		$proveedor = Proveedorpienso::find($id)->delete();
  		return Redirect::to('proveedor');
  	}
  }

 ?>