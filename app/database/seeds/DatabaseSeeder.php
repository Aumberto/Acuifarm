<?php

class DatabaseSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Eloquent::unguard();
        
		$this->call('UsersTableSeeder');
		$this->command->info('Datos insertados en la tabla Users');

		$this->call('AuthorsTableSeeder');
		$this->command->info('Datos insertados en la tabla Authors');

		$this->call('BooksTableSeeder');
		$this->command->info('Datos insertados en la tabla Books');

		$this->call('ProveedoresTableSeeder');
		$this->command->info('Datos insertados en la tabla Proveedores');

		$this->call('PiensoTableSeeder');
		$this->command->info('Datos insertados en la tabla Piensos');

		$this->call('PelletsTableSeeder');
		$this->command->info('Datos insertados en la tabla Pellets');

		$this->call('GranjasTableSeeder');
		$this->command->info('Datos insertados en la tabla Granjas');

		$this->call('JaulasTableSeeder');
		$this->command->info('Datos insertados en la tabla Jaulas');

		$this->call('LotesTableSeeder');
		$this->command->info('Datos insertados en la tabla Lotes');
        
        $this->call('McrecimientoTableSeeder');
		$this->command->info('Datos insertados en la tabla m_crecimiento');

		$this->call('MtemperaturaTableSeeder');
		$this->command->info('Datos insertados en la tabla m_temperatura');
		

	}

}
