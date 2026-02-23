<?php
namespace Tests\Feature\v3\iterasi_3;

use App\Livewire\DataMaster\User\UserForm;
use App\Models\Spatie\Role;
use App\Models\User;
use Livewire\Livewire;
use Tests\Feature\BaseTest;

class US05_EditPegawaiTest extends BaseTest
{
    /**
     * US-05 Sebagai admin, saya ingin mengubah data pegawai sehingga informasi pegawai selalu akurat.
     */
    public function test_us05_admin_dapat_mengubah_data_pegawai(): void
    {
        $admin = $this->getAdminUser();
        $this->actingAs($admin);

        // Create initial pegawai
        $pegawai = User::factory()->create([
            'name'  => 'Pegawai Lama',
            'email' => 'lama@example.com',
        ]);
        $pegawai->assignRole('Pegawai');

        $pegawaiRole = Role::where('name', 'Pegawai')->first();

        Livewire::test(UserForm::class)
            ->call('editFormUser', $pegawai)
            ->set('name', 'Pegawai Terupdate')
            ->set('roles_selected', [$pegawaiRole->id])
            ->call('update')
            ->assertDispatched('alert', status: 'info');

        $this->assertDatabaseHas('users', [
            'id'   => $pegawai->id,
            'name' => 'Pegawai Terupdate',
        ]);
    }
}
