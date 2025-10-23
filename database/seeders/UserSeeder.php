<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Spatie\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminRole = Role::create(['name' => 'admin']);
        $pegawaiRole = Role::create(['name' => 'pegawai']);
        $admin = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password'=> Hash::make('password')
        ]);
        $pegawai = User::factory()->create([
            'name' => 'Pegawai nih',
            'email' => 'pegawai@gmail.com',
            'password'=> Hash::make('password')
        ]);
        $admin->assignRole('admin');
        $pegawai->assignRole('pegawai');
    }
}
