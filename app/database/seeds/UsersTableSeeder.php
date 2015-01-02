<?php 

  Class UsersTableSeeder extends Seeder {

     public function run()
     {
     	DB::table('users')->delete();

     	User::create(array(
     		         'email' => 'member@email.com',
     		         'password' => Hash::make('password'),
 					 'name' => 'John Doe',
 					 'admin' => 0
     		         ));

     	User::create(array(
     		         'email' => 'aumberto.jimenez@gmail.com',
     		         'password' => Hash::make('dulcita'),
 					 'name' => 'Aumberto',
 					 'admin' => 1
     		         ));
     }

  }

 ?>