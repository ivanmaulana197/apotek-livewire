<?php
namespace Tests\Feature;

use App\Livewire\Auth\Login;
use Illuminate\Support\Facades\Auth;
use Livewire\Livewire;

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

    public function test_us03_gudang_dapat_mengakses_fitur_pengelolaan_stok(): void
    {
        $gudang = $this->getGudangUser();
        $this->actingAs($gudang);

        // Akses Barang (Stok)
        $this->get(route('data.barang'))->assertStatus(200);

        // Akses Faktur (Stok Masuk)
        $this->get(route('faktur'))->assertStatus(200);

        // Akses Stok Opname
        $this->get(route('stok-opname'))->assertStatus(200);
    }

    public function test_us03_gudang_tidak_dapat_mengakses_fitur_admin_atau_kasir(): void
    {
        $gudang = $this->getGudangUser();
        $this->actingAs($gudang);

        // Admin: Data User
        $this->get(route('data-master.user'))->assertStatus(302);

        // Admin: Data Role
        $this->get(route('data-master.role'))->assertStatus(302);
    }
}
