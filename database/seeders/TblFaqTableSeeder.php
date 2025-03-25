<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class TblFaqTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('tbl_faq')->delete();
        
        \DB::table('tbl_faq')->insert(array (
            0 => 
            array (
                'id' => 1,
                'question' => 'Test 1',
                'answer' => 'Hello Test how are&nbsp; ypou',
                'is_active' => 1,
                'created_at' => '2023-08-28 06:55:05',
                'updated_at' => '2023-08-28 09:55:52',
            ),
            1 => 
            array (
                'id' => 2,
                'question' => 'asda',
            'answer' => '<span style="color:#212529;font-family:\'open sans sans-serif\';font-size:medium;background-color:rgba(0, 0, 0, 0.05);">Hello Test how are&nbsp; ypou</span>',
                'is_active' => 1,
                'created_at' => '2023-08-28 07:08:18',
                'updated_at' => '2023-08-28 12:07:52',
            ),
            2 => 
            array (
                'id' => 3,
                'question' => 'How do i Get Started?',
                'answer' => '<p><strong style="margin:0px;padding:0px;font-family:\'Open Sans\', Arial, sans-serif;font-size:14px;text-align:justify;background-color:#ffffff;">Lorem Ipsum</strong><span style="font-family:\'Open Sans\', Arial, sans-serif;font-size:14px;text-align:justify;background-color:#ffffff;">&nbsp;is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.</span></p><p><span style="font-family:\'Open Sans\', Arial, sans-serif;font-size:14px;text-align:justify;background-color:#ffffff;"></span><a href="https://www.lipsum.com/" target="_blank">https://www.lipsum.com/</a></p><p>&nbsp;</p><p>&nbsp;</p>',
                'is_active' => 1,
                'created_at' => '2023-08-28 11:30:18',
                'updated_at' => '2023-08-28 12:02:12',
            ),
        ));
        
        
    }
}