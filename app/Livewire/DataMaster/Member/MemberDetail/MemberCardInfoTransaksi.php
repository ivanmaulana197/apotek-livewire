<?php
namespace App\Livewire\DataMaster\Member\MemberDetail;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;
use Livewire\Component;

class MemberCardInfoTransaksi extends Component
{
    public $id_member;
    public $filter_date;

    #[On('update_value_filter_date')]
    public function filterDate($value)
    {
        $this->filter_date = $value;
    }

    public function getData()
    {
        $data = DB::table('transaksi')
            ->join('transaksi_list_promo', 'transaksi_list_promo.id_transaksi', '=', 'transaksi.id')
            ->where('id_member', $this->id_member)
            ->where('status', 'TERJUAL')
            ->whereBetween('transaksi.created_at', [
                "{$this->filter_date['start']} 00:00:00",
                "{$this->filter_date['end']} 23:59:59",
            ])
            ->selectRaw(
                'COUNT(*) as total_transaksi,
                    SUM(total_bayar) as total_bayar,
                    SUM(COALESCE(nominal_promo,0)) as total_benefit')
            ->first();

        return $data;
    }
    public function render()
    {
        if (Auth::user()->canAny(["member.detail.info-total-transaksi", "member.detail.info-total-belanja", "member.detail.info-total-benefit"])) {
            $data = $this->getData();
        } else {
            return $this->dispatch('alert', status: 'error', message: 'Kamu Tidak Memiliki Akses');
        }

        return view('livewire.data-master.member.member-detail.member-card-info-transaksi', compact('data'));
    }

    // public function getDataWithMember(){
    //     $data = DB::table('member')
    //         ->join('transaksi', 'transaksi.id_member', '=', 'member.id')
    //         ->join('transaksi_list_promo', function ($join) {
    //             $join->on('transaksi_list_promo.id_transaksi', '=', 'transaksi.id')
    //                 ->where('transaksi_list_promo.tipe_promo', '=', 'BARANG BONUS');
    //         })
    //         ->where('status', 'TERJUAL')
    //         ->whereBetween('transaksi.created_at', [
    //             "{$this->filter_date['start']} 00:00:00",
    //             "{$this->filter_date['end']} 23:59:59",
    //         ])
    //         ->where('member.id', '=', $this->id_member)
    //         ->selectRaw(
    //             'member.*,
    //             COUNT(*) as total_transaksi,
    //             SUM(total_bayar) as total_bayar,
    //             SUM(COALESCE(nominal_promo,0)) as total_benefit')
    //         ->groupBy('member.id')
    //         ->first();
    // }

}
