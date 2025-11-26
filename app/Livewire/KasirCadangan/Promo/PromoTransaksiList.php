<?php
namespace App\Livewire\KasirCadangan\Promo;

use App\Models\DataMaster\Member;
use App\Models\Promo\PromoTransaksi;
use Livewire\Attributes\On;
use Livewire\Component;

class PromoTransaksiList extends Component
{
    public $id_promo_transaksi_selected;
    public $promo_transaksis;
    public $total_belanja;
    public function mount()
    {
        $this->promo_transaksis = collect();
    }

    #[On('list-promo-transaksi')]
    public function getPromoTransaksiProperty($id_promo_transaksi_selected = null, $id_member = null, $total_belanja = 0)
    {
        $id_profesi = null;
        // dd($id_member);
        if ($id_member) {
            $member     = Member::find($id_member);
            $id_profesi = $member->id_profesi;
        }
        // dd($id_profesi);

        $this->promo_transaksis = PromoTransaksi::where('minimal_belanja', '<=', $total_belanja)
            ->whereHas('promo', function ($q) use ($id_profesi) {
                $q->aktif()
                    ->when($id_profesi, function ($q) use ($id_profesi) {
                        $q->where(function ($query) use ($id_profesi) {
                            $query->whereDoesntHave('profesies') // promo umum
                                ->orWhereHas('profesies', fn($q) => $q->where('id_profesi', $id_profesi));
                        });
                    }, function ($q) {
                        // Jika user tidak punya profesi, ambil hanya promo umum
                        $q->whereDoesntHave('profesies');
                    });
            })
            ->get()
            ->sortByDesc('minimal_belanja');

        $this->id_promo_transaksi_selected = $id_promo_transaksi_selected;
        $this->total_belanja               = $total_belanja;
    }

    public function selectPromoTransaksi($id_promo_transaksi = null)
    {
        // dd($id_promo_transaksi);
        $this->id_promo_transaksi_selected = $id_promo_transaksi;
        $this->dispatch('select-promo_transaksi', id_promo_transaksi: $id_promo_transaksi);
        $this->dispatch('toggle-modal-promo_transaksi', promo_transaksiModal: false);
    }
    public function render()
    {
        return view('livewire.kasir-cadangan.promo.promo-transaksi-list');
    }
}
