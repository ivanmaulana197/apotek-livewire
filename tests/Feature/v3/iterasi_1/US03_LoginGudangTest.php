<?php
namespace Tests\Feature\v3\iterasi_1;

use App\Livewire\Auth\Login;
use Illuminate\Support\Facades\Auth;
use Livewire\Livewire;
use Tests\Feature\BaseTest;

class US03_LoginGudangTest extends BaseTest
{
    /**
     * US-03 - Sebagai gudang, saya ingin login ke sistem apotek sehingga saya dapat mengakses fitur pengelolaan stok.
     */

    public function test_us03_login_sukses_sebagai_gudang(): void
    {
        $gudang = $this->getGudangUser();

        Livewire::test(Login::class)
            ->set('email', $gudang->email)
            ->set('password', 'password')
            ->call('submitLogin')
            ->assertRedirect(route('home'));

        $this->assertTrue(Auth::check());
        $this->assertTrue(Auth::user()->hasRole('Gudang'));
    }

}
