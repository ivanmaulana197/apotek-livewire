<?php
namespace Tests\Feature\v1;

use App\Livewire\KasirCadangan\BarangPesananForm;
use App\Livewire\KasirCadangan\KasirPembayaran;
use App\Models\Barang\Barang;
use App\Models\DataMaster\MetodePembayaran;
use App\Models\DataMaster\Satuan;
use App\Models\Kasir\PesananKasir;
use App\Models\Setting;
use App\Models\User;
use Livewire\Livewire;
use Tests\Feature\BaseTest;

class US07_KasirUmumTest extends BaseTest
{
    public function test_us07_transaksi_non_member_sukses_dan_update_stok()
    {
        $pegawai = $this->getPegawaiUser();
        $pegawai->givePermissionTo(['kasir', 'kasir-show-all']);
        $this->actingAs($pegawai);

        $metodeId = MetodePembayaran::firstOrCreate(['nama' => 'TUNAI'])->id;
        $barang   = Barang::factory()->create([
            'nama'               => 'OBAT B',
            'stok'               => 20,
            'stok_min'           => 5,
            'harga_beli_default' => 1000,
            'id_satuan_utama'    => Satuan::firstOrCreate(['nama' => 'PCS'])->id,
            'mode_jual_utama'    => 'HARGA',
            'harga_jual_utama'   => 2000,
        ]);

        $pesanan = PesananKasir::create([
            'id_user'              => $pegawai->id,
            'no_pesanan_kasir'     => 'PK-UMUM-1',
            'mode_kasir'           => 'UMUM',
            'mode_shift'           => 'PAGI',
            'total_harga'          => 0,
            'id_metode_pembayaran' => $metodeId,
        ]);

        Livewire::test(BarangPesananForm::class, ['pesanan_kasir' => $pesanan->fresh(), 'persen_resep' => Setting::first()->persentase_jual_resep])
            ->set('id_barang', $barang->id)
            ->set('jumlah', '3')
            ->set('id_satuan', $barang->id_satuan_utama)
            ->call('addBarangPesananKasir')
            ->assertHasNoErrors();

        $setting = Setting::first();
        Livewire::test(KasirPembayaran::class, ['pesanan_kasir' => $pesanan->fresh(), 'setting' => $setting])
            ->set('bayar', '100000')
            ->set('kembalian', '0')
            ->call('handlePembayaran')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('transaksi', ['id_user' => $pegawai->id, 'status' => 'TERJUAL']);
        $this->assertEquals(17, $barang->fresh()->stok);
    }

    public function test_us07_stok_tidak_mencukupi_error()
    {
        $pegawai = $this->getPegawaiUser();
        $pegawai->givePermissionTo(['kasir', 'kasir-show-all']);
        $this->actingAs($pegawai);

        $metodeId = MetodePembayaran::firstOrCreate(['nama' => 'TUNAI'])->id;
        $barang   = Barang::factory()->create([
            'nama'               => 'OBAT C',
            'stok'               => 2,
            'stok_min'           => 1,
            'harga_beli_default' => 1000,
            'id_satuan_utama'    => Satuan::firstOrCreate(['nama' => 'PCS'])->id,
            'mode_jual_utama'    => 'HARGA',
            'harga_jual_utama'   => 2000,
        ]);

        $pesanan = PesananKasir::create([
            'id_user'              => $pegawai->id,
            'no_pesanan_kasir'     => 'PK-UMUM-2',
            'mode_kasir'           => 'UMUM',
            'mode_shift'           => 'PAGI',
            'total_harga'          => 0,
            'id_metode_pembayaran' => $metodeId,
        ]);

        Livewire::test(BarangPesananForm::class, ['pesanan_kasir' => $pesanan->fresh(), 'persen_resep' => Setting::first()->persentase_jual_resep])
            ->set('id_barang', $barang->id)
            ->set('jumlah', '5')
            ->set('id_satuan', $barang->id_satuan_utama)
            ->call('addBarangPesananKasir');

        $this->assertEquals(0, $pesanan->fresh()->total_harga);
    }

    public function test_us07_akses_gudang_ke_kasir_ditolak()
    {
        $userTanpaKasir = User::factory()->create(['password' => 'password']);
        $this->actingAs($userTanpaKasir);
        $this->get(route('kasir-cadangan'))->assertRedirect(route('home'));
    }
}
