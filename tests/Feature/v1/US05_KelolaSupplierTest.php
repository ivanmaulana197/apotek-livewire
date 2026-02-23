<?php
namespace Tests\Feature\v1;

use App\Livewire\DataMaster\Supplier\SupplierForm;
use App\Livewire\DataMaster\Supplier\SupplierList;
use App\Models\User;
use Livewire\Livewire;
use Tests\Feature\BaseTest;

class US05_KelolaSupplierTest extends BaseTest
{
    public function test_us05_tambah_supplier_valid(): void
    {
        $admin = $this->getAdminUser();
        $this->actingAs($admin);

        Livewire::test(SupplierForm::class)
            ->set('nama', 'SUPPLIER A')
            ->set('no_hp', '0812')
            ->set('alamat', 'Jl. Supplier')
            ->set('asal', 'PT')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('supplier', ['nama' => 'SUPPLIER A']);
    }

    public function test_us05_validasi_data_supplier_nama_kosong(): void
    {
        $admin = $this->getAdminUser();
        $this->actingAs($admin);

        Livewire::test(SupplierForm::class)
            ->set('nama', '')
            ->set('asal', 'PT')
            ->call('save')
            ->assertHasErrors(['nama' => 'required']);
    }

    public function test_us05_update_supplier(): void
    {
        $admin = $this->getAdminUser();
        $this->actingAs($admin);
        $supplier = \App\Models\DataMaster\Supplier::create(['nama' => 'OLD', 'asal' => 'PT', 'alamat' => 'JL', 'no_hp' => '0812', 'is_pajak' => true]);

        Livewire::test(SupplierForm::class)
            ->call('editFormSupplier', $supplier)
            ->set('nama', 'NEW')
            ->set('asal', 'PT')
            ->call('update')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('supplier', ['id' => $supplier->id, 'nama' => 'NEW']);
    }

    public function test_us05_hapus_supplier(): void
    {
        $admin = $this->getAdminUser();
        $this->actingAs($admin);
        $supplier = \App\Models\DataMaster\Supplier::create(['nama' => 'DEL', 'asal' => 'CV', 'alamat' => 'JL', 'no_hp' => '0813', 'is_pajak' => true]);

        Livewire::test(SupplierList::class)
            ->dispatch('delete-supplier', $supplier);

        $this->assertDatabaseMissing('supplier', ['id' => $supplier->id]);
    }

    public function test_us05_akses_non_admin_ke_supplier_ditolak(): void
    {
        $userTanpaAkses = User::factory()->create(['password' => 'password']);
        $this->actingAs($userTanpaAkses);
        $this->get(route('data-master.supplier'))->assertRedirect(route('home'));
    }
}
