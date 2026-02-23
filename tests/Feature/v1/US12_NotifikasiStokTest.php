<?php
namespace Tests\Feature\v1;

use App\Livewire\Home\HomeIndex;
use App\Models\Barang\Barang;
use App\Models\Barang\StokBatchBarang;
use Livewire\Livewire;
use Tests\Feature\BaseTest;

class US12_NotifikasiStokTest extends BaseTest
{
    /**
     * Mendeteksi barang dengan jumlah stok yang berada di bawah batas minimum
     */
    public function test_us12_deteksi_stok_di_bawah_minimum(): void
    {
        $admin = $this->getAdminUser();
        $this->actingAs($admin);

        // Barang dengan stok di bawah minimum (1 < 5)
        Barang::factory()->create(['stok' => 1, 'stok_min' => 5]);

        // Barang dengan stok di atas minimum (10 > 5)
        Barang::factory()->create(['stok' => 10, 'stok_min' => 5]);

        Livewire::test(HomeIndex::class)
            ->call('getData') // Memanggil method yang menghitung alert
            ->assertSet('obat_hampir_habis', 1);
    }

    /**
     * Mendeteksi barang dengan jumlah stok sama dengan batas minimum
     */
    public function test_us12_deteksi_stok_sama_dengan_minimum(): void
    {
        $admin = $this->getAdminUser();
        $this->actingAs($admin);

        // Barang dengan stok pas minimum (5 == 5) -> Termasuk hampir habis
        Barang::factory()->create(['stok' => 5, 'stok_min' => 5]);

        Livewire::test(HomeIndex::class)
            ->call('getData')
            ->assertSet('obat_hampir_habis', 1);
    }

    /**
     * Mengidentifikasi barang yang mendekati tanggal kadaluwarsa
     */
    public function test_us12_identifikasi_barang_mendekati_kadaluwarsa(): void
    {
        $admin = $this->getAdminUser();
        $this->actingAs($admin);

        $barang = Barang::factory()->create();

        // Batch mendekati expired (2 bulan lagi, batas alert 3 bulan)
        StokBatchBarang::factory()->create([
            'id_barang'   => $barang->id,
            'jumlah'      => 10,
            'tgl_expired' => now()->addMonths(2),
        ]);

        // Batch aman (4 bulan lagi)
        StokBatchBarang::factory()->create([
            'id_barang'   => $barang->id,
            'jumlah'      => 10,
            'tgl_expired' => now()->addMonths(4),
        ]);

        Livewire::test(HomeIndex::class)
            ->call('getData')
            ->assertSet('barang_kadaluarsa', 1);
    }

    /**
     * Mengidentifikasi barang yang telah kadaluwarsa
     */
    public function test_us12_identifikasi_barang_telah_kadaluwarsa(): void
    {
        $admin = $this->getAdminUser();
        $this->actingAs($admin);

        $barang = Barang::factory()->create();

        // Batch sudah expired (kemarin)
        StokBatchBarang::factory()->create([
            'id_barang'   => $barang->id,
            'jumlah'      => 10,
            'tgl_expired' => now()->subDay(),
        ]);

        Livewire::test(HomeIndex::class)
            ->call('getData')
            ->assertSet('barang_kadaluarsa', 1);
    }
}
