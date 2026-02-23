<?php
namespace Tests\Feature;

use App\Models\Setting;
use App\Models\User;
use Database\Seeders\TestingSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

abstract class BaseTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Artisan::call('migrate');
        $this->seed(TestingSeeder::class);
        Setting::firstOrCreate([], ['persentase_jual_resep' => 10]);
    }

    protected function getAdminUser(): User
    {
        return User::whereHas('roles', fn($q) => $q->where('name', 'Admin'))->firstOrFail();
    }

    protected function getPegawaiUser(): User
    {
        return User::whereHas('roles', fn($q) => $q->where('name', 'Pegawai'))->firstOrFail();
    }
    protected function getGudangUser(): User
    {
        return User::whereHas('roles', fn($q) => $q->where('name', 'Gudang'))->firstOrFail();
    }
}
