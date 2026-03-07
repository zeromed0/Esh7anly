<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::updateOrCreate(
    ['email' => 'test@example.com'], // المفتاح الفريد
    [
        'name' => 'Test User',
        'email_verified_at' => now(),
        'password' => bcrypt('12345678'),
        'remember_token' => Str::random(10),
        'is_admin' => '1',
    ]
);

        // ✅ هنا $this صحيح
        $this->call([
            SettingsSeeder::class,
        ]);
        
    }

    

}

