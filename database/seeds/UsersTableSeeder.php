<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = new \App\User();
        $user->name = 'Super Admin';
        $user->username = 'super';
        $user->password = bcrypt('123456');
        $user->save();
    }
}
