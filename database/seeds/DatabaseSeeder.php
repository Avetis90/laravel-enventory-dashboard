<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(UserTableSeeder::class);
		
		//Seed the countries
		$this->call(CountriesSeeder::class);
		$this->command->info('Seeded the countries!'); 
    }
}
