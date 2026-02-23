<?php
namespace Tests\Feature\v2;

use App\Livewire\Barang\BarangForm\BarangForm;
use App\Models\Barang\Barang;
use App\Models\DataMaster\Satuan;
use Livewire\Livewire;
use Tests\Feature\BaseTest;

class US16_OpsiSatuanBarangTest extends BaseTest
{
    public function test_us16_admin_dapat_menetapkan_opsi_satuan_barang(): void
    {
        $admin = $this->getAdminUser();
        $this->actingAs($admin);

        $barang      = Barang::factory()->create();
        $satuanUtama = $barang->satuan_utama;
        $satuanLain  = Satuan::where('id', '!=', $satuanUtama->id)->first();

        if (! $satuanLain) {
            $satuanLain = Satuan::factory()->create();
        }

        $opsiSatuan = [
            [
                'id_satuan'          => $satuanLain->id,
                'rasio_satuan_utama' => '10',
                'rasio_satuan_opsi'  => '1',
            ],
        ];

        Livewire::test(BarangForm::class, ['id' => $barang->id])
            ->set('form.opsi_satuan', $opsiSatuan)
            ->call('update')
            ->assertHasNoErrors()
            ->assertRedirect(route('data.barang'));

        $this->assertDatabaseHas('satuan_barang', [
            'id_barang'          => $barang->id,
            'id_satuan'          => $satuanLain->id,
            'rasio_satuan_utama' => 10,
            'rasio_satuan_opsi'  => 1,
        ]);
    }
}
