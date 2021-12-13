<?php

namespace Database\Seeders;
use DB;

use Illuminate\Database\Seeder;

class ElectionDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // php artisan db:seed --class=ElectionDataSeeder
        
        $path = base_path().'/database/seeders/sql/geseed.sql';
        $sql = file_get_contents($path);
        DB::unprepared($sql);
    }
}
