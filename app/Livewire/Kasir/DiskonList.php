<?php
namespace App\Livewire\Kasir;

use App\Models\DataMaster\Member;
use App\Models\Kasir\PesananKasir;
use App\Models\Promo\PromoBarang;
use Livewire\Attributes\On;
use Livewire\Component;

class DiskonList extends Component
{
    public $pesanan_kasir;
    public $id_promo_barang_selected;
    public $diskons;

    public function mount($id_pesanan_kasir)
    {
        $this->pesanan_kasir = PesananKasir::with('barang_pesanan_kasir')->find($id_pesanan_kasir);
        $this->diskons = collect();
    }

    #[On('get-diskon-from-inputan')]
    public function getDiskonFromInputan($id_promo_barang_selected = null,$id_barang, $jumlah_dalam_satuan_utama, $id_member = null)
    {
        $this->id_promo_barang_selected = $id_promo_barang_selected;
        $id_profesi           = null;
        // dd($id_member);
        if ($id_member) {
            $member     = Member::find($id_member);
            $id_profesi = $member->id_profesi;
        }

        $promoBarang = PromoBarang::with(['promo.profesies', 'satuan'])
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
            ->filter(function ($barangPromo) use ($jumlah_dalam_satuan_utama) {
                // Ambil hanya promo yang memenuhi syarat
                return $barangPromo->SyaratQtyDalamSatuanUtama <= $jumlah_dalam_satuan_utama;
            })
            ->values();

        $this->diskons = $promoBarang;
    }

    #[On('list-diskon')]
    public function getDiskon($id_promo_barang_selected , $id_promo_barangs = null)
    {
        if($id_promo_barangs){
            $this->diskons = PromoBarang::with(['promo.profesies', 'satuan'])->whereIn('id', $id_promo_barangs)->get();
        }else{
            $this->diskons = collect();
        }
        $this->id_promo_barang_selected = $id_promo_barang_selected;
    }

    public function selectDiskon($id_promo_barang = null, $id_barang = null){

        $this->id_promo_barang_selected = $id_promo_barang;
        $this->dispatch('select-diskon', id_promo_barang: $id_promo_barang, id_barang: $id_barang);
         $this->dispatch('toggle-modal-diskon', diskonModal: false);
    }


    public function render()
    {
        return view('livewire.kasir.diskon-list');
    }
}