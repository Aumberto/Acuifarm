<?php 
   
   class AuthorsTableSeeder extends Seeder {

      public function run()
      {
      	DB::table('authors')->delete();

      	Author::create(array(
      					'name' => 'Lauren',
      					'surname' => 'Oliver'
      		));

      	Author::create(array(
      					'name' => 'Dan',
      					'surname' => 'Brown'
      		));

      	Author::create(array(
      					'name' => 'García',
      					'surname' => 'Marquez'
      		));
      }

   }

 ?>