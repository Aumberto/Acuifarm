<?php 
Class MtemperaturaTableSeeder extends Seeder {

     public function run()
     {
     	DB::table('m_temperatura')->delete();

     	MTemperatura::create(array(
     		         'nombre'       => 'uno',
                     'descripcion'  => 'otro',
                     'enero'        => 19.02,
                     'febrero'      => 18.65,
                     'marzo'        => 18.47,
                     'abril'        => 19.10,
                     'mayo'         => 19.90,
                     'junio'        => 20.81,
                     'julio'        => 21.99,
                     'agosto'       => 22.76,
                     'septiembre'   => 22.80,
                     'octubre'      => 22.90,
                     'noviembre'    => 22.39,
                     'diciembre'    => 20.83
 			         ));
     	     	
     }

  }

 ?>