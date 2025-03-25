<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use DB;
use Illuminate\Support\Facades\Hash;

class MainAdminSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    DB::table('mainadmin')->insert([
      'name' => 'Wire',
      'email' => 'admin@itsgotime.com',
      'password' => Hash::make('12345678'),
    ]);
  }
}
