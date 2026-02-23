<?php
namespace Tests\Feature;

use App\Livewire\Barang\BarangForm\BarangForm;
use App\Models\DataMaster\Kategori;
use App\Models\DataMaster\Rak;
use App\Models\DataMaster\Satuan;
use Livewire\Livewire;
use Tests\Feature\BaseTest;

class US10_CreateBarangrTest extends BaseTest
{
    public function test_us10_admin_dapat_menambah_barang(): void
    {
        $admin = $this->getAdminUser();
        $this->actingAs($admin);

        $kategori   = Kategori::first();
        $rak        = Rak::first();
        $satuan     = Satuan::first();
        $namaBarang = 'Obat Test Baru';

        Livewire::test(BarangForm::class)
            ->set('form.nama', $namaBarang)
            ->set('form.stok', '100')
            ->set('form.stok_min', '10')
            ->set('form.id_kategories', [$kategori->id])
            ->set('form.id_raks', [$rak->id])
            ->set('form.id_satuan_utama', $satuan->id)
            ->set('form.harga_beli_default', '10000')
            ->set('form.mode_jual_utama', 'HARGA')
            ->set('form.harga_jual_utama', '11000')
        // ->set('form.persen_jual_utama', '10')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('barang', [
            'nama'            => $namaBarang,
            'stok'            => 100,
            'id_satuan_utama' => $satuan->id,
        ]);
    }
}
