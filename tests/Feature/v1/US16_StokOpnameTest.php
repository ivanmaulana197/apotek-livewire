<?php
namespace Tests\Feature\v1;

use App\Livewire\StokOpname\StokOpnameIndex;
use App\Livewire\StokOpname\StokOpnameList;
use App\Models\Barang\Barang;
use Livewire\Livewire;
use Tests\Feature\BaseTest;

class US16_StokOpnameTest extends BaseTest
{
    /**
     * Mencatat stok fisik barang secara berkala
     */
    public function test_us16_mencatat_stok_fisik(): void
    {
        $admin = $this->getAdminUser();
        $this->actingAs($admin);

        $barang = Barang::factory()->create([
            'stok'               => 10,
            'harga_beli_default' => 5000,
        ]);

        // Input stok fisik (stok opname)
        // Misal ditemukan fisik hanya 8
        Livewire::test(StokOpnameList::class)
            ->set('stok_real', [$barang->id => '8'])
            ->call('simpanStokReal', $barang->id)
            ->assertDispatched('alert', status: 'success');

        $this->assertEquals(8, $barang->fresh()->stok_real, 'Stok real harus tersimpan di database');
    }

    /**
     * Membandingkan stok sistem dengan stok aktual (Hitung Kerugian)
     */
    public function test_us16_membandingkan_stok_sistem_dan_aktual(): void
    {
        $admin = $this->getAdminUser();
        $this->actingAs($admin);

        // Barang 1: Hilang 2 (10 -> 8) @5000 = Kerugian 10.000
        $barang1 = Barang::factory()->create([
            'stok'               => 10,
            'stok_real'          => 8,
            'harga_beli_default' => 5000,
        ]);
        // Hapus batch otomatis dari factory agar harga beli konsisten dari default
        $barang1->stok_batch_barang()->delete();

        // Barang 2: Aman (5 -> 5)
        $barang2 = Barang::factory()->create([
            'stok'               => 5,
            'stok_real'          => 5,
            'harga_beli_default' => 5000,
        ]);
        $barang2->stok_batch_barang()->delete();

        Livewire::test(StokOpnameList::class)
            ->assertSet('total_kerugian', -10000);
    }

    /**
     * Menyesuaikan data stok berdasarkan hasil opname & Menyimpan histori
     * (Mencakup skenario surplus dan defisit stok)
     */
    public function test_us16_sinkronisasi_stok_dan_histori(): void
    {
        $admin = $this->getAdminUser();
        $this->actingAs($admin);

        // Case 1: Defisit (Stok Fisik < Sistem) -> 10 vs 8
        $barangDefisit = Barang::factory()->create([
            'stok'               => 10,
            'stok_real'          => 8,
            'harga_beli_default' => 5000,
        ]);
        $barangDefisit->stok_batch_barang()->delete();

        // Case 2: Surplus (Stok Fisik > Sistem) -> 10 vs 12
        $barangSurplus = Barang::factory()->create([
            'stok'               => 10,
            'stok_real'          => 12,
            'harga_beli_default' => 5000,
        ]);
        $barangSurplus->stok_batch_barang()->delete();

        // Lakukan sinkronisasi
        Livewire::test(StokOpnameIndex::class)
            ->call('sinkronisasiStok')
            ->assertDispatched('alert', status: 'success');

        // 1. Verifikasi Stok Sistem Terupdate
        $this->assertEquals(8, $barangDefisit->fresh()->stok, 'Stok sistem (defisit) harus terupdate');
        $this->assertNull($barangDefisit->fresh()->stok_real, 'Stok real harus di-reset');

        $this->assertEquals(12, $barangSurplus->fresh()->stok, 'Stok sistem (surplus) harus terupdate');
        $this->assertNull($barangSurplus->fresh()->stok_real, 'Stok real harus di-reset');

        // 2. Verifikasi Histori Terbentuk
        $this->assertDatabaseHas('stok_opname_histori', [
            'id_barang' => $barangDefisit->id,
            'stok'      => 10,
            'stok_real' => 8,
            'kerugian'  => -10000, // (8-10)*5000 = -10000 (Kerugian)
        ]);

        $this->assertDatabaseHas('stok_opname_histori', [
            'id_barang' => $barangSurplus->id,
            'stok'      => 10,
            'stok_real' => 12,
            'kerugian'  => 10000, // (12-10)*5000 = 10000 (Keuntungan/Surplus)
        ]);
    }
}
