<?php
namespace Tests\Feature\v1;

use App\Livewire\DataMaster\Profesi\ProfesiForm;
use App\Livewire\DataMaster\Profesi\ProfesiList;
use App\Models\DataMaster\Profesi;
use App\Models\Promo\Promo;
use Carbon\Carbon;
use Livewire\Livewire;
use Tests\Feature\BaseTest;

class US04_KelolaProfesiTest extends BaseTest
{
    public function test_admin_dapat_menambah_profesi_unik_tersimpan(): void
    {
        $admin = $this->getAdminUser();
        $this->actingAs($admin);

        Livewire::test(ProfesiForm::class)
            ->set('nama', 'Apoteker')
            ->call('save');

        $this->assertDatabaseHas('profesi', ['nama' => 'Apoteker']);
    }

    public function test_validasi_nama_kosong_error(): void
    {
        $admin = $this->getAdminUser();
        $this->actingAs($admin);

        Livewire::test(ProfesiForm::class)
            ->set('nama', '')
            ->call('save')
            ->assertHasErrors(['nama' => 'required']);
    }

    public function test_update_profesi_nama_berubah_terupdate_di_db(): void
    {
        $admin = $this->getAdminUser();
        $this->actingAs($admin);
        $profesi = Profesi::create(['nama' => 'Awal']);

        Livewire::test(ProfesiForm::class)
            ->call('editFormSupplier', $profesi)
            ->set('nama', 'Baru')
            ->call('update');

        $this->assertDatabaseHas('profesi', ['id' => $profesi->id, 'nama' => 'Baru']);
    }

    public function test_hapus_profesi_tidak_terpakai_berhasil(): void
    {
        $admin = $this->getAdminUser();
        $this->actingAs($admin);
        $profesi = Profesi::create(['nama' => 'TidakTerpakai']);

        Livewire::test(ProfesiList::class)
            ->call('delete', $profesi->id);

        $this->assertDatabaseMissing('profesi', ['id' => $profesi->id]);
    }

    public function test_hapus_profesi_terpakai_promo_direject(): void
    {
        $admin = $this->getAdminUser();
        $this->actingAs($admin);
        $profesi = Profesi::create(['nama' => 'Dipakai']);
        $promo   = Promo::create([
            'nama'       => 'Promo Khusus Profesi',
            'deskripsi'  => 'Promo untuk profesi tertentu',
            'tipe'       => 'BARANG',
            'start_date' => Carbon::now()->subDay(),
            'end_date'   => Carbon::now()->addDays(7),
            'is_aktif'   => true,
            'is_member'  => true,
        ]);
        $promo->profesies()->sync([$profesi->id]);

        Livewire::test(ProfesiList::class)
            ->call('delete', $profesi->id);

        $this->assertDatabaseHas('profesi', ['id' => $profesi->id]);
    }
}
