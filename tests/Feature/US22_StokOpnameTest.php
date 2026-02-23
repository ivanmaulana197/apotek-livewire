<?php
namespace Tests\Feature;

use App\Livewire\StokOpname\StokOpnameList;
use App\Models\Barang\Barang;
use Livewire\Livewire;
use Tests\Feature\BaseTest;

class US22_StokOpnameTest extends BaseTest
{
    public function test_us22_gudang_dapat_melakukan_stok_opname(): void
    {
        $gudang = $this->getGudangUser();
        $this->actingAs($gudang);

        $barang = Barang::factory()->create([
            'stok' => 10,
        ]);

        Livewire::test(StokOpnameList::class)
            ->set('stok_real', [$barang->id => '8'])
            ->call('simpanStokReal', $barang->id)
            ->assertDispatched('alert', status: 'success');

        $this->assertDatabaseHas('barang', [
            'id'        => $barang->id,
            'stok_real' => 8,
        ]);
    }
}
