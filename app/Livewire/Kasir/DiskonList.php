<?php
namespace App\Livewire\Kasir;

use App\Models\Promo\PromoBarang;
use Livewire\Attributes\On;
use Livewire\Component;

class DiskonList extends Component
{
    public $id_promo_barang_selected;
    public $diskons;

    public function mount()
    {
        $this->diskons = collect();
    }

    #[On('list-diskon')]
    public function getDiskon($id_promo_barang_selected, $id_promo_barangs = null)
    {
        if ($id_promo_barangs) {
            $this->diskons = PromoBarang::with(['promo.profesies', 'satuan'])->whereIn('id', $id_promo_barangs)->get();
        } else {
            $this->diskons = collect();
        }
        $this->id_promo_barang_selected = $id_promo_barang_selected;
    }

    public function selectDiskon($id_promo_barang = null, $id_barang = null)
    {

        $this->id_promo_barang_selected = $id_promo_barang;
        $this->dispatch('select-diskon', id_promo_barang: $id_promo_barang, id_barang: $id_barang);
        $this->dispatch('toggle-modal-diskon', diskonModal: false);
    }

    public function render()
    {
        return view('livewire.kasir.diskon-list');
    }
}
