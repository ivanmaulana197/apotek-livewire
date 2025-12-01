<?php
namespace App\Livewire\Transaksi;

use App\Helpers\FormatHelper;
use App\Models\Transaksi\Transaksi;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class TransaksiBarangDetailModal extends Component
{
    public $transaksi;

    #[On('set-transaksi-barang')]
    public function setTransaksiBarang($id_transaksi)
    {

        $this->transaksi = Transaksi::with([
            'detail_transaksi.detail_barang' => function ($q) {
                $q->with(['barang:id,nama', 'satuan:id,nama'])->orderBy('id_barang');
                // $q->with(['detail_barang.ba', 'barang:id,nama', 'satuan:id,nama'])->orderBy('id_barang');
            },
            'transaksi_list_promo.barang:id,nama',
        ])->select(['id'])
            ->findOrFail($id_transaksi);

        if (! Auth::user()->can('transaksi.detail-all') && ! (Auth::user()->can('transaksi.detail-own') && $this->transaksi->id_user == Auth::user()->id)) {
            return $this->dispatch('alert', status: 'error', message: 'Kamu Tidak Memiliki Akses');
        }

        $diskonPromos = $this->transaksi->transaksi_list_promo
            ->where('tipe_promo', '!=', 'TRANSAKSI')
            ->keyBy('id_barang');

        $this->transaksi->detail_transaksi->map(function ($item, $key) use ($diskonPromos) {
            $next               = $this->transaksi->detail_transaksi->get($key + 1)?->detail_barang;
            $item->harga        = FormatHelper::formatNumberToString($item->harga);
            $item->total_harga  = FormatHelper::formatNumberToString($item->total_harga);
            $item->promo_barang = (! $next || $item->detail_barang->id_barang != $next->id_barang)
                ? ($diskonPromos[$item->detail_barang->id_barang] ?? null)
                : null;
            return $item;
        });
    }
    // #[On('set-transaksi-barang')]
    // public function setTransaksiBarang($id_transaksi)
    // {
    //     $this->transaksi = Transaksi::with([
    //         'transaksi_barang' => function ($q) {
    //             $q->with(['detail_transaksi', 'barang:id,nama', 'satuan:id,nama'])->orderBy('id_barang');
    //         },
    //         'transaksi_list_promo.barang:id,nama',
    //     ])->select(['id'])
    //         ->findOrFail($id_transaksi);

    //     $diskonPromos = $this->transaksi->transaksi_list_promo
    //         ->where('tipe_promo', '!=', 'TRANSAKSI')
    //         ->keyBy('id_barang');
    //     // dd($diskonPromos);

    //     $this->transaksi->transaksi_barang->map(function ($item, $key) use ($diskonPromos) {
    //         $next                                = $this->transaksi->transaksi_barang->get($key + 1);
    //         $item->detail_transaksi->harga       = FormatHelper::formatNumberToString($item->detail_transaksi->harga);
    //         $item->detail_transaksi->total_harga = FormatHelper::formatNumberToString($item->detail_transaksi->total_harga);
    //         $item->promo_barang                  = (! $next || $item->id_barang != $next->id_barang)
    //             ? ($diskonPromos[$item->id_barang] ?? null)
    //             : null;
    //         return $item;
    //     });
    // }

    public function render()
    {
        return view('livewire.transaksi.transaksi-barang-detail-modal');
    }
}
