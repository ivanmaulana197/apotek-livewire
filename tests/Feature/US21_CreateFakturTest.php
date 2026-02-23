<?php
namespace Tests\Feature;

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

        // Simulate items in temp table
        FakturBarangTemp::create([
            'id_user'     => $gudang->id,
            'id_barang'   => $barang->id,
            'jumlah'      => 10, // will be converted to jumlah_dalam_satuan_utama via accessor logic if applicable, but for factory/simple test assume direct
            'id_satuan'   => $barang->id_satuan_utama,
            'harga_beli'  => 5000,
            'tgl_expired' => now()->addYear()->format('Y-m-d'),
        ]);

        Livewire::test(FakturForm::class)
            ->set('form.no_faktur', 'INV-001')
            ->set('form.id_supplier', $supplier->id)
            ->set('form.tgl_jatuh_tempo', now()->addMonth()->format('d-m-Y')) // Format display
            ->set('form.total_biaya', '50.000')                               // Format display
            ->call('save')
            ->assertRedirect(route('faktur'));

        $this->assertDatabaseHas('faktur', [
            'no_faktur'   => 'INV-001',
            'id_supplier' => $supplier->id,
        ]);

        $this->assertDatabaseHas('barang', [
            'id'   => $barang->id,
            'stok' => 10, // Updated stock
        ]);

        $this->assertDatabaseHas('stok_batch_barang', [
            'id_barang' => $barang->id,
            'jumlah'    => 10,
        ]);
    }

}
