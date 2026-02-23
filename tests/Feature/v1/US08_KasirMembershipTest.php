<?php
namespace Tests\Feature\v1;

use App\Livewire\KasirCadangan\KasirPembayaran;
use App\Models\DataMaster\MetodePembayaran;
use App\Models\DataMaster\Profesi;
use App\Models\Kasir\PesananKasir;
use App\Models\Setting;
use Livewire\Livewire;
use Tests\Feature\BaseTest;

class US08_KasirMembershipTest extends BaseTest
{
    public function test_us08_transaksi_member_valid_mengisi_id_member(): void
    {
        $pegawai = $this->getPegawaiUser();
        $pegawai->givePermissionTo(['kasir', 'kasir-show-all']);
        $this->actingAs($pegawai);

        $profesi  = Profesi::firstOrCreate(['nama' => 'UMUM']);
        $member   = \App\Models\DataMaster\Member::create(['nama' => 'MEMBER X', 'id_profesi' => $profesi->id, 'id_user' => $pegawai->id, 'alamat' => 'JL', 'no_hp' => '08']);
        $metodeId = MetodePembayaran::firstOrCreate(['nama' => 'TUNAI'])->id;

        $pesanan = PesananKasir::create([
            'id_user'              => $pegawai->id,
            'no_pesanan_kasir'     => 'PK-MEM-1',
            'mode_kasir'           => 'UMUM',
            'mode_shift'           => 'PAGI',
            'total_harga'          => 0,
            'id_metode_pembayaran' => $metodeId,
        ]);

        $setting = Setting::first();
        Livewire::test(KasirPembayaran::class, ['pesanan_kasir' => $pesanan->fresh(), 'setting' => $setting])
            ->set('id_member', $member->id)
            ->assertSet('id_member', $member->id);
    }

    public function test_us08_member_tidak_ditemukan_error(): void
    {
        $pegawai = $this->getPegawaiUser();
        $pegawai->givePermissionTo(['kasir', 'kasir-show-all']);
        $this->actingAs($pegawai);

        $metodeId = MetodePembayaran::firstOrCreate(['nama' => 'TUNAI'])->id;
        $pesanan  = PesananKasir::create([
            'id_user'              => $pegawai->id,
            'no_pesanan_kasir'     => 'PK-MEM-2',
            'mode_kasir'           => 'UMUM',
            'mode_shift'           => 'PAGI',
            'total_harga'          => 0,
            'id_metode_pembayaran' => $metodeId,
        ]);

        $setting = Setting::first();
        Livewire::test(KasirPembayaran::class, ['pesanan_kasir' => $pesanan->fresh(), 'setting' => $setting])
            ->set('id_member', '999999')
            ->assertHasErrors(['id_member' => 'exists']);
    }
}
