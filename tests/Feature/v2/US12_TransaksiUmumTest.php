<?php
namespace Tests\Feature\v2;

use App\Livewire\KasirCadangan\KasirPembayaran;
use App\Models\Barang\Barang;
use App\Models\DataMaster\MetodePembayaran;
use App\Models\Kasir\BarangPesananKasir;
use App\Models\Kasir\PesananKasir;
use App\Models\Setting;
use Livewire\Livewire;
use Tests\Feature\BaseTest;

class US12_TransaksiUmumTest extends BaseTest
{
    /**
     * US-12 Sebagai kasir, saya ingin melakukan transaksi penjualan umum sehingga saya dapat melayani pembelian pelanggan dengan benar dan tercatat di sistem.
     */
    public function test_us12_kasir_dapat_melakukan_transaksi_penjualan_umum(): void
    {
        $kasir = $this->getPegawaiUser();
        $this->actingAs($kasir);

        // Setup Data
        $setting          = Setting::first();
        $barang           = Barang::factory()->create(['stok' => 100, 'harga_jual_utama' => 10000]);
        $metodePembayaran = MetodePembayaran::factory()->create(['nama' => 'TUNAI']);
        $pesanan          = PesananKasir::create([
            'id_user'              => $kasir->id,
            'no_pesanan_kasir'     => 'PK-TEST-001',
            'mode_kasir'           => 'UMUM',
            'mode_shift'           => 'PAGI',
            'total_harga'          => 0,
            'id_metode_pembayaran' => $metodePembayaran->id,
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
        $pesanan->update(['total_harga' => $pesanan->barang_pesanan_kasir()->sum('sub_total')]);

        Livewire::test(KasirPembayaran::class, ['pesanan_kasir' => $pesanan, 'setting' => $setting])
            ->set('bayar', '50000')
            ->set('kembalian', '30000')
            ->call('handlePembayaran')
            ->assertDispatched('struk-pembayaran');

        $this->assertDatabaseHas('transaksi', [
            'total_bayar'     => 20000,
            'jenis_transaksi' => 'UMUM',
            'id_user'         => $kasir->id,
            'bayar'           => 50000,
            'kembalian'       => 30000,
        ]);
    }
}
