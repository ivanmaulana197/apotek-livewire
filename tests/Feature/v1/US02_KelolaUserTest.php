<?php
namespace Tests\Feature\v1;

use App\Livewire\DataMaster\User\UserForm;
use App\Livewire\DataMaster\User\UserList;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Livewire;
use Tests\Feature\BaseTest;

class US02_KelolaUserTest extends BaseTest
{
    public function test_us02_admin_dapat_menambah_pegawai_valid(): void
    {
        $admin = $this->getAdminUser();
        $this->actingAs($admin);
        $roleKasirId = \App\Models\Spatie\Role::where('name', 'Pegawai')->value('id');

        Livewire::test(UserForm::class)
            ->set('name', 'Pegawai Kasir')
            ->set('email', 'kasir@example.com')
            ->set('password', 'password')
            ->set('password_confirmation', 'password')
            ->set('roles_selected', [$roleKasirId])
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('users', ['email' => 'kasir@example.com']);
        $user = User::where('email', 'kasir@example.com')->first();
        $this->assertTrue($user->hasRole('Pegawai'));
    }

    public function test_us02_validasi_field_wajib_nama_kosong(): void
    {
        $admin = $this->getAdminUser();
        $this->actingAs($admin);
        $roleKasirId = \App\Models\Spatie\Role::where('name', 'Pegawai')->value('id');

        Livewire::test(UserForm::class)
            ->set('name', '')
            ->set('email', 'kasir2@example.com')
            ->set('password', 'password')
            ->set('password_confirmation', 'password')
            ->set('roles_selected', [$roleKasirId])
            ->call('save')
            ->assertHasErrors(['name' => 'required']);
    }

    public function test_us02_update_data_pegawai(): void
    {
        $admin = $this->getAdminUser();
        $this->actingAs($admin);
        $u = User::factory()->create(['name' => 'Old Name']);
        $u->assignRole('Pegawai');
        $roleGudangId = \App\Models\Spatie\Role::where('name', 'Gudang')->value('id');

        Livewire::test(UserForm::class)
            ->call('editFormUser', $u)
            ->set('name', 'New Name')
            ->set('roles_selected', [$roleGudangId])
            ->call('update')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('users', ['id' => $u->id, 'name' => 'New Name']);
        $this->assertTrue($u->fresh()->hasRole('Gudang'));
    }

    public function test_us02_hapus_pegawai_melalui_list(): void
    {
        $admin = $this->getAdminUser();
        $this->actingAs($admin);
        $u = User::factory()->create(['email' => 'delete@example.com']);

        Livewire::test(UserList::class)
            ->dispatch('delete-user', $u);

        $this->assertDatabaseMissing('users', ['email' => 'delete@example.com']);
        $this->assertFalse(Auth::attempt(['email' => 'delete@example.com', 'password' => 'password']));
    }

    public function test_us02_non_admin_tidak_boleh_kelola_pegawai(): void
    {
        $pegawai = $this->getPegawaiUser();
        $this->actingAs($pegawai);

        Livewire::test(UserForm::class)
            ->set('name', 'No Access')
            ->set('email', 'noaccess@example.com')
            ->set('password', 'password')
            ->set('password_confirmation', 'password')
            ->set('roles_selected', [\App\Models\Spatie\Role::where('name', 'Kasir')->value('id')])
            ->call('save');

        $this->assertDatabaseMissing('users', ['email' => 'noaccess@example.com']);
    }
}
