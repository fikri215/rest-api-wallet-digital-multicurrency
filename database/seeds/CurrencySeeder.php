<?php

use Illuminate\Database\Seeder;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('currencies')->insert([
            'id' => 1,
            'name' => 'IDR'
        ]);

        DB::table('currencies')->insert([
            'id' => 2,
            'name' => 'USD'
        ]);
    }
}
