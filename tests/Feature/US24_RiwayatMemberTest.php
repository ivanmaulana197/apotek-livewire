<?php
namespace Tests\Feature;

use App\Livewire\DataMaster\Member\MemberDetail\MemberCardInfoTransaksi;
use App\Livewire\DataMaster\Member\MemberDetail\MemberDetailIndex;
use App\Livewire\DataMaster\Member\MemberDetail\MemberListBarangTerjual;
use App\Models\DataMaster\Member;
use App\Models\HistoryBarang;
use App\Models\Transaksi\DetailTransaksiBarang;
use App\Models\Transaksi\Transaksi;
use Livewire\Livewire;

class US24_RiwayatMemberTest extends BaseTest
{

    public function test_us24_non_admin_update_filter_member_history_forbidden(): void
    {
        $pegawai = $this->getPegawaiUser();
        $this->actingAs($pegawai);
        $profesi = \App\Models\DataMaster\Profesi::create(['nama' => 'Perawat']);
        $member  = Member::create([
            'nama'       => 'Member A',
            'id_user'    => $pegawai->id,
            'id_profesi' => $profesi->id,
        ]);

        Livewire::test(MemberDetailIndex::class, ['id_member' => $member->id])
            ->set('filter_date', [
                'start' => now()->startOfMonth()->format('Y-m-d'),
                'end'   => now()->endOfMonth()->format('Y-m-d'),
            ])
            ->assertDispatched('alert', status: 'error', message: 'Kamu Tidak Memiliki Akses');
    }

    public function test_us24_admin_dapat_melihat_riwayat_pembelian_member(): void
    {
        $admin = $this->getAdminUser();
        $this->actingAs($admin);

        $member = Member::create([
            'nama'    => 'Member B',
            'id_user' => $admin->id,
        ]);

        $metode = \App\Models\DataMaster\MetodePembayaran::first();
        $trx1   = Transaksi::create([
            'id_member'            => $member->id,
            'status'               => 'TERJUAL',
            'total_bayar'          => 10000,
            'created_at'           => now()->startOfMonth()->addDay(),
            'id_user'              => $admin->id,
            'no_transaksi'         => 'MTRX-1',
            'sub_total'            => 10000,
            'potongan_diskon'      => 0,
            'pembulatan'           => 0,
            'bayar'                => 0,
            'kembalian'            => 0,
            'jenis_transaksi'      => 'UMUM',
            'shift_transaksi'      => 'PAGI',
            'id_metode_pembayaran' => $metode?->id,
        ]);
        $trx2 = Transaksi::create([
            'id_member'            => $member->id,
            'status'               => 'TERJUAL',
            'total_bayar'          => 5000,
            'created_at'           => now()->startOfMonth()->addDays(2),
            'id_user'              => $admin->id,
            'no_transaksi'         => 'MTRX-2',
            'sub_total'            => 5000,
            'potongan_diskon'      => 0,
            'pembulatan'           => 0,
            'bayar'                => 0,
            'kembalian'            => 0,
            'jenis_transaksi'      => 'UMUM',
            'shift_transaksi'      => 'MALAM',
            'id_metode_pembayaran' => $metode?->id,
        ]);
        \App\Models\Transaksi\TransaksiListPromo::create([
            'id_transaksi'  => $trx1->id,
            'tipe_promo'    => 'BARANG BONUS',
            'nominal_promo' => 0,
            'deskripsi'     => 'no promo',
        ]);
        \App\Models\Transaksi\TransaksiListPromo::create([
            'id_transaksi'  => $trx2->id,
            'tipe_promo'    => 'BARANG BONUS',
            'nominal_promo' => 0,
            'deskripsi'     => 'no promo',
        ]);

        Livewire::test(MemberCardInfoTransaksi::class, [
            'id_member'   => $member->id,
            'filter_date' => [
                'start' => now()->startOfMonth()->format('Y-m-d'),
                'end'   => now()->endOfMonth()->format('Y-m-d'),
            ],
        ])
            ->assertViewHas('data', function ($data) {
                return $data && $data->total_bayar == 15000 && $data->total_transaksi == 2;
            });

        // Test MemberListBarangTerjual (Riwayat Item)
        // Setup detail barang
        $barang  = \App\Models\Barang\Barang::factory()->create();
        $history = HistoryBarang::create([
            'id_barang'   => $barang->id,
            'jumlah'      => 2,
            'id_satuan'   => $barang->id_satuan_utama,
            'status'      => 'TERJUAL',
            'id_user'     => $admin->id,
            'tgl_expired' => [],
        ]);
        DetailTransaksiBarang::create([
            'id_transaksi'       => $trx1->id,
            'id_history_barang'  => $history->id,
            'harga'              => 5000,
            'harga_satuan_utama' => 5000,
            'total_harga'        => 10000,
            'harga_beli'         => 4000,
            'komisi'             => 0,
        ]);

        Livewire::test(MemberListBarangTerjual::class, [
            'id_member'   => $member->id,
            'filter_date' => [
                'start' => now()->startOfMonth()->format('Y-m-d'),
                'end'   => now()->endOfMonth()->format('Y-m-d'),
            ],
        ])
            ->assertViewHas('barang_terlaris', function ($collection) {
                return $collection->count() > 0;
            });
    }
}
