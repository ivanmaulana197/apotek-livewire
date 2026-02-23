<?php
namespace Tests\Feature\v2;

use App\Livewire\Cetak\Transaksi\CetakLaporanTransaksi;
use App\Models\Transaksi\Transaksi;
use App\Models\User;
use Livewire\Livewire;
use Tests\Feature\BaseTest;

class US25_LaporanPenjualanTest extends BaseTest
{

    public function test_us25_admin_bisa_mengakses_laporan_transaksi(): void
    {
        $admin = $this->getAdminUser();
        $this->actingAs($admin);
        $this->get(route('laporan.transaksi'))->assertStatus(200);
    }

    public function test_us25_laporan_transaksi_by_user_shift_dan_tanggal(): void
    {
        $admin = $this->getAdminUser();
        $this->actingAs($admin);

        $otherUser = User::factory()->create();

        $metode = \App\Models\DataMaster\MetodePembayaran::first();
        Transaksi::create([
            'status'               => 'TERJUAL',
            'id_user'              => $admin->id,
            'total_bayar'          => 20000,
            'shift_transaksi'      => 'PAGI',
            'created_at'           => now()->startOfMonth()->addDay(),
            'no_transaksi'         => 'RTX-1',
            'sub_total'            => 20000,
            'potongan_diskon'      => 0,
            'pembulatan'           => 0,
            'bayar'                => 0,
            'kembalian'            => 0,
            'jenis_transaksi'      => 'UMUM',
            'id_metode_pembayaran' => $metode?->id,
        ]);
        Transaksi::create([
            'status'               => 'TERJUAL',
            'id_user'              => $otherUser->id,
            'total_bayar'          => 5000,
            'shift_transaksi'      => 'MALAM',
            'created_at'           => now()->startOfMonth()->addDays(2),
            'no_transaksi'         => 'RTX-2',
            'sub_total'            => 5000,
            'potongan_diskon'      => 0,
            'pembulatan'           => 0,
            'bayar'                => 0,
            'kembalian'            => 0,
            'jenis_transaksi'      => 'UMUM',
            'id_metode_pembayaran' => $metode?->id,
        ]);

        $laporan = Livewire::test(CetakLaporanTransaksi::class)
            ->set('filter_date', [
                'start' => now()->startOfMonth()->format('Y-m-d'),
                'end'   => now()->endOfMonth()->format('Y-m-d'),
            ])
            ->set('by_user', (string) $admin->id)
            ->set('shift_transaksi', 'PAGI')
            ->call('getTransaksi');

        $this->assertEquals(20000, (int) $laporan->get('sum_total_bayar'));
    }
}
