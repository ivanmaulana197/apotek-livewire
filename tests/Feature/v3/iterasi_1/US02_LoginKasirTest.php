<?php
namespace Tests\Feature\v3\iterasi_1;

use App\Livewire\Auth\Login;
use Illuminate\Support\Facades\Auth;
use Livewire\Livewire;
use Tests\Feature\BaseTest;

class US02_LoginKasirTest extends BaseTest
{
    /**
     * US-02: Sebagai kasir, saya ingin login ke sistem apotek sehingga saya dapat mengakses fitur transaksi penjualan.
     */

    public function test_us02_login_sukses_sebagai_kasir(): void
    {
        // Menggunakan user dengan role Pegawai yang memiliki hak akses kasir
        $kasir = $this->getPegawaiUser();

        Livewire::test(Login::class)
            ->set('email', $kasir->email)
            ->set('password', 'password')
            ->call('submitLogin')
            ->assertRedirect(route('home'));

        $this->assertTrue(Auth::check());
        $this->assertTrue(Auth::user()->hasRole('Pegawai'));
        $this->assertTrue(Auth::user()->can('kasir'));
    }

    public function test_us02_kasir_dapat_mengakses_fitur_transaksi_penjualan(): void
    {
        $kasir = $this->getPegawaiUser();
        $this->actingAs($kasir);

        // Akses Riwayat Transaksi
        $this->get(route('transaksi'))->assertStatus(200);
    }

    public function test_us02_kasir_tidak_dapat_mengakses_fitur_admin(): void
    {
        $kasir = $this->getPegawaiUser();
        $this->actingAs($kasir);

                                                                  // Akses Data Master User (Fitur Admin) -> Harus Ditolak
        $this->get(route('data-master.user'))->assertStatus(302); // Redirect (Unauthorized)

        // Akses Setting Apotek (Fitur Admin) -> Harus Ditolak
        $this->get(route('setting-apotek'))->assertStatus(302);
    }
}
