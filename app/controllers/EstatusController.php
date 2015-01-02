<?php 

	class EstatusController extends BaseController{

		public function getIndex()
		{
            /*
            $companyID = User::find($id)->company()->first()->id; 
            //Get all users and their assets 
            $companyUsers = User::ofCompanyUsers($companyID)->with("assets")->get();


            $chartArray["chart"] = array("type" => "column"); 
            $chartArray["title"] = array("text" => "Total User Assets"); 
            $chartArray["credits"] = array("enabled" => false); 
            $chartArray["navigation"] = array("buttonOptions" => array("align" => "right")); 
            $chartArray["series"] = array(); 
            $chartArray["xAxis"] = array("categories" => array()); 
            foreach ($companyUsers as $user) 
            { 
               $categoryArray[] = $user->first_name . " " . $user->last_name; 
               $assetCountArray[] = count($user->assets); 
               //don't show users who have no assets
               if (count($user->assets) != 0) 
               { 
                 $chartArray["series"][] = array("name" => $user->first_name . " " . $user->last_name, "data" => array(count($user->assets))); 
               } 
            } 
            $chartArray["xAxis"] = array("categories" => $categoryArray); 
            $chartArray["yAxis"] = array("title" => array("text" => "Total Assets")); 

            //return View::make('report/reports')->with("chartArray", $chartArray);
			return View::make('estatus.estatus')->with("chartArray", $chartArray); 

            $consumosporpellet = DB::select('Select week(consumos.fecha, 3), consumos.proveedor_id, consumos.proveedor, consumos.diametro_pienso, sum(consumos.cantidad) as cantidad from consumos group by week(consumos.fecha, 3), consumos.proveedor_id, consumos.proveedor, consumos.diametro_pienso');
            foreach ($consumosporpellet as $consumo)
             {
                //echo " Proveedor: " . $consumo->proveedor;
                //echo " Diametro Pellet: " . $consumo->diametro_pienso;
                //echo " Cantidad: " . $consumo->cantidad;
            }
            //var_dump($consumosporpellet); */
            return View::make('estatus.estatus');
		}
	}
 ?>