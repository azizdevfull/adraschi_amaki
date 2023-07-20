<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RegionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('regions')->insert([
            [
                'name' => 'Andijon viloyati',
                'rus_name' => 'Андижанская область',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Buxoro viloyati',
                'rus_name' => 'Бухарская область',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Farg\'ona viloyati',
                'rus_name' => 'Ферганская область',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Jizzax viloyati',
                'rus_name' => 'Джизакская область',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Namangan viloyati',
                'rus_name' => 'Наманганская область',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Navoiy viloyati',
                'rus_name' => 'Навоийская область',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Qashqadaryo viloyati',
                'rus_name' => 'Кашкадарьинская область',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Samarqand viloyati',
                'rus_name' => 'Самаркандская область',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Sirdaryo viloyati',
                'rus_name' => 'Сырдарьинская область',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Surxondaryo viloyati',
                'rus_name' => 'Сурхандарьинская область',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Toshkent viloyati',
                'rus_name' => 'Ташкентская область',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Xorazm viloyati',
                'rus_name' => 'Хорезмская область',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
