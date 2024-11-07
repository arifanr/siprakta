<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run()
    {
        // User::factory(10)->create();

        User::create([
            'name' => 'student',
            'username' => 'Student',
            'password' => Hash::make('testPassword123'),
            'status' => 1,
            'flag_delete' => 1,
            'login_attempt' => 0,
            'created_by' => 'system',
        ]);
    }
}
