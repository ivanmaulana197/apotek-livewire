<?php
namespace Tests\Feature\v1;

use App\Livewire\DataMaster\Member\MemberForm;
use App\Livewire\DataMaster\Member\MemberList;
use App\Models\DataMaster\Profesi;
use Livewire\Livewire;
use Tests\Feature\BaseTest;

class US03_KelolaMemberTest extends BaseTest
{
    public function test_us03_admin_dapat_menambah_member(): void
    {
        $admin = $this->getAdminUser();
        $this->actingAs($admin);
        $profesi = Profesi::firstOrCreate(['nama' => 'DOKTER']);

        Livewire::test(MemberForm::class)
            ->set('nama', 'Member Satu')
            ->set('id_profesi', $profesi->id)
            ->set('no_hp', '0812')
            ->set('alamat', 'Jl. Test')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('member', ['nama' => 'MEMBER SATU']);
    }

    public function test_us03_validasi_data_member_nama_kosong(): void
    {
        $admin = $this->getAdminUser();
        $this->actingAs($admin);
        $profesi = Profesi::firstOrCreate(['nama' => 'DOKTER']);

        Livewire::test(MemberForm::class)
            ->set('nama', '')
            ->set('id_profesi', $profesi->id)
            ->call('save')
            ->assertHasErrors(['nama' => 'required']);
    }

    public function test_us03_update_data_member(): void
    {
        $admin = $this->getAdminUser();
        $this->actingAs($admin);
        $profesi = Profesi::firstOrCreate(['nama' => 'DOKTER']);
        $member  = \App\Models\DataMaster\Member::factory()->create(['nama' => 'MEMBER LAMA', 'id_profesi' => $profesi->id]);

        Livewire::test(MemberForm::class)
            ->call('editFormMember', $member)
            ->set('nama', 'Member Baru')
            ->set('id_profesi', $profesi->id)
            ->call('update')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('member', ['id' => $member->id, 'nama' => 'MEMBER BARU']);
    }

    public function test_us03_hapus_member(): void
    {
        $admin = $this->getAdminUser();
        $this->actingAs($admin);
        $member = \App\Models\DataMaster\Member::factory()->create(['nama' => 'HAPUS']);

        Livewire::test(MemberList::class)
            ->dispatch('delete-member', $member);

        $this->assertDatabaseMissing('member', ['id' => $member->id]);
    }

    public function test_us03_akses_non_admin_ke_fitur_member_ditolak(): void
    {
        $pegawai = $this->getPegawaiUser();
        $this->actingAs($pegawai);
        $this->get(route('data-master.member'))->assertStatus(200);
    }
}
