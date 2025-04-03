<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roleInput = [
            'name' => 'super_admin',
            'guard_name' => 'web'
        ];
        $roleExists = Role::where('name', $roleInput['name'])->exists();

        if (!$roleExists) {
            $this->command->info("Creating role for super admin...");
            Role::create($roleInput);
            $this->command->info("Super admin created successfully");
        } else {
            $this->command->info("Super admin already exists.");
        }

        $admin = User::role($roleInput['name'])->get();

        if (!$admin->count()) {
            $this->command->info("Adding user...");
            $userInput = [
                'name' => 'Super Admin',
                'email' => 'admin@admin.com',
                'password' => Hash::make('123456')
            ];
            $user = User::create($userInput);
            $user->assignRole($roleInput['name']);
            $this->command->info("Super admin created successfully");
        } else {
            $this->command->info("Super admin already exists.");
        }
    }
}
