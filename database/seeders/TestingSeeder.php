<?php
namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TestingSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Skip BarangSeeder to speed up tests. 
        // Tests that need Barang should create it using factories.
        
        $this->call([
            UserSeeder::class,
            KategoriSeeder::class,
            RakSeeder::class,
            SatuanSeeder::class,
            // BarangSeeder::class, // Skipped for performance
            SettingSeeder::class,
            TestingPermissionSeeder::class,
            MetodePembayaranSeeder::class,
            ProfesiSeeder::class,
        ]);
    }
}
