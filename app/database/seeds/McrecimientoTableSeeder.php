<?php 

Class McrecimientoTableSeeder extends Seeder {

     public function run()
     {
     	DB::table('m_crecimiento')->delete();

     	MCrecimiento::create(array(
     		         'nombre' => 'uno',
                     'descripcion'  => 'otro',
                     'SGR_a_lub'    => 0.07,
                     'SGR_Peso_max' => 5000.00,
                     'SGR_T_cero'   => 10.00,
                     'SGR_T_max'    => 30.00,
                     'SGR_Seno'     => 1.70,
                     'FCR_cte'      => 0.90,
                     'FCR_a'        => 0.17117000
 			         ));
     	     	
     }

  }

 ?>