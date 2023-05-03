<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use App\Enum\RoleEnum;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $customerRoleID = Role::where('role_name', RoleEnum::Customer->value)->first()->id;
        $adminRoleID = Role::where('role_name', RoleEnum::Admin->value)->first()->id;
        $data = [
            [
                'first_name' => 'minh',
                'last_name' => 'bui',
                'email' => 'minh.bui@TestEmail.com',
                'password' =>  Hash::make('password_minh'),
                'role_id' => $customerRoleID
            ],
            [
                'first_name' => 'first',
                'last_name' => 'customer',
                'email' => 'first.customer@TestEmail.com',
                'password' =>  Hash::make('password_first'),
                'role_id' => $customerRoleID
            ],
            [
                'first_name' => 'second',
                'last_name' => 'customer',
                'email' => 'second.customer@TestEmail.com',
                'password' =>  Hash::make('password_second'),
                'role_id' => $customerRoleID
            ],
            [
                'first_name' => 'mr',
                'last_name' => 'admin',
                'email' => 'mr.admin@TestEmail.com',
                'password' =>  Hash::make('password_admin'),
                'role_id' => $adminRoleID
            ]
        ];

        foreach ($data as $row) {
            User::create($row);
        }
    }
}
