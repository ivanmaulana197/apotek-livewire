<?php
namespace Tests\Feature\v1;

use App\Livewire\Home\HomeIndex;
use App\Models\Transaksi\Transaksi;
use Livewire\Livewire;
use Tests\Feature\BaseTest;

class US17_DashboardServiceTest extends BaseTest
{
    public function test_non_admin_tidak_bisa_mengakses_grafik(): void
    {
        $pegawai = $this->getPegawaiUser();
        $this->actingAs($pegawai);

        Livewire::test(HomeIndex::class)
            ->set('tipe_chart', 'TRANSAKSI')
            ->call('fetchData')
            ->assertDispatched('alert', status: 'error', message: 'Kamu Tidak Memiliki Akses');
    }

    public function test_admin_bisa_melihat_chart_pendapatan_dan_transaksi(): void
    {
        $admin = $this->getAdminUser();
        $this->actingAs($admin);

        $metode = \App\Models\DataMaster\MetodePembayaran::first();
        Transaksi::create([
            'status'               => 'TERJUAL',
            'total_bayar'          => 10000,
            'created_at'           => now()->subDay(),
            'id_user'              => $admin->id,
            'no_transaksi'         => 'TRX-1',
            'sub_total'            => 10000,
            'potongan_diskon'      => 0,
            'pembulatan'           => 0,
            'bayar'                => 0,
            'kembalian'            => 0,
            'jenis_transaksi'      => 'UMUM',
            'shift_transaksi'      => 'PAGI',
            'id_metode_pembayaran' => $metode?->id,
        ]);
        Transaksi::create([
            'status'               => 'TERJUAL',
            'total_bayar'          => 5000,
            'created_at'           => now(),
            'id_user'              => $admin->id,
            'no_transaksi'         => 'TRX-2',
            'sub_total'            => 5000,
            'potongan_diskon'      => 0,
            'pembulatan'           => 0,
            'bayar'                => 0,
            'kembalian'            => 0,
            'jenis_transaksi'      => 'UMUM',
            'shift_transaksi'      => 'MALAM',
            'id_metode_pembayaran' => $metode?->id,
        ]);

        $comp = Livewire::test(HomeIndex::class)
            ->set('chartDate', [
                'start' => now()->startOfMonth()->toDateString(),
                'end'   => now()->toDateString(),
            ])
            ->call('fetchData');

        $this->assertIsArray($comp->get('chartPendapatan') ?? []);

        $comp->set('tipe_chart', 'TRANSAKSI')
            ->call('fetchData');

        $this->assertIsArray($comp->get('chartTransaksi') ?? []);
    }
}
