<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('categories')->insert([
            [
                'name' => 'Atlas',
                'rus_name' => 'Атлас',
                'en_name' => 'Atlas',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Adras',
                'rus_name' => 'Адрас',
                'en_name' => 'Adras',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Dahmal',
                'rus_name' => 'Дахмал',
                'en_name' => 'Dahmal',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Cho\'pon',
                'rus_name' => 'Чопон',
                'en_name' => 'Cho\'pon',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Milliy Ko\'ynaklar',
                'rus_name' => 'Национальные рубашки',
                'en_name' => 'National Dresses',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Boshqalar',
                'rus_name' => 'Другие',
                'en_name' => 'Others',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
