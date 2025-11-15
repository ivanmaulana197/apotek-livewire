<?php
namespace App\Livewire\Cetak\Member;

use App\Models\DataMaster\Member;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;

class CetakMemberDetail extends Component
{

    public $id_member;
    #[Url( as : 'date')]
    public $filter_date;

    public function mount()
    {
        $this->filter_date = [
            'start' => request('date.start', now()->startOfMonth()->format('Y-m-d')),
            'end'   => request('date.end', now()->endOfMonth()->format('Y-m-d')),
        ];
    }

    public function getData()
    {
        $data = Member::query()
            ->with(['transaksi' => function ($q) {
                $q->select([
                    'id', 'no_transaksi', 'created_at', 'id_user', 'id_metode_pembayaran',
                    'sub_total', 'potongan_diskon', 'pembulatan', 'total_bayar', 'id_member',
                    'shift_transaksi', 'status',
                ])
                    ->where('status', 'TERJUAL')
                    ->whereBetween('created_at', [
                        $this->filter_date['start'] . ' 00:00:00',
                        $this->filter_date['end'] . ' 23:59:59',
                    ])
                    ->with([
                        'user:id,name',
                        'metode_pembayaran:id,nama',
                        'transaksi_list_promo:id,id_transaksi,tipe_promo,id_barang,deskripsi,nominal_promo',
                        'detail_transaksi.detail_barang' => function ($q) {
                            $q->select(['id', 'id_barang', 'jumlah', 'id_satuan'])
                                ->with([
                                    'satuan:id,nama',
                                    'barang:id,nama',
                                ]);
                        },
                    ]);
            },
                'profesi:id,nama',
            ])
            ->find($this->id_member);

        $data->transaksi->transform(function ($trx) {
            $trx->total_promo_barang_bonus = $trx->transaksi_list_promo
                ->where('tipe_promo', 'BARANG BONUS')
                ->sum('nominal_promo');
            $trx->benefit = $trx->transaksi_list_promo->sum('nominal_promo');

            $trx->promoBarang = $trx->transaksi_list_promo
                ->where('tipe_promo', '!=', 'TRANSAKSI')
                ->keyBy('id_barang');

            return $trx;
        });
        // dd($data);
        return $data;
    }

    #[Layout('components.layouts.cetak-laporan')]
    public function render()
    {
        $data                     = $this->getData();
        $data['jumlah_transaksi'] = $data->transaksi->count();
        $data['total_bayar']      = $data->transaksi->sum('total_bayar');
        $data['total_benefit']    = $data->transaksi->sum('benefit');
        // dd($data);
        return view('livewire.cetak.member.cetak-member-detail', compact('data'));
    }
}
