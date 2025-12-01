<?php
namespace App\Livewire\Informasi\InfoBarangHabis;

use App\Models\Barang\Barang;
use App\Traits\CustomDataTable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class InfoBarangHabisList extends Component
{
    use WithPagination, CustomDataTable;

    public $ceklistInfoBarang;

    public function mount()
    {
        $this->sortBy        = 'stok';
        $this->defaultSortBy = 'stok';
    }

    public function getData()
    {
        return Barang::with(['satuan_utama:id,nama'])
            ->select(['id', 'nama', 'stok', 'stok_min', 'id_satuan_utama',
                DB::raw("
            CASE
                WHEN stok <= 0 THEN 'bg-red-300 dark:bg-red-600/40'
                WHEN stok <= stok_min THEN 'bg-amber-200/60 dark:bg-amber-600/40'
                ELSE ''
            END as warna
        ")])
            ->whereColumn('stok', '<=', 'stok_min')
            ->search($this->search, ['kode', 'nama', 'stok', 'stok_min'], ['satuan_utama' => ['nama']])
            ->orderBy($this->sortBy, $this->sortDir)
            ->paginate($this->perPage);
    }

    public function render()
    {
        if (! Auth::user()->can('info-barang-habis.list')) {
            return $this->dispatch('alert', status: 'error', message: 'Kamu Tidak Memiliki Akses');
        }
        $data = $this->getData();
        return view('livewire.informasi.info-barang-habis.info-barang-habis-list', [
            'info_barang_habis' => $data,
        ]);
    }

    public function placeholder()
    {
        return view('components.placeholder.loading');
    }
}
