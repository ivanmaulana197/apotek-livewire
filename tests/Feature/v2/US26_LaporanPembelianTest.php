<?php
namespace Tests\Feature\v2;

use App\Livewire\Cetak\Faktur\CetakLaporanFaktur;
use App\Models\DataMaster\Supplier;
use App\Models\Faktur\Faktur;
use Livewire\Livewire;
use Tests\Feature\BaseTest;

class US26_LaporanPembelianTest extends BaseTest
{

    public function test_us26_laporan_faktur_by_status_supplier_dan_tanggal(): void
    {
        $admin = $this->getAdminUser();
        $this->actingAs($admin);

        $supA = Supplier::create(['nama' => 'Supplier A']);
        $supB = Supplier::create(['nama' => 'Supplier B']);

        Faktur::create([
            'id_supplier'     => $supA->id,
            'status'          => 'LUNAS',
            'total_biaya'     => 10000,
            'created_at'      => now()->startOfMonth()->addDay(),
            'tgl_jatuh_tempo' => now()->addDays(10),
            'no_faktur'       => 'F-1',
            'id_user'         => $admin->id,
        ]);
        Faktur::create([
            'id_supplier'     => $supB->id,
            'status'          => 'BELUM LUNAS',
            'total_biaya'     => 5000,
            'created_at'      => now()->startOfMonth()->addDays(2),
            'tgl_jatuh_tempo' => now()->addDays(5),
            'no_faktur'       => 'F-2',
            'id_user'         => $admin->id,
        ]);

        Livewire::test(CetakLaporanFaktur::class)
            ->set('created_at', [
                'start' => now()->startOfMonth()->format('Y-m-d'),
                'end'   => now()->endOfMonth()->format('Y-m-d'),
            ])
            ->set('status', 'LUNAS')
            ->set('tgl_jatuh_tempo', 'SEMUA')
            ->set('id_suppliers', [$supA->id])
            ->assertViewHas('dataInfo', function ($dataInfo) {
                return $dataInfo->jumlah_faktur == 1
                && (int) $dataInfo->total_belanja == 10000
                && (int) $dataInfo->total_pengeluaran == 10000
                && (int) $dataInfo->total_hutang == 0;
            })
            ->assertSee('Supplier A');
    }
}
