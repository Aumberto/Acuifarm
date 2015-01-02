<?php 

Class PelletsTableSeeder extends Seeder {

     public function run()
     {
     	DB::table('tamanio_pellets')->delete();

     	Pellet::create(array(
     		         'diametro' => 3,
     		         'pm_min' => 15,
 				     'pm_max' => 25,
 				     'transito' => 30,
                     'proveedor_pienso_id' =>1
 			         ));

     	Pellet::create(array(
                     'diametro' => 4.5,
                     'pm_min' => 25,
                     'pm_max' => 45,
                     'transito' => 50,
                     'proveedor_pienso_id' =>1
                     ));

        Pellet::create(array(
                     'diametro' => 1.5,
                     'pm_min' => 25,
                     'pm_max' => 45,
                     'transito' => 50,
                     'proveedor_pienso_id' =>1
                     ));

        Pellet::create(array(
                     'diametro' => 1.9,
                     'pm_min' => 25,
                     'pm_max' => 45,
                     'transito' => 50,
                     'proveedor_pienso_id' =>1
                     ));

        Pellet::create(array(
                     'diametro' => 6.5,
                     'pm_min' => 25,
                     'pm_max' => 45,
                     'transito' => 50,
                     'proveedor_pienso_id' =>1
                     ));

        Pellet::create(array(
                     'diametro' => 9,
                     'pm_min' => 25,
                     'pm_max' => 45,
                     'transito' => 50,
                     'proveedor_pienso_id' =>1
                     ));

     	
     }

  }

 ?>