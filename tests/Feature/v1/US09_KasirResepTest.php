<?php
namespace Tests\Feature\v1;

use App\Livewire\KasirCadangan\BarangPesananForm;
use App\Livewire\KasirCadangan\KasirIndex as KasirCadanganIndex;
use App\Livewire\KasirCadangan\KasirPembayaran;
use App\Models\Barang\Barang;
use App\Models\DataMaster\MetodePembayaran;
use App\Models\DataMaster\Satuan;
use App\Models\Kasir\BarangPesananKasir;
use App\Models\Kasir\PesananKasir;
use App\Models\Setting;
use Livewire\Livewire;
use Tests\Feature\BaseTest;

class US09_KasirResepTest extends BaseTest
{
    public function test_us09_bisa_mengubah_mode_kasir_ke_resep(): void
    {
        $pegawai = $this->getPegawaiUser();
        $pegawai->givePermissionTo(['kasir', 'kasir-show-all']);
        $this->actingAs($pegawai);

        $metodeId = MetodePembayaran::firstOrCreate(['nama' => 'TUNAI'])->id;
        $pesanan  = PesananKasir::create([
            'id_user'              => $pegawai->id,
            'no_pesanan_kasir'     => 'PK-RSP-1',
            'mode_kasir'           => 'UMUM',
            'mode_shift'           => 'PAGI',
            'total_harga'          => 0,
            'id_metode_pembayaran' => $metodeId,
        ]);

        Livewire::test(KasirCadanganIndex::class, [
            'id_pesanan_kasir' => $pesanan->id,
        ])->set('mode_kasir', 'RESEP')->assertOk();

        $this->assertEquals('RESEP', $pesanan->fresh()->mode_kasir);
    }

    public function test_us09_wajib_mengisi_form_pasien_saat_resep(): void
    {
        $pegawai = $this->getPegawaiUser();
        $pegawai->givePermissionTo(['kasir', 'kasir-show-all']);
        $this->actingAs($pegawai);

        $metodeId = MetodePembayaran::firstOrCreate(['nama' => 'TUNAI'])->id;
        $pesanan  = PesananKasir::create([
            'id_user'              => $pegawai->id,
            'no_pesanan_kasir'     => 'PK-RSP-1',
            'mode_kasir'           => 'RESEP',
            'mode_shift'           => 'PAGI',
            'total_harga'          => 0,
            'id_metode_pembayaran' => $metodeId,
        ]);

        $setting = Setting::first();
        Livewire::test(KasirPembayaran::class, ['pesanan_kasir' => $pesanan->fresh(), 'setting' => $setting])
            ->set('bayar', '10000')
            ->set('kembalian', '0')
            ->call('handlePembayaran')
            ->assertHasErrors(['nama_pasien' => 'required']);
    }

    public function test_us09_harga_resep_mengikuti_persen_setting(): void
    {
        $pegawai = $this->getPegawaiUser();
        $pegawai->givePermissionTo(['kasir', 'kasir-show-all']);
        $this->actingAs($pegawai);

        $satuan = Satuan::firstOrCreate(['nama' => 'PCS']);
        $barang = Barang::factory()->create([
            'nama'               => 'OBAT RESEP',
            'stok'               => 50,
            'stok_min'           => 5,
            'id_satuan_utama'    => $satuan->id,
            'harga_beli_default' => 1000,
            'mode_jual_utama'    => 'HARGA',
            'harga_jual_utama'   => 2000,
        ]);

        $metodeId = MetodePembayaran::firstOrCreate(['nama' => 'TUNAI'])->id;
        $pesanan  = PesananKasir::create([
            'id_user'              => $pegawai->id,
            'no_pesanan_kasir'     => 'PK-RSP-2',
            'mode_kasir'           => 'UMUM',
            'mode_shift'           => 'PAGI',
            'total_harga'          => 0,
            'id_metode_pembayaran' => $metodeId,
        ]);

        $setting = Setting::first();
        Livewire::test(BarangPesananForm::class, ['pesanan_kasir' => $pesanan->fresh(), 'persen_resep' => $setting->persentase_jual_resep])
            ->set('id_barang', $barang->id)
            ->set('jumlah', '2')
            ->set('id_satuan', $barang->id_satuan_utama)
            ->call('addBarangPesananKasir')
            ->assertHasNoErrors();

        $item       = BarangPesananKasir::where('id_pesanan_kasir', $pesanan->id)->where('id_barang', $barang->id)->firstOrFail();
        $harga_umum = $item->harga;

        Livewire::test(KasirCadanganIndex::class, [
            'id_pesanan_kasir' => $pesanan->id,
        ])->set('mode_kasir', 'RESEP')->assertOk();

        $item_resep = BarangPesananKasir::where('id_pesanan_kasir', $pesanan->id)->where('id_barang', $barang->id)->firstOrFail();
        $expected   = ceil($harga_umum + ($harga_umum * $setting->persentase_jual_resep) / 100);
        $this->assertEquals($expected, $item_resep->harga);
    }
}
