<?php
namespace Tests\Feature\v3\iterasi_3;

use App\Livewire\DataMaster\Member\MemberList;
use App\Models\DataMaster\Member;
use App\Models\DataMaster\Profesi;
use Livewire\Livewire;
use Tests\Feature\BaseTest;

class US09_DeleteMemberTest extends BaseTest
{
    /**
     * US-09 Sebagai admin, saya ingin menghapus data member sehingga data member yang tidak aktif dapat dihapus.
     */
    public function test_us09_admin_dapat_menghapus_member(): void
    {
        $admin = $this->getAdminUser();
        $this->actingAs($admin);

        $profesi = Profesi::create(['nama' => 'Umum']);

        $member = Member::create([
            'nama'       => 'Member Hapus',
            'id_profesi' => $profesi->id,
            'no_hp'      => '0822222222',
            'alamat'     => 'Alamat Hapus',
            'id_user'    => $admin->id,
        ]);

        Livewire::test(MemberList::class)
            ->call('delete', $member)
            ->assertDispatched('alert', message: 'Berhasil Menghapus Member');

        $this->assertDatabaseMissing('member', [
            'id' => $member->id,
        ]);
    }
}
