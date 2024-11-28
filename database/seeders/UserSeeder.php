<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //

        /*f (!User::where('email', 'admin@admin.com')->first()) {
            User::create([
                'name' => 'admin',
                'email' => 'admin@admin.com',
                'level' => 'SUPER_ADM',

                'password' => Hash::make('051161Tu', ['rounds' => 12]),

            ]);
        }*/
    }
}
