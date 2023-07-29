<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\UserSeeder;
use Database\Seeders\CategorySeeder;
use Database\Seeders\MahsulotTolaSeeder;
use Database\Seeders\IshlabChiqarishSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(UserSeeder::class);
        $this->call(CategorySeeder::class);
        $this->call(IshlabChiqarishSeeder::class);
        $this->call(MahsulotTolaSeeder::class);
    }
}
