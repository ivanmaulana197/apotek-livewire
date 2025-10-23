<?php

namespace App\Livewire\Promo;

use Livewire\Component;
use App\Models\Promo\Promo;
use App\Models\DataMaster\Profesi;
use App\Traits\CustomDataTable;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\WithPagination;

class PromoList extends Component
{
    use WithPagination, CustomDataTable;
    public $filters = [
        'is_aktif'     => '',
        'id_profesies' => '',
        'date'         => null,
    ];

    #[On('promo-created')]
    public function refresh()
    {}

    #[On('delete-promo')]
    public function deletePromo($id)
    {
        DB::beginTransaction();
        try {
            $promo = Promo::with(['profesies', 'promoBarang', 'promoTransaksi'])->findOrFail($id);

            // Hapus relasi profesies (pivot table)
            $promo->profesies()->detach();

            // Hapus promo barang
            $promo->promoBarang()->delete();

            // Hapus promo transaksi
            $promo->promoTransaksi()->delete();

            // Hapus promo utamanya
            $promo->delete();

            DB::commit();

            session()->flash('alert', [
                'title'   => 'Berhasil!',
                'message' => 'Promo berhasil dihapus',
                'icon'    => 'success',
            ]);
            $this->dispatch('alert', message: 'Berhasil Menghapus Promo', status: 'success');
        } catch (\Throwable $th) {
            DB::rollBack();
            $this->dispatch('alert', message: 'Terjadi kesalahan saat menghapus promo: ' . $th->getMessage(), status: 'error');

        }
    }

    public function getData()
    {
        return Promo::with(['promoBarang', 'promoTransaksi:id_promo,minimal_belanja,tipe_transaksi,diskon_persen,diskon_nominal'])
            ->search($this->search)
            ->filter($this->filters)
            ->orderBy($this->sortBy, $this->sortDir)
            ->paginate($this->perPage);
    }

    public function render()
    {
        return view('livewire.promo.promo-list', [
            'promos' => $this->getData(),
        ]);
    }

    #[Computed(persist: true, seconds: 3600, cache: true)]
    public function profesies()
    {
        return Profesi::select('id', 'nama')->get();
    }

}
