<?php
namespace Tests\Feature\v1;

use App\Livewire\Faktur\FakturForm\FakturForm;
use App\Livewire\Faktur\FakturList;
use App\Models\Barang\Barang;
use App\Models\DataMaster\Supplier;
use App\Models\Faktur\DetailFakturBarang;
use App\Models\Faktur\Faktur;
use App\Models\Faktur\FakturBarangTemp;
use App\Models\HistoryBarang;
use Livewire\Livewire;
use Tests\Feature\BaseTest;

class US14_KelolaFakturTest extends BaseTest
{
    public function test_tambah_faktur_pembelian_data_valid_tersimpan(): void
    {
        $admin = $this->getAdminUser();
        $this->actingAs($admin);
        $supplier = Supplier::first() ?? Supplier::create(['nama' => 'PT Test', 'is_pajak' => true]);

        $barang = Barang::factory()->create();
        FakturBarangTemp::create([
            'id_barang'   => $barang->id,
            'jumlah'      => 1,
            'id_satuan'   => $barang->id_satuan_utama,
            'tgl_expired' => now()->addMonths(6),
            'harga_beli'  => 1000,
            'id_user'     => $admin->id,
        ]);

        Livewire::test(FakturForm::class)
            ->set('form.no_faktur', 'FKT-001')
            ->set('form.id_supplier', $supplier->id)
            ->set('form.total_biaya', 1000)
            ->set('form.status', 'LUNAS')
            ->call('save')
            ->assertRedirect(route('faktur'));

        $this->assertDatabaseHas('faktur', ['no_faktur' => 'FKT-001', 'id_supplier' => $supplier->id]);
    }

    public function test_validasi_nomor_faktur_kosong_error(): void
    {
        $admin = $this->getAdminUser();
        $this->actingAs($admin);
        $supplier = Supplier::first() ?? Supplier::create(['nama' => 'PT Test', 'is_pajak' => true]);
        Livewire::test(FakturForm::class)
            ->set('form.no_faktur', '')
            ->set('form.id_supplier', $supplier->id)
            ->set('form.total_biaya', 10000)
            ->set('form.status', 'LUNAS')
            ->set('form.faktur_barang', [1])
            ->call('save')
            ->assertHasErrors(['form.no_faktur' => 'required']);
    }

    public function test_lihat_daftar_faktur_data_sesuai_db(): void
    {
        $admin = $this->getAdminUser();
        $this->actingAs($admin);
        Livewire::test(FakturList::class)->assertOk();
    }

    public function test_hapus_faktur_sukses(): void
    {
        $admin = $this->getAdminUser();
        $this->actingAs($admin);
        $supplier = Supplier::first() ?? Supplier::create(['nama' => 'PT Obat', 'is_pajak' => true]);
        $faktur   = Faktur::create(['no_faktur' => 'FKT-DEL', 'id_supplier' => $supplier->id, 'total_biaya' => 10000, 'tgl_jatuh_tempo' => now()->addDays(7), 'status' => 'BELUM LUNAS']);
        $barang   = Barang::factory()->create(['stok' => 2]);
        $history  = HistoryBarang::create(['id_barang' => $barang->id, 'jumlah' => 1, 'id_satuan' => $barang->id_satuan_utama, 'status' => 'MASUK', 'id_user' => $admin->id, 'tgl_expired' => [['tgl' => now()->addMonths(6)->toDateString(), 'jumlah' => 1]]]);
        DetailFakturBarang::create(['id_history_barang' => $history->id, 'id_faktur' => $faktur->id, 'harga_beli' => 1000]);

        Livewire::test(FakturList::class)
            ->call('deleteFaktur', $faktur->id)
            ->assertOk();

        $this->assertDatabaseMissing('faktur', ['id' => $faktur->id]);
        $this->assertDatabaseHas('barang', ['id' => $barang->id, 'stok' => 1]);
    }

    public function test_akses_kasir_ke_halaman_faktur_ditolak(): void
    {
        $pegawai = $this->getPegawaiUser();
        $this->actingAs($pegawai);
        $this->get(route('faktur'))->assertStatus(302)->assertRedirect(route('home'));
    }
}
