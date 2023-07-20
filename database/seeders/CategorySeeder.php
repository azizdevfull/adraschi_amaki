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
                'name' => 'Elektronika',
                'rus_name' => 'Электроника',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Uy Jihozlari',
                'rus_name' => 'Товары для дома',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Oziq-ovqat mahsulotlari',
                'rus_name' => 'Продукты питания',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Sport va Mashina Jangi',
                'rus_name' => 'Спорт и автомобильная техника',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Moda va Krasota',
                'rus_name' => 'Мода и красота',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
