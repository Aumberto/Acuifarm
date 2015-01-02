<?php 

	class SemanaController extends BaseController{

		public function getIndex(){
			$semanas = Semana::paginate();
			return View::make('semana.semana_list')->with('semanas', $semanas);
		}

		public function getAdd(){

            $fecha_inicial="2009-12-28";
            $dt_ini = new DateTime($fecha_inicial);
            $dt_fin = new DateTime($fecha_inicial);
            /*
            echo $dt->format('Y-m-d');
            echo " ";
            echo $dt->format('W');
            echo " ";
            */
            for ($i = 1; $i <= 1000; $i++) {
    			
    			$fecha_ini = $dt_ini->format('Y-m-d');
    			//echo "Fecha Inicial: " . $fecha_ini;
    			//echo " ";
    			
    			$dt_ini->modify('+6 day');
    			$semana = $dt_ini->format('W');
    			$anyo = $dt_ini->format('Y');
    			$fecha_fin = $dt_ini->format('Y-m-d');
    			//echo "Fecha Final: " . $fecha_fin;
    			//echo " ";
    			$dt_ini->modify('+1 day');
    			
    			//echo "Semana: " . $semana;
    			//echo " ";
    			//echo "Año: " . $anyo;
    			//echo " ";
                
    			Semana::create(
				array(
					'year' => $anyo,
					'week' => $semana,
					'first_day' => $fecha_ini,
					'last_day' => $fecha_fin
					));
                
            }



            
            return Redirect::to('semana');

           
		}
	}
 ?>