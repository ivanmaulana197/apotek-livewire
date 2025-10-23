<?php
namespace App\Livewire\Promo\PromoDetail;

use App\Models\Promo\Promo;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\On;
use Livewire\Component;

#[Lazy(isolate: false)]
class PromoDetailModal extends Component
{
    public $promo;

    #[On('detail-promo')]
    public function detailPromo($id_promo)
    {
        $this->getData($id_promo);
    }

    public function getData($id)
    {
        $this->promo = Promo::with(['promoBarang.barang:id,nama', 'promoBarang.barang_bonus:id,nama', 'profesies:id,nama', 'promoTransaksi:id_promo,minimal_belanja,tipe_transaksi,diskon_persen,diskon_nominal'])
            ->findOrFail($id);
        // dd($this->promo);
    }

    public function render()
    {
        return view('livewire.promo.promo-detail.promo-detail-modal');
    }

    public function placeholder()
    {
        return view('components.placeholder.loading');
    }
}
