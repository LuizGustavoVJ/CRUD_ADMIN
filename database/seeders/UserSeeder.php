<?php

namespace Database\Seeders;

use App\Models\Access\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name' => 'User_1020304050',
            'email' => 'dev@empiricus.com',
            'password' => bcrypt('1020304050'),
            'city' => 'Maranguape',

        ]);
    }
}
