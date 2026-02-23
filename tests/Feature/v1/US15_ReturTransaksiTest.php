<?php
namespace Tests\Feature\v1;

use App\Livewire\Transaksi\TransaksiList;
use App\Models\Barang\Barang;
use App\Models\Barang\StokBatchBarang;
use App\Models\DataMaster\MetodePembayaran;
use App\Models\HistoryBarang;
use App\Models\Transaksi\DetailTransaksiBarang;
use App\Models\Transaksi\Transaksi;
use Livewire\Livewire;
use Tests\Feature\BaseTest;

class US15_ReturTransaksiTest extends BaseTest
{
    /**
     * Memproses retur transaksi penjualan
     * Mengembalikan stok barang sesuai transaksi yang diretur
     * Mengubah status transaksi setelah proses retur
     */
    public function test_us15_proses_retur_transaksi_valid(): void
    {
        $admin = $this->getAdminUser();
        $this->actingAs($admin);

        $metodeId   = MetodePembayaran::firstOrCreate(['nama' => 'TUNAI'])->id;
        $barang     = Barang::factory()->create(['stok' => 5]);
        $stokAwal   = $barang->stok;
        $tglExpired = now()->addMonths(6)->toDateString();

        // Setup Stok Batch Awal (Optional, tapi bagus untuk verifikasi penambahan)
        $batch = StokBatchBarang::create([
            'id_barang'   => $barang->id,
            'tgl_expired' => $tglExpired,
            'jumlah'      => 2, // Misalnya sisa 2 di batch ini
        ]);
        $stokBatchAwal = $batch->jumlah;

        // 1. Buat Transaksi
        $trx = Transaksi::create([
            'no_transaksi'         => 'TRX-RETUR-01',
            'sub_total'            => 10000,
            'potongan_diskon'      => 0,
            'total_bayar'          => 10000,
            'pembulatan'           => 0,
            'bayar'                => 10000,
            'kembalian'            => 0,
            'jenis_transaksi'      => 'UMUM',
            'shift_transaksi'      => 'PAGI',
            'status'               => 'TERJUAL',
            'id_metode_pembayaran' => $metodeId,
            'id_user'              => $admin->id,
        ]);

        // 2. Buat Detail Transaksi (1 item terjual dari batch tersebut)
        $history = HistoryBarang::create([
            'id_barang'   => $barang->id,
            'jumlah'      => 1,
            'id_satuan'   => $barang->id_satuan_utama,
            'status'      => 'TERJUAL',
            'id_user'     => $admin->id,
            'tgl_expired' => [['tgl' => $tglExpired, 'jumlah' => 1]],
        ]);

        DetailTransaksiBarang::create([
            'id_transaksi'       => $trx->id,
            'id_history_barang'  => $history->id,
            'harga'              => 10000,
            'harga_satuan_utama' => 10000,
            'komisi'             => 0,
            'total_harga'        => 10000,
            'harga_beli'         => 8000,
        ]);

        // 3. Proses Retur
        Livewire::test(TransaksiList::class)
            ->call('retur', $trx->id)
            ->assertDispatched('alert', status: 'success');

        // 4. Verifikasi

        // Poin 3: Mengubah status transaksi setelah proses retur
        $this->assertEquals('RETUR', $trx->fresh()->status, 'Status transaksi harus berubah jadi RETUR');

        // Poin 2: Mengembalikan stok barang sesuai transaksi yang diretur
        // Cek Total Stok
        $this->assertEquals($stokAwal + 1, $barang->fresh()->stok, 'Stok total barang harus bertambah 1');

        // Cek Stok Batch (Expired Date)
        $this->assertEquals(
            $stokBatchAwal + 1,
            StokBatchBarang::where('id', $batch->id)->value('jumlah'),
            'Stok batch barang harus bertambah 1 sesuai tanggal expired'
        );
    }

    /**
     * Memperbarui stok dan catatan keuangan
     * (Verifikasi bahwa transaksi yang tidak valid tidak bisa diretur)
     */
    public function test_us15_validasi_transaksi_tidak_ditemukan(): void
    {
        $admin = $this->getAdminUser();
        $this->actingAs($admin);

        Livewire::test(TransaksiList::class)
            ->call('retur', 'TRX-GAIB')
            ->assertDispatched('alert', status: 'error');
    }

    /**
     * Memastikan hanya admin yang bisa melakukan retur
     */
    public function test_us15_akses_non_admin_ditolak(): void
    {
        $pegawai = $this->getPegawaiUser();
        $this->actingAs($pegawai);

        Livewire::test(TransaksiList::class)
            ->call('retur', 'TRX-ANY')
            ->assertDispatched('alert', status: 'error', message: 'Kamu Tidak Memiliki Akses');
    }
}
