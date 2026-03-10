<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'rolodev.uy@gmail.com'],
            [
                'name'     => 'Admin',
                'password' => Hash::make('f-XM.cWtUfY0MPHJXd]n'),
                'is_admin' => true,
            ]
        );
    }
}
