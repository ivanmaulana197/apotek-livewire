<?php

namespace App\Livewire\Kasir;

use App\Models\Promo\PromoTransaksi;
use Livewire\Attributes\On;
use Livewire\Component;

class DiskonTransaksiList extends Component
{
    public $diskons;
    public $id_promo_transaksi_selected;
    public $sub_total;

    public function mount(){
        $this->diskons = collect();
    }

    #[On('list-diskon-transaksi')]
    public function getDiskon($id_promo_transaksi_selected , $id_promo_transaksis = null, $sub_total = null)
    {
        if($id_promo_transaksis){
            $this->diskons = PromoTransaksi::whereIn('id', $id_promo_transaksis)->get();
        }else{
            $this->diskons = collect();
        }
        $this->sub_total = $sub_total;
        $this->id_promo_transaksi_selected = $id_promo_transaksi_selected;
    }

    public function selectDiskon($id_promo_transaksi = null){

        $this->id_promo_transaksi_selected = $id_promo_transaksi;
        $this->dispatch('select-diskon-transaksi', id_promo_transaksi: $id_promo_transaksi);
         $this->dispatch('toggle-modal-diskon_transaksi', diskon_transaksiModal: false);
    }

    public function render()
    {
        return view('livewire.kasir.diskon-transaksi-list');
    }
}
