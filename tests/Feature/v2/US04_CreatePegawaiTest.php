<?php
namespace Tests\Feature\v2;

use App\Livewire\DataMaster\User\UserForm;
use App\Models\Spatie\Role;
use App\Models\User;
use Livewire\Livewire;
use Tests\Feature\BaseTest;

class US04_CreatePegawaiTest extends BaseTest
{
    /**
     * US-04 Sebagai admin, saya ingin menambahkan data pegawai sehingga pegawai baru dapat tercatat di sistem.
     */
    public function test_us04_admin_dapat_menambah_pegawai(): void
    {
        $admin = $this->getAdminUser();
        $this->actingAs($admin);

        $pegawaiRole = Role::where('name', 'Pegawai')->first();

        Livewire::test(UserForm::class)
            ->set('name', 'Pegawai Baru')
            ->set('email', 'pegawaibaru@example.com')
            ->set('password', 'password123')
            ->set('password_confirmation', 'password123')
            ->set('roles_selected', [$pegawaiRole->id])
            ->call('save')
            ->assertDispatched('alert', status: 'success');

        $this->assertDatabaseHas('users', [
            'email' => 'pegawaibaru@example.com',
            'name'  => 'Pegawai Baru',
        ]);

        $user = User::where('email', 'pegawaibaru@example.com')->first();
        $this->assertTrue($user->hasRole('Pegawai'));
    }
}
