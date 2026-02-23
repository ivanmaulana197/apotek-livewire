<?php
namespace Tests\Feature\v1;

use App\Livewire\Barang\BarangForm\BarangForm;
use App\Models\Barang\Barang;
use App\Models\DataMaster\Kategori;
use App\Models\DataMaster\Rak;
use App\Models\DataMaster\Satuan;
use Livewire\Livewire;
use Tests\Feature\BaseTest;

class US10_AlternatifHargaSatuanTest extends BaseTest
{
    public function test_us10_tambah_harga_dan_satuan_via_barang_form(): void
    {
        $admin = $this->getAdminUser();
        $this->actingAs($admin);
        $satuanUtama = Satuan::firstOrCreate(['nama' => 'Pcs']);
        $satuanBox   = Satuan::firstOrCreate(['nama' => 'Box']);
        $kategori    = Kategori::firstOrCreate(['nama' => 'UMUM']);
        $rak         = Rak::firstOrCreate(['nama' => 'RAK A']);

        $barang = Barang::factory()->create([
            'nama'               => 'Obat Test Harga',
            'id_satuan_utama'    => $satuanUtama->id,
            'harga_beli_default' => 1000,
            'harga_jual_utama'   => 2000,
            'mode_jual_utama'    => 'HARGA',
            'stok'               => 100,
        ]);
        $barang->kategories()->attach($kategori->id);
        $barang->raks()->attach($rak->id);

        $component = Livewire::test(BarangForm::class, ['id' => $barang->id]);
        $component->set('form.opsi_satuan', [
            ['id_satuan' => $satuanBox->id, 'rasio_satuan_utama' => 10, 'rasio_satuan_opsi' => 1],
        ]);
        $component->set('form.opsi_harga', [
            ['id_satuan' => $satuanBox->id, 'jumlah_min' => 1, 'mode' => 'HARGA', 'harga' => 18000, 'harga_satuan_utama' => 1800, 'persen' => 0],
        ]);
        $component->call('update')->assertHasNoErrors();

        $this->assertDatabaseHas('satuan_barang', [
            'id_barang' => $barang->id,
            'id_satuan' => $satuanBox->id,
        ]);
        $this->assertDatabaseHas('harga_barang', [
            'id_barang' => $barang->id,
            'id_satuan' => $satuanBox->id,
            'harga'     => 18000,
        ]);
    }
}
