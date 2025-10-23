<?php

namespace Database\Seeders;

use App\Models\Spatie\Permission;
use App\Models\Spatie\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data_permission = [
            'supplier.index',
            'supplier.form',
            'supplier.list',
            'supplier.store',
            'supplier.update',
            'supplier.destroy',
        ];

        foreach ($data_permission as $permission) {
            Permission::create(['name' => $permission]);
        }

        Role::where('name','admin')->first()->givePermissionTo($data_permission);
    }
}