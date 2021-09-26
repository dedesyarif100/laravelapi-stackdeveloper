<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class insert_records_in_users_table extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = [
            [
                'name' => 'amit',
                'email' => 'dede@mail.com',
                'password' => bcrypt('123456')
            ],
            [
                'name' => 'rian',
                'email' => 'rian@mail.com',
                'password' => bcrypt('123456')
            ],
        ];
        User::insert($users);
    }
}
