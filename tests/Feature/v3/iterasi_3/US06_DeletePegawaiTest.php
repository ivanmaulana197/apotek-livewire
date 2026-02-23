<?php
namespace Tests\Feature\v3\iterasi_3;

use App\Livewire\DataMaster\User\UserList;
use App\Models\User;
use Livewire\Livewire;
use Tests\Feature\BaseTest;

class US06_DeletePegawaiTest extends BaseTest
{
    /**
     * US-06 Sebagai admin, saya ingin menghapus data pegawai sehingga data pegawai yang tidak digunakan dapat dihapus.
     */
    public function test_us06_admin_dapat_menghapus_pegawai(): void
    {
        $admin = $this->getAdminUser();
        $this->actingAs($admin);

        $pegawai = User::factory()->create([
            'name'  => 'Pegawai Hapus',
            'email' => 'hapus@example.com',
        ]);
        $pegawai->assignRole('Pegawai');

        Livewire::test(UserList::class)
            ->call('delete', $pegawai)
            ->assertDispatched('alert', message: 'Berhasil Menghapus User');

        $this->assertDatabaseMissing('users', [
            'id' => $pegawai->id,
        ]);
    }
}
