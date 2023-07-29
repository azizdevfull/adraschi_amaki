<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class MahsulotTolaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('mahsulot_tolas')->insert([
            [
                'name' => 'Paxta',
                'rus_name' => 'Хлопок',
                'en_name' => 'Cotton',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Sintetika',
                'rus_name' => 'Синтетический',
                'en_name' => 'Synthetic',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Palister',
                'rus_name' => 'Палистер',
                'en_name' => 'Palister',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Ipak',
                'rus_name' => 'Шелк',
                'en_name' => 'Silk',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
