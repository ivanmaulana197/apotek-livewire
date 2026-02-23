<?php
namespace Tests\Feature\v2;

use App\Livewire\Informasi\InfoBarangExpired\InfoBarangExpiredList;
use App\Models\Barang\Barang;
use App\Models\Barang\StokBatchBarang;
use Livewire\Livewire;
use Tests\Feature\BaseTest;

class US20_InfoBarangKadaluwarsaTest extends BaseTest
{
    public function test_us20_gudang_dapat_melihat_barang_mendekati_expired(): void
    {
        $gudang = $this->getGudangUser();
        $this->actingAs($gudang);

        $barang = Barang::factory()->create(['nama' => 'Obat Expired']);

        // Batch mendekati expired (1 bulan lagi)
        StokBatchBarang::factory()->create([
            'id_barang'   => $barang->id,
            'jumlah'      => 10,
            'tgl_expired' => now()->addMonth(),
        ]);

        Livewire::test(InfoBarangExpiredList::class)
            ->assertSee('Obat Expired');
    }
}
