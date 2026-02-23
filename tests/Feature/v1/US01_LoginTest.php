<?php
namespace Tests\Feature\v1;

use App\Livewire\Auth\Login;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Livewire;
use Tests\Feature\BaseTest;

class US01_LoginTest extends BaseTest
{
    public function test_us01_login_sukses_dengan_kredensial_valid(): void
    {
        $user = User::factory()->create(['password' => 'password']);
        $user->assignRole('Admin');

        Livewire::test(Login::class)
            ->set('email', $user->email)
            ->set('password', 'password')
            ->call('submitLogin')
            ->assertRedirect(route('home'));

        $this->assertTrue(Auth::check());
        $this->assertTrue($user->hasRole('Admin'));
    }
    public function test_us01_login_gagal_username_atau_password_salah(): void
    {
        Livewire::test(Login::class)
            ->set('email', 'unknown@example.com')
            ->set('password', 'password')
            ->call('submitLogin')
            ->assertDispatched('alert', status: 'error', message: 'Email atau password salah')
            ->assertSet('password', '');

        $this->assertFalse(Auth::check());
    }
    public function test_us01_login_memuat_role_yang_benar(): void
    {
        $user = User::factory()->create(['password' => 'password']);
        $user->assignRole('Pegawai');

        Livewire::test(Login::class)
            ->set('email', $user->email)
            ->set('password', 'password')
            ->call('submitLogin')
            ->assertRedirect(route('home'));

        $this->assertTrue(Auth::user()->hasRole('Pegawai'));
    }

    public function test_us01_akses_endpoint_admin_oleh_kasir_ditolak(): void
    {
        $pegawai = $this->getPegawaiUser();
        $this->actingAs($pegawai);
        $this->get(route('data-master.user'))->assertStatus(302);
    }
}
