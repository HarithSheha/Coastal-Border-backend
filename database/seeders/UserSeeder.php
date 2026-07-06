<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name'     => 'System Administrator',
                'username' => 'admin',
                'email'    => 'admin@coastalborder.local',
                'password' => Hash::make('admin123'),
                'role'     => 'admin',
            ],
            [
                'name'     => 'Officer Mwangi',
                'username' => 'mwangi',
                'email'    => 'mwangi@coastalborder.local',
                'password' => Hash::make('officer123'),
                'role'     => 'officer',
            ],
            [
                'name'     => 'Sgt. Achieng',
                'username' => 'achieng',
                'email'    => 'achieng@coastalborder.local',
                'password' => Hash::make('officer123'),
                'role'     => 'officer',
            ],
            [
                'name'     => 'Cpl. Otieno',
                'username' => 'otieno',
                'email'    => 'otieno@coastalborder.local',
                'password' => Hash::make('officer123'),
                'role'     => 'officer',
            ],
        ];

        foreach ($users as $data) {
            User::firstOrCreate(
                ['username' => $data['username']],
                $data
            );
        }

        $this->command->info('  Users seeded:');
        $this->command->info('    admin   / admin123   (Administrator)');
        $this->command->info('    mwangi  / officer123 (Officer)');
        $this->command->info('    achieng / officer123 (Officer)');
        $this->command->info('    otieno  / officer123 (Officer)');
    }
}
