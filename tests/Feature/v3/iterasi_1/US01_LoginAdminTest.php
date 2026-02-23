<?php
namespace Tests\Feature\v3\iterasi_1;

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

        Livewire::test(Login::class)->set('email', $admin->email)->set('password', 'password')->call('submitLogin')
            ->assertRedirect(route('home'));

        $this->assertTrue(Auth::check());
        $this->assertTrue(Auth::user()->hasRole('Admin'));
    }

    public function test_us01_admin_dapat_mengakses_fiturnya(): void
    {
        $admin = $this->getAdminUser();
        $this->actingAs($admin);
        $this->get(route('data.barang.tambah'))->assertStatus(200);
    }

    public function test_us01_login_gagal_username_atau_password_tidak_salah(): void
    {
        Livewire::test(Login::class)->set('email', 'unknown@example.com')->set('password', 'password')
            ->call('submitLogin')
            ->assertDispatched('alert', status: 'error', message: 'Email atau password salah')
            ->assertSet('password', '');

        $this->assertFalse(Auth::check());
    }
}
