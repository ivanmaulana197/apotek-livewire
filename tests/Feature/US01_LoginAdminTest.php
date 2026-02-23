<?php
namespace Tests\Feature;

use App\Livewire\Auth\Login;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Livewire;
use Tests\Feature\BaseTest;

class US01_LoginAdminTest extends BaseTest
{
    /**
     * US01 - Sebagai admin,
     * saya ingin login ke sistem apotek sehingga
     * saya dapat mengakses seluruh fitur sistem sesuai dengan hak akses yang dimiliki.
     */

    public function test_us01_login_sukses_sebagai_admin(): void
    {
        $admin = $this->getAdminUser();

        Livewire::test(Login::class)
            ->set('email', $admin->email)
            ->set('password', 'password')
            ->call('submitLogin')
            ->assertRedirect(route('home'));

        $this->assertTrue(Auth::check());
        $this->assertTrue(Auth::user()->hasRole('Admin'));
    }

    public function test_us01_admin_dapat_mengakses_seluruh_fitur_sistem(): void
    {
        $admin = $this->getAdminUser();
        $this->actingAs($admin);

        // Akses Dashboard/Home
        $this->get(route('home'))
            ->assertStatus(200);

        // Akses Data Master (Fitur Admin)

        // Akses Setting Apotek (Fitur Admin)
        $this->get(route('setting-apotek'))
            ->assertStatus(200);
    }

    public function test_us01_login_gagal_username_atau_password_tidak_salah(): void
    {
        Livewire::test(Login::class)
            ->set('email', 'unknown@example.com')
            ->set('password', 'password')
            ->call('submitLogin')
            ->assertDispatched('alert', status: 'error', message: 'Email atau password salah')
            ->assertSet('password', '');

        $this->assertFalse(Auth::check());
    }
}
