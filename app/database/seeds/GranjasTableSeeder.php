<?php 

Class GranjasTableSeeder extends Seeder {

     public function run()
     {
     	DB::table('granjas')->delete();

     	Granja::create(array(
     		         'nombre' => 'Melenara'
 			         ));

        Granja::create(array(
                     'nombre' => 'Procria'
                     ));

     	     	
     }

  }

 ?>