<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            'fullname' => 'Azizbek',
            'username' => 'azizdev',
            'phone' => '+998991903704',
            'address' => 'Tashkent, Uzbekistan',
            'product_number' => 45,
            'additionalAddress' => 'mn : Toshkent shaxar Yunusobod tuman',
            'phone_verified_at' => now(),
            'password' => Hash::make('password'),
            'role' => 1,
            'remember_token' => Str::random(10),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
