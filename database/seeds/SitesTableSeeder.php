<?php

use Illuminate\Database\Seeder;

class SitesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      DB::table('sites')->insert([
          'short_name' => 'KIM',
          'full_name' => 'KARYA INDAH MULTIGUNA',
      ]);

      DB::table('sites')->insert([
          'short_name' => 'MBI',
          'full_name' => 'MULTIBOX INDAH',
      ]);
    }
}
