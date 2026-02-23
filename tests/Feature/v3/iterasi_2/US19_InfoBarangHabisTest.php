<?php
namespace Tests\Feature\v3\iterasi_2;

use App\Livewire\Informasi\InfoBarangHabis\InfoBarangHabisList;
use App\Models\Barang\Barang;
use Livewire\Livewire;
use Tests\Feature\BaseTest;

class US19_InfoBarangHabisTest extends BaseTest
{
    public function test_us19_gudang_dapat_melihat_barang_hampir_habis(): void
    {
        $gudang = $this->getGudangUser();
        $this->actingAs($gudang);

        // Barang stok habis (stok <= stok_min)
        $barangHabis = Barang::factory()->create([
            'nama'     => 'Obat Habis',
            'stok'     => 5,
            'stok_min' => 10,
        ]);

        // Barang stok aman
        $barangAman = Barang::factory()->create([
            'nama'     => 'Obat Aman',
            'stok'     => 20,
            'stok_min' => 10,
        ]);

        Livewire::test(InfoBarangHabisList::class)
            ->call('getData')
            ->assertSee('Obat Habis')
            ->assertDontSee('Obat Aman');
    }
}
