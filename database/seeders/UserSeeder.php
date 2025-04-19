<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $users = array(
            [
                'email' => 'tapas@gmail.com',
                'password' => Hash::make('12345678'),
                'type' => '0',
            ],

            [
                'email' => 'ricky@gmail.com',
                'password' => Hash::make('12345678'),
                'type' => '1',
            ],
        );
        foreach($users as $user) {
            User::create($user);

        }
    }
}
