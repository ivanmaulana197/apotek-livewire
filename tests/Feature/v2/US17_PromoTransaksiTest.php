<?php
namespace Tests\Feature\v2;

use App\Livewire\Promo\PromoForm\PromoForm;
use App\Models\Promo\Promo;
use Livewire\Livewire;
use Tests\Feature\BaseTest;

class US17_PromoTransaksiTest extends BaseTest
{
    public function test_us17_admin_dapat_menetapkan_promo_transaksi(): void
    {
        $admin = $this->getAdminUser();
        $this->actingAs($admin);

        $promoData = [
            'nama'            => 'Promo Diskon Transaksi',
            'deskripsi'       => 'Diskon 10% untuk pembelian diatas 100rb',
            'tipe'            => 'TRANSAKSI',
            'start_date'      => now()->format('d-m-Y'),
            'end_date'        => now()->addMonth()->format('d-m-Y'),
            'is_aktif'        => true,
            'is_member'       => false,
            'minimal_belanja' => '100.000',
            'tipe_transaksi'  => 'PERSEN',
            'diskon_persen'   => '10',
        ];

        Livewire::test(PromoForm::class)
            ->set('form.nama', $promoData['nama'])
            ->set('form.deskripsi', $promoData['deskripsi'])
            ->set('form.tipe', $promoData['tipe'])
            ->set('form.start_date', $promoData['start_date'])
            ->set('form.end_date', $promoData['end_date'])
            ->set('form.is_aktif', $promoData['is_aktif'])
            ->set('form.is_member', $promoData['is_member'])
            ->set('form.minimal_belanja', $promoData['minimal_belanja'])
            ->set('form.tipe_transaksi', $promoData['tipe_transaksi'])
            ->set('form.diskon_persen', $promoData['diskon_persen'])
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('promo'));

        $this->assertDatabaseHas('promo', [
            'nama' => $promoData['nama'],
            'tipe' => 'TRANSAKSI',
        ]);

        $promo = Promo::where('nama', $promoData['nama'])->first();
        $this->assertDatabaseHas('promo_transaksi', [
            'id_promo'        => $promo->id,
            'minimal_belanja' => 100000,
            'diskon_persen'   => 10,
        ]);
    }
}
