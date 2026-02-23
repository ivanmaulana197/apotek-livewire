<?php
namespace Database\Seeders;

use App\Models\Spatie\Role;
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
        $adminRole   = Role::create(['name' => 'Super Admin']);
        $adminRole   = Role::create(['name' => 'Admin']);
        $pegawaiRole = Role::create(['name' => 'Pegawai']);
        // Role::create(['name' => 'Kasir']);
        Role::create(['name' => 'Gudang']);
        $admin = User::factory()->create([
            'name'     => 'Test User',
            'email'    => 'test@example.com',
            'password' => Hash::make('password'),
        ]);
        $admin_biasa = User::factory()->create([
            'name'     => 'Admin biasa',
            'email'    => 'adminsaja@example.com',
            'password' => Hash::make('password'),
        ]);
        $pegawai = User::factory()->create([
            'name'     => 'Pegawai nih',
            'email'    => 'pegawai@gmail.com',
            'password' => Hash::make('password'),
        ]);
        $gudang = User::factory()->create([
            'name'     => 'Gudang nih',
            'email'    => 'gudang@gmail.com',
            'password' => Hash::make('password'),
        ]);
        $admin->assignRole('Super Admin');
        $admin_biasa->assignRole('Admin');
        $pegawai->assignRole('Pegawai');
        $gudang->assignRole('Gudang');
    }
}
