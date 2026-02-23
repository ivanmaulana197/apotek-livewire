<?php
namespace Tests\Feature;

use App\Livewire\DataMaster\Member\MemberForm;
use App\Models\DataMaster\Member;
use App\Models\DataMaster\Profesi;
use Livewire\Livewire;
use Tests\Feature\BaseTest;

class US08_EditMemberTest extends BaseTest
{
    /**
     * US-08 Sebagai admin, saya ingin mengubah data member sehingga data member tetap akurat.
     */
    public function test_us08_admin_dapat_mengubah_data_member(): void
    {
        $admin = $this->getAdminUser();
        $this->actingAs($admin);

        $profesi     = Profesi::create(['nama' => 'Umum']);
        $profesiBaru = Profesi::create(['nama' => 'VIP']);

        $member = Member::create([
            'nama'       => 'Member Lama',
            'id_profesi' => $profesi->id,
            'no_hp'      => '0811111111',
            'alamat'     => 'Alamat Lama',
            'id_user'    => $admin->id,
        ]);

        Livewire::test(MemberForm::class)
            ->call('editFormMember', $member)
            ->set('nama', 'Member Update')
            ->set('id_profesi', $profesiBaru->id)
            ->call('update')
            ->assertDispatched('alert', status: 'info');

        $this->assertDatabaseHas('member', [
            'id'         => $member->id,
            'nama'       => 'MEMBER UPDATE', // Uppercase
            'id_profesi' => $profesiBaru->id,
        ]);
    }
}
