<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        // Create roles
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'label' => 'Administrator']);
        $operatorRole = Role::firstOrCreate(['name' => 'operator', 'label' => 'Operator']);


        // Create the admin user
        User::firstOrCreate([
            'email' => 'admin@regtix.id',
        ], [
            'name' => 'Administrator',
            'password' => Hash::make('regtix@123'),
            'role_id' => $adminRole->id,  // Set the role_id to the admin role
        ]);

        // Create the operator user
        User::firstOrCreate([
            'email' => 'operator1@regtix.id',
        ], [
            'name' => 'Operator 1',
            'password' => Hash::make('regtix@123op'),
            'role_id' => $operatorRole->id,  // Set the role_id to the operator role
        ]);
    }
}
