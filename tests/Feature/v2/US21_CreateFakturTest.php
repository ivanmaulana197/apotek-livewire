<?php
namespace Tests\Feature\v2;

use App\Livewire\Faktur\FakturForm\FakturForm;
use App\Models\Barang\Barang;
use App\Models\DataMaster\Supplier;
use App\Models\Faktur\FakturBarangTemp;
use Livewire\Livewire;
use Tests\Feature\BaseTest;

class US21_CreateFakturTest extends BaseTest
{
    public function test_us21_gudang_dapat_menyimpan_faktur_pembelian(): void
    {
        $gudang = $this->getGudangUser();
        $this->actingAs($gudang);

        $supplier = Supplier::factory()->create();
        $barang   = Barang::factory()->create(['stok' => 0]);

        FakturBarangTemp::create([
            'id_user'     => $gudang->id,
            'id_barang'   => $barang->id,
            'jumlah'      => 10,
            'id_satuan'   => $barang->id_satuan_utama,
            'harga_beli'  => 5000,
            'tgl_expired' => now()->addYear()->format('Y-m-d'),
        ]);

        Livewire::test(FakturForm::class)
            ->set('form.no_faktur', 'INV-001')
            ->set('form.id_supplier', $supplier->id)
            ->set('form.tgl_jatuh_tempo', now()->addMonth()->format('d-m-Y'))
            ->set('form.total_biaya', '50.000')
            ->call('save')
            ->assertRedirect(route('faktur'));
        //cek faktur tersimpan
        $this->assertDatabaseHas('faktur', ['no_faktur' => 'INV-001', 'id_supplier' => $supplier->id]);
        //cek stok barang bertambah
        $this->assertDatabaseHas('barang', ['id' => $barang->id, 'stok' => 10]);
        //cek stok batchnya menyimpan tgl_expired
        $this->assertDatabaseHas('stok_batch_barang', ['id_barang' => $barang->id, 'jumlah' => 10, 'tgl_expired' => now()->addYear()->format('Y-m-d')]);
    }

}
