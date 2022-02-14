<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();

        // Seed super admin
        $admin = new Admin();
        $admin->email = "admin";
        $admin->password = password_hash("password", PASSWORD_DEFAULT);
        $admin->save();
    }
}
