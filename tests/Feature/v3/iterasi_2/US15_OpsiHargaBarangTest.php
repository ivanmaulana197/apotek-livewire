<?php
namespace Tests\Feature\v3\iterasi_2;

use App\Livewire\Barang\BarangForm\BarangForm;
use App\Models\Barang\Barang;
use Livewire\Livewire;
use Tests\Feature\BaseTest;

class US15_OpsiHargaBarangTest extends BaseTest
{
    public function test_us15_admin_dapat_menetapkan_opsi_harga_bertingkat(): void
    {
        $admin = $this->getAdminUser();
        $this->actingAs($admin);

        $barang = Barang::factory()->create();
        $satuan = $barang->satuan_utama;

        $opsiHarga = [
            [
                'jumlah_min'         => '10',
                'id_satuan'          => $satuan->id,
                'mode'               => 'HARGA',
                'harga'              => '9000',
                'persen'             => '0',
                'harga_satuan_utama' => '9000',
            ],
        ];

        Livewire::test(BarangForm::class, ['id' => $barang->id])
            ->set('form.opsi_harga', $opsiHarga)
            ->call('update')
            ->assertHasNoErrors()
            ->assertRedirect(route('data.barang'));

        $this->assertDatabaseHas('harga_barang', [
            'id_barang'  => $barang->id,
            'jumlah_min' => 10,
            'harga'      => 9000,
        ]);
    }
}
