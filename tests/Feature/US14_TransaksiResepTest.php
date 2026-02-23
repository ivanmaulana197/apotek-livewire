<?php
namespace Tests\Feature;

use App\Livewire\Kasir\KasirPembayaranForm;
use App\Models\Barang\Barang;
use App\Models\DataMaster\Dokter;
use App\Models\DataMaster\MetodePembayaran;
use App\Models\Kasir\BarangPesananKasir;
use App\Models\Kasir\PesananKasir;
use Livewire\Livewire;
use Tests\Feature\BaseTest;

class US14_TransaksiResepTest extends BaseTest
{
    /**
     * US-14 Sebagai kasir, saya ingin melakukan transaksi penjualan berdasarkan resep dokter sehingga saya dapat melayani penjualan obat resep sesuai ketentuan.
     */
    public function test_us14_kasir_dapat_melakukan_transaksi_penjualan_resep(): void
    {
        $kasir = $this->getPegawaiUser();
        $this->actingAs($kasir);

        $barang = Barang::factory()->create([
            'stok'             => 100,
            'harga_jual_utama' => 10000,
        ]);

        $metodePembayaran = MetodePembayaran::factory()->create(['nama' => 'TUNAI']);
        $dokter           = Dokter::factory()->create();

        $pesanan = PesananKasir::create([
            'id_user'              => $kasir->id,
            'no_pesanan_kasir'     => 'PK-TEST-003',
            'mode_kasir'           => 'RESEP', // Set mode RESEP
            'mode_shift'           => 'PAGI',
            'total_harga'          => 0,
            'id_metode_pembayaran' => $metodePembayaran->id,
            // Pasien info stored in pesanan_kasir
            'nama_pasien'          => 'PASIEN TEST',
            'alamat_pasien'        => 'ALAMAT TEST',
            'no_hp_pasien'         => '08123456789',
            'id_dokter'            => $dokter->id,
        ]);

        BarangPesananKasir::create([
            'id_pesanan_kasir'          => $pesanan->id,
            'id_barang'                 => $barang->id,
            'id_satuan'                 => $barang->id_satuan_utama,
            'jumlah'                    => 2,
            'jumlah_dalam_satuan_utama' => 2,
            'harga'                     => 10000,
            'harga_satuan_utama'        => 10000,
            'sub_total'                 => 20000,
        ]);

        Livewire::test(KasirPembayaranForm::class, ['pesanan_kasir' => $pesanan])
            ->set('nama_pasien', 'PASIEN TEST') // Ensure component has these set
            ->set('alamat_pasien', 'ALAMAT TEST')
            ->set('no_hp_pasien', '08123456789')
            ->set('dokter_pasien', $dokter->id)
            ->call('handlePembayaran', bayar: 50000, kembalian: 30000)
            ->assertDispatched('struk-pembayaran');

        $this->assertDatabaseHas('transaksi', [
            'total_bayar'     => 20000,
            'jenis_transaksi' => 'RESEP',
            'id_dokter'       => $dokter->id,
        ]);

        $this->assertDatabaseHas('pasien', [
            'nama_pasien' => 'PASIEN TEST',
        ]);
    }
}
