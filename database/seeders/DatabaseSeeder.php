<?php

namespace Database\Seeders;

use DB;
use Illuminate\Support\Facades\Hash;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        DB::table('mainadmin')->truncate();
        $this->call(MainAdminSeeder::class);
        $this->call(Cms::class);
        $this->call(TblCityTableSeeder::class);
        $this->call(TblFaqTableSeeder::class);
        $this->call(TblPlanTableSeeder::class);
    }
}