<?php
namespace App\Livewire\Promo\PromoDetail;

use App\Models\Promo\Promo;
use Livewire\Component;

class PromoDetail extends Component
{
    public $promo;

    public function mount($id)
    {
        $this->promo = Promo::with(['promoBarang.barang:id,nama', 'promoBarang.barang_bonus:id,nama', 'profesies:id,nama', 'promoTransaksi:id_promo,minimal_belanja,tipe_transaksi,diskon_persen,diskon_nominal'])
            ->findOrFail($id);
        // dd($this->promo);
    }
    public function render()
    {
        return view('livewire.promo.promo-detail.promo-detail');
    }
}
