<?php
namespace Tests\Feature\v2;

use App\Livewire\KasirCadangan\KasirPembayaran;
use App\Models\Barang\Barang;
use App\Models\DataMaster\Dokter;
use App\Models\DataMaster\MetodePembayaran;
use App\Models\Kasir\BarangPesananKasir;
use App\Models\Kasir\PesananKasir;
use App\Models\Setting;
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
        //setup data
        $barang           = Barang::factory()->create(['stok' => 100, 'harga_jual_utama' => 10000]);
        $metodePembayaran = MetodePembayaran::factory()->create(['nama' => 'TUNAI']);
        $dokter           = Dokter::factory()->create();
        $setting          = Setting::first();

        $pesanan = PesananKasir::create([
            'id_user'              => $kasir->id,
            'no_pesanan_kasir'     => 'PK-TEST-003',
            'mode_kasir'           => 'RESEP', // Set mode RESEP
            'mode_shift'           => 'PAGI',
            'total_harga'          => 0,
            'id_metode_pembayaran' => $metodePembayaran->id,
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
        $pesanan->update(['total_harga' => $pesanan->barang_pesanan_kasir()->sum('sub_total')]);

        Livewire::test(KasirPembayaran::class, ['pesanan_kasir' => $pesanan, 'setting' => $setting])
            ->set('nama_pasien', 'PASIEN TEST')
            ->set('alamat_pasien', 'ALAMAT TEST')
            ->set('no_hp_pasien', '08123456789')
            ->set('dokter_pasien', $dokter->id)
            ->set('bayar', '50000')
            ->set('kembalian', '30000')
            ->call('handlePembayaran')
            ->assertDispatched('struk-pembayaran');
        //cek transaksi tersimpan dengan jenis RESEP
        $this->assertDatabaseHas('transaksi', ['total_bayar' => 20000, 'jenis_transaksi' => 'RESEP', 'id_dokter' => $dokter->id]);
        //cek data pasien tersimpan
        $this->assertDatabaseHas('pasien', ['nama_pasien' => 'PASIEN TEST']);
    }
}
