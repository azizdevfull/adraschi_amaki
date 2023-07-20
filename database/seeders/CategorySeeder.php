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
                'en_name' => 'Electrnic',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Uy Jihozlari',
                'rus_name' => 'Товары для дома',
                'en_name' => 'Things',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Oziq-ovqat mahsulotlari',
                'rus_name' => 'Продукты питания',
                'en_name' => 'Foods',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Sport va Mashina Jangi',
                'rus_name' => 'Спорт и автомобильная техника',
                'en_name' => 'Sporn and Machine',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Moda va Krasota',
                'rus_name' => 'Мода и красота',
                'en_name' => 'Fashion',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
