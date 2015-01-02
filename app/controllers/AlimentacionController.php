<?php 


	class AlimentacionController extends BaseController{

		public function getIndex()
		{
			Excel::load('public/files/estadillo_procria.xls', function($file) {

    // modify stuff
				$result = $file->first();
				$result->cells('E68', function($cell) {
                    
                    $cell = 800;
    // manipulate the cell

});

})->export('xls');
			//return View::make('alimentacion.listado');
		}
	}
 ?>