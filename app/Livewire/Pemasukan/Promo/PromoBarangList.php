<?php
namespace App\Livewire\Pemasukan\Promo;

use App\Models\DataMaster\Member;
use App\Models\Promo\PromoBarang;
use Livewire\Attributes\On;
use Livewire\Component;

class PromoBarangList extends Component
{
    public $id_promo_barang_selected;
    public $promos;

    public function mount()
    {
        $this->promos = collect();
    }

    #[On('list-promo-barang')]
    public function getPromo($id_promo_barang_selected, $id_barang, $total_jumlah, $id_member = null)
    {
        $id_profesi = null;
        if ($id_member) {
            $member     = Member::find($id_member);
            $id_profesi = $member->id_profesi;
        }

        if ($id_barang) {
            $this->promos = PromoBarang::query()
                ->with(['barang.opsi_satuan:id,nama', 'satuan:id,nama'])
                ->where('id_barang', $id_barang)
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
                })->get()
                ->filter(function ($barangPromo) use ($total_jumlah) {
                    // Ambil hanya promo yang memenuhi syarat
                    return $barangPromo->SyaratQtyDalamSatuanUtama <= $total_jumlah;
                });
        } else {
            $this->promos = collect();
        }
        $this->id_promo_barang_selected = $id_promo_barang_selected;
    }

    public function render()
    {
        if ($this->promos->isNotEmpty()) {
            $this->promos->loadMissing(['promo.profesies', 'satuan']);
        }
        return view('livewire.pemasukan.promo.promo-barang-list');
    }
}
