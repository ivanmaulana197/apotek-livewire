<?php
namespace Tests\Feature\v2;

use App\Livewire\Barang\BarangForm\BarangForm;
use App\Models\Barang\Barang;
use Livewire\Livewire;
use Tests\Feature\BaseTest;

class US11_EditBarangrTest extends BaseTest
{
    public function test_us11_admin_dapat_mengubah_data_barang(): void
    {
        $admin = $this->getAdminUser();
        $this->actingAs($admin);

        // Create initial barang
        $barang  = Barang::factory()->create();
        $newNama = 'Barang Edit';

        Livewire::test(BarangForm::class, ['id' => $barang->id])
            ->set('form.nama', $newNama)
            ->call('update')
            ->assertHasNoErrors()
            ->assertRedirect(route('data.barang'));

        $this->assertDatabaseHas('barang', [
            'id'   => $barang->id,
            'nama' => $newNama,
        ]);
    }
}
