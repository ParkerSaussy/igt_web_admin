<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class TblPlanTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('tbl_plan')->delete();
        
        \DB::table('tbl_plan')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'normal plan',
                'description' => 'lorem',
                'price' => '100.00',
                'duration' => 2,
                'image' => 'default_plan.png',
                'is_active' => 1,
                'created_at' => '2023-08-25 09:11:16',
                'updated_at' => '2023-08-25 11:49:14',
                'type' => 'normal',
                'apple_pay_key' => '1',
            ),
            1 => 
            array (
                'id' => 3,
                'name' => 'Test Cycle 1wad',
                'description' => 'lasd',
                'price' => '10.00',
                'duration' => NULL,
                'image' => '1692967254.jpg',
                'is_active' => 0,
                'created_at' => '2023-08-25 12:40:54',
                'updated_at' => '2023-08-25 12:44:34',
                'type' => 'singal',
                'apple_pay_key' => '12345',
            ),
            2 => 
            array (
                'id' => 4,
                'name' => 's',
                'description' => 's',
                'price' => '99.10',
                'duration' => NULL,
                'image' => '1692967440.jpg',
                'is_active' => 1,
                'created_at' => '2023-08-25 12:44:00',
                'updated_at' => '2023-08-25 12:44:00',
                'type' => 'singal',
                'apple_pay_key' => '1',
            ),
        ));
        
        
    }
}