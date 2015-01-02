<?php 

Class PiensoTableSeeder extends Seeder {

     public function run()
     {
     	DB::table('piensos')->delete();

     	Pienso::create(array(
     		            'codigo' => 'MP51536503',
     		            'nombre' => 'Efico YM 858 nº6.5 S25',
 				        'proveedor_id' => 2,
 				        'diametro_pellet_id' => 1,
                        'precio' => 0.99
 			            ));

        Pienso::create(array(
                        'codigo' => 'MP51536603',
                        'nombre' => 'Efico YM 858 nº9 S25',
                        'proveedor_id' => 2,
                        'diametro_pellet_id' => 1,
                        'precio' => 0.99
                        ));

        Pienso::create(array(
                        'codigo' => 'MP51536303',
                        'nombre' => 'Efico YM 858 nº3 S25',
                        'proveedor_id' => 2,
                        'diametro_pellet_id' => 1,
                        'precio' => 1
                        ));

        Pienso::create(array(
                        'codigo' => 'MP51536403',
                        'nombre' => 'Efico YM 858 nº4.5 S25',
                        'proveedor_id' => 2,
                        'diametro_pellet_id' => 1,
                        'precio' => 0.98
                        ));

     	Pienso::create(array(
                        'codigo' => 'MPL142032',
                        'nombre' => 'L-6 ACTIVE 3P',
                        'proveedor_id' => 1,
                        'diametro_pellet_id' => 2,
                        'precio' => 0.96
                        ));

        Pienso::create(array(
                        'codigo' => 'MPD140137',
                        'nombre' => 'L-4 ALTERNA 2P',
                        'proveedor_id' => 1,
                        'diametro_pellet_id' => 2,
                        'precio' => 0.97
                        ));

        Pienso::create(array(
                        'codigo' => 'MPL140141',
                        'nombre' => 'L-8 ALTERNA XL',
                        'proveedor_id' => 1,
                        'diametro_pellet_id' => 2,
                        'precio' => 0.94
                        ));

        Pienso::create(array(
                        'codigo' => 'MPD140184',
                        'nombre' => 'L-10 ALTERNA XXL',
                        'proveedor_id' => 1,
                        'diametro_pellet_id' => 2,
                        'precio' => 0.93
                        ));

        Pienso::create(array(
                        'codigo' => 'MPD140135',
                        'nombre' => 'L-2 ALTERNA 1P',
                        'proveedor_id' => 1,
                        'diametro_pellet_id' => 2,
                        'precio' => 0.98
                        ));

     	
     }

  }

 ?>