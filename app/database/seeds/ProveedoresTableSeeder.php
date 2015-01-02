<?php 

Class ProveedoresTableSeeder extends Seeder {

     public function run()
     {
     	DB::table('proveedores_pienso')->delete();

     	Proveedorpienso::create(array(
     		         'nombre' => 'SKRETTING ESPAÑA, S.A.',
     		         'email' => 'info.spain@skretting.com',
 					 'web' => 'www.skretting.es',
 					 'telefono' => '947400301',
 					 'fax' => '947423053',
 					 'direccion' => 'CTRA.LA ESTACION S/N',
 					 'cp' => '09620',
 					 'localidad' => 'BURGOS',
 					 'pais' => 'España'
     		         ));

     	Proveedorpienso::create(array(
     		         'nombre' => 'BIOMAR IBERIA, S.A.',
     		         'email' => 'biomariberia@biomar.com',
 					 'web' => 'www.biomariberia.com',
 					 'telefono' => '979761404',
 					 'fax' => '979780337',
 					 'direccion' => 'A-62 KM.99 apdo. 16',
 					 'cp' => '34210',
 					 'localidad' => 'PALENCIA',
 					 'pais' => 'España'
     		         ));

          Proveedorpienso::create(array(
                        'nombre' => 'DIBAQ DIPROTEG S.A.',
                        'email' => 'acuicultura@bdibaq.com',
                          'web' => 'www.dibaq.com',
                          'telefono' => '921574286',
                          'fax' => '921574516',
                          'direccion' => 'Ctra. Nacional Navalmanzano a Fuentepelayo, km. 4300',
                          'cp' => '40260',
                          'localidad' => 'Segovia',
                          'pais' => 'España'
                        ));

     	
     }

  }

 ?>