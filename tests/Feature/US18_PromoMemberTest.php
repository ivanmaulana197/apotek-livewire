<?php
namespace Tests\Feature;

use App\Livewire\Promo\PromoForm\PromoForm;
use App\Models\DataMaster\Profesi;
use App\Models\Promo\Promo;
use Livewire\Livewire;
use Tests\Feature\BaseTest;

class US17_PromoMemberTest extends BaseTest
{
    public function test_us18_admin_dapat_menetapkan_promo_khusus_profesi_member(): void
    {
        $admin = $this->getAdminUser();
        $this->actingAs($admin);

        $profesi = Profesi::factory()->create(['nama' => 'Dokter']);

        $promoData = [
            'nama'            => 'Promo Khusus Dokter',
            'deskripsi'       => 'Diskon spesial untuk dokter',
            'tipe'            => 'TRANSAKSI',
            'start_date'      => now()->format('d-m-Y'),
            'end_date'        => now()->addMonth()->format('d-m-Y'),
            'is_aktif'        => true,
            'is_member'       => true,
            'profesi_member'  => [$profesi->id],
            'minimal_belanja' => '50.000',
            'tipe_transaksi'  => 'HARGA',
            'diskon_nominal'  => '5.000',
        ];

        Livewire::test(PromoForm::class)
            ->set('form.nama', $promoData['nama'])
            ->set('form.deskripsi', $promoData['deskripsi'])
            ->set('form.tipe', $promoData['tipe'])
            ->set('form.start_date', $promoData['start_date'])
            ->set('form.end_date', $promoData['end_date'])
            ->set('form.is_aktif', $promoData['is_aktif'])
            ->set('form.is_member', $promoData['is_member'])
            ->set('form.profesi_member', $promoData['profesi_member'])
            ->set('form.minimal_belanja', $promoData['minimal_belanja'])
            ->set('form.tipe_transaksi', $promoData['tipe_transaksi'])
            ->set('form.diskon_nominal', $promoData['diskon_nominal'])
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('promo'));

        $promo = Promo::where('nama', $promoData['nama'])->first();
        $this->assertTrue((bool) $promo->is_member);

        $this->assertDatabaseHas('promo_profesi_member', [
            'id_promo'   => $promo->id,
            'id_profesi' => $profesi->id,
        ]);
    }
}
