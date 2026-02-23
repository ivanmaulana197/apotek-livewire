<?php
namespace Tests\Feature\v3\iterasi_3;

use App\Livewire\DataMaster\Member\MemberForm;
use App\Models\DataMaster\Profesi;
use Livewire\Livewire;
use Tests\Feature\BaseTest;

class US07_CreateMemberTest extends BaseTest
{
    /**
     * US-07 Sebagai admin, saya ingin menambahkan data member sehingga pelanggan dapat terdaftar sebagai member.
     */
    public function test_us07_admin_dapat_menambah_member(): void
    {
        $admin = $this->getAdminUser();
        $this->actingAs($admin);

        $profesi = Profesi::create(['nama' => 'Umum']);

        Livewire::test(MemberForm::class)
            ->set('nama', 'Member Baru')
            ->set('id_profesi', $profesi->id)
            ->set('no_hp', '08123456789')
            ->set('alamat', 'Jl. Test No. 1')
            ->call('save')
            ->assertDispatched('alert', status: 'success');

        $this->assertDatabaseHas('member', [
            'nama'       => 'MEMBER BARU',
            'no_hp'      => '08123456789',
            'id_profesi' => $profesi->id,
        ]);
    }
}
