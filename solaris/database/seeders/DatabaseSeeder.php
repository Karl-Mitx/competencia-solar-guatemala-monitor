<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(DepartmentSeeder::class);
        if (config('solaris.demo_data')) {
            $this->call(DemoSeeder::class);
        }
        if (config('solaris.admin_email') && config('solaris.admin_password')) {
            User::updateOrCreate(['email' => config('solaris.admin_email')], ['name' => 'Administrador Solaris', 'password' => config('solaris.admin_password'), 'role' => 'admin']);
        }
    }
}
