<?php
namespace Tests\Feature\v1;

use App\Livewire\Barang\BarangForm\BarangForm;
use App\Models\Barang\Barang;
use App\Models\DataMaster\Kategori;
use App\Models\DataMaster\Rak;
use App\Models\DataMaster\Satuan;
use Livewire\Livewire;
use Tests\Feature\BaseTest;

class US06_KelolaBarangTest extends BaseTest
{
    public function test_us06_tambah_barang_baru_tersimpan(): void
    {
        $admin = $this->getAdminUser();
        $this->actingAs($admin);

        $satuan   = Satuan::firstOrCreate(['nama' => 'PCS']);
        $kategori = Kategori::firstOrCreate(['nama' => 'UMUM']);
        $rak      = Rak::firstOrCreate(['nama' => 'RAK A']);

        Livewire::test(BarangForm::class)
            ->set('form.nama', 'OBAT A')
            ->set('form.stok', '10')
            ->set('form.stok_min', '5')
            ->set('form.id_satuan_utama', $satuan->id)
            ->set('form.id_kategories', [$kategori->id])
            ->set('form.id_raks', [$rak->id])
            ->set('form.harga_beli_default', '1000')
            ->set('form.mode_jual_utama', 'HARGA')
            ->set('form.harga_jual_utama', '2000')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('barang', ['nama' => 'OBAT A', 'stok' => 10]);
    }

    public function test_us06_update_barang_berhasil(): void
    {
        $admin = $this->getAdminUser();
        $this->actingAs($admin);

        $satuan = Satuan::firstOrCreate(['nama' => 'PCS']);
        $barang = Barang::factory()->create([
            'nama'               => 'OBAT A',
            'stok'               => 10,
            'stok_min'           => 5,
            'id_satuan_utama'    => $satuan->id,
            'harga_beli_default' => 1000,
            'mode_jual_utama'    => 'HARGA',
            'harga_jual_utama'   => 2000,
        ]);

        Livewire::test(BarangForm::class, ['id' => $barang->id])
            ->set('form.nama', 'OBAT A+')
            ->call('update')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('barang', ['id' => $barang->id, 'nama' => 'OBAT A+']);
    }
}
