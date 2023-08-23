<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class IshlabChiqarishSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('ishlab_chiqarish_turis')->insert([
            [
                'name' => 'Qo\'l Mehnati',
                'rus_name' => 'Ручной труд',
                'en_name' => 'Manual Labor',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Avtomat',
                'rus_name' => 'Автоматический',
                'en_name' => 'Automatic',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Yarim Tayyor',
                'rus_name' => 'Половина готовности',
                'en_name' => 'Half ready',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
