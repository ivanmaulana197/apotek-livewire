<?php
namespace Tests\Feature\v1;

use App\Livewire\KasirCadangan\Promo\PromoBarangList;
use App\Models\Barang\Barang;
use App\Models\DataMaster\Satuan;
use App\Models\Promo\Promo;
use App\Models\Promo\PromoBarang;
use Livewire\Livewire;
use Tests\Feature\BaseTest;

class US11_PromoBarangTest extends BaseTest
{
    /**
     * Mengelola data promo barang (diskon dan bonus)
     */
    public function test_us11_tambah_promo_diskon_dan_bonus(): void
    {
        $admin = $this->getAdminUser();
        $this->actingAs($admin);

        $satuan      = Satuan::firstOrCreate(['nama' => 'PCS']);
        $barang      = Barang::factory()->create(['id_satuan_utama' => $satuan->id]);
        $bonusBarang = Barang::factory()->create(['id_satuan_utama' => $satuan->id]);

        // 1. Buat Promo Diskon
        $promoDiskon = Promo::create([
            'nama'       => 'Promo Diskon Langsung',
            'deskripsi'  => 'Potongan harga langsung',
            'tipe'       => 'BARANG',
            'start_date' => now(),
            'end_date'   => now()->addDays(7),
            'is_aktif'   => true,
            'is_member'  => false,
        ]);

        $detailDiskon = PromoBarang::create([
            'id_promo'       => $promoDiskon->id,
            'id_barang'      => $barang->id,
            'id_satuan'      => $satuan->id,
            'syarat_qty'     => 1,
            'tipe_qty'       => 'MINIMAL',
            'tipe'           => 'HARGA',
            'diskon_nominal' => 5000,
        ]);

        // 2. Buat Promo Bonus Barang
        $promoBonus = Promo::create([
            'nama'       => 'Beli 2 Gratis 1',
            'deskripsi'  => 'Bonus barang gratis',
            'tipe'       => 'BARANG',
            'start_date' => now(),
            'end_date'   => now()->addDays(7),
            'is_aktif'   => true,
            'is_member'  => false,
        ]);

        $detailBonus = PromoBarang::create([
            'id_promo'        => $promoBonus->id,
            'id_barang'       => $barang->id,
            'id_satuan'       => $satuan->id,
            'syarat_qty'      => 2,
            'tipe_qty'        => 'KELIPATAN',
            'tipe'            => 'BARANG BONUS',
            'id_barang_bonus' => $bonusBarang->id,
            'id_satuan_bonus' => $satuan->id,
            'bonus_qty'       => 1,
        ]);

        $this->assertDatabaseHas('promo', ['nama' => 'Promo Diskon Langsung']);
        $this->assertDatabaseHas('promo_barang', ['id_promo' => $promoDiskon->id, 'tipe' => 'HARGA']);

        $this->assertDatabaseHas('promo', ['nama' => 'Beli 2 Gratis 1']);
        $this->assertDatabaseHas('promo_barang', ['id_promo' => $promoBonus->id, 'tipe' => 'BARANG BONUS']);
    }

    /**
     * Menentukan syarat dan ketentuan promo
     * (Verifikasi bahwa promo hanya berlaku jika syarat terpenuhi)
     */
    public function test_us11_syarat_promo_qty_minimal_dan_periode(): void
    {
        $admin = $this->getAdminUser();
        $this->actingAs($admin);

        $satuan = Satuan::firstOrCreate(['nama' => 'PCS']);
        $barang = Barang::factory()->create(['id_satuan_utama' => $satuan->id]);

        // Promo aktif
        $promo = Promo::create([
            'nama'       => 'Promo Minimal Beli 3',
            'deskripsi'  => 'Diskon jika beli minimal 3',
            'tipe'       => 'BARANG',
            'start_date' => now()->subDay(),
            'end_date'   => now()->addDay(),
            'is_aktif'   => true,
            'is_member'  => false,
        ]);

        PromoBarang::create([
            'id_promo'       => $promo->id,
            'id_barang'      => $barang->id,
            'id_satuan'      => $satuan->id,
            'syarat_qty'     => 3, // Syarat minimal 3
            'tipe_qty'       => 'MINIMAL',
            'tipe'           => 'HARGA',
            'diskon_nominal' => 2000,
        ]);

        // Cek jika beli cuma 2 (tidak memenuhi syarat)
        Livewire::test(PromoBarangList::class)
            ->call('getPromo', null, $barang->id, 2, null, 'form')
            ->assertDontSee('Promo Minimal Beli 3');

        // Cek jika beli 3 (memenuhi syarat)
        Livewire::test(PromoBarangList::class)
            ->call('getPromo', null, $barang->id, 3, null, 'form')
            ->assertSee('Promo Minimal Beli 3');
    }

    /**
     * Menampilkan daftar promo aktif
     * (Verifikasi bahwa promo expired atau non-aktif tidak muncul)
     */
    public function test_us11_list_promo_hanya_menampilkan_yang_aktif(): void
    {
        $admin = $this->getAdminUser();
        $this->actingAs($admin);

        $satuan = Satuan::firstOrCreate(['nama' => 'PCS']);
        $barang = Barang::factory()->create(['id_satuan_utama' => $satuan->id]);

        // Promo Kadaluarsa
        $promoExpired = Promo::create([
            'nama'       => 'Promo Sudah Lewat',
            'tipe'       => 'BARANG',
            'start_date' => now()->subDays(10),
            'end_date'   => now()->subDays(1),
            'is_aktif'   => true,
            'is_member'  => false,
        ]);

        PromoBarang::create([
            'id_promo'       => $promoExpired->id,
            'id_barang'      => $barang->id,
            'id_satuan'      => $satuan->id,
            'syarat_qty'     => 1,
            'tipe_qty'       => 'MINIMAL',
            'tipe'           => 'HARGA',
            'diskon_nominal' => 1000,
        ]);

        // Promo Aktif
        $promoAktif = Promo::create([
            'nama'       => 'Promo Masih Berlaku',
            'tipe'       => 'BARANG',
            'start_date' => now()->subDay(),
            'end_date'   => now()->addDay(),
            'is_aktif'   => true,
            'is_member'  => false,
        ]);

        PromoBarang::create([
            'id_promo'       => $promoAktif->id,
            'id_barang'      => $barang->id,
            'id_satuan'      => $satuan->id,
            'syarat_qty'     => 1,
            'tipe_qty'       => 'MINIMAL',
            'tipe'           => 'HARGA',
            'diskon_nominal' => 1000,
        ]);

        // Verifikasi via Component
        Livewire::test(PromoBarangList::class)
            ->call('getPromo', null, $barang->id, 1, null, 'form')
            ->assertSee('Promo Masih Berlaku')
            ->assertDontSee('Promo Sudah Lewat');

        // Verifikasi via Query Scope
        $this->assertEquals(1, Promo::aktif()->whereIn('id', [$promoExpired->id, $promoAktif->id])->count());
    }
}
