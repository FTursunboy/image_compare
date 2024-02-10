<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Category;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

//        Setting::create([
//            'percent' => 94
//        ]);
//
//        Category::insert([
//            ['name' => 'Кольца'],
//            ['name' => 'Сережки'],
//            ['name' => 'Цепочки']
//        ]);
//
//

        User::create([
            'name' => '123',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('password')
        ]);
    }
}
