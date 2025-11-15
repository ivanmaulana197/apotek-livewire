<?php
namespace App\Livewire\Cetak;

use App\Models\StokOpnameHistory;
use Livewire\Attributes\Layout;
use Livewire\Component;

class CetakStokOpname extends Component
{
    public $stokOpnames;
    public $created_at;
    public function mount($created_at)
    {
        $this->created_at  = $created_at;
        $this->stokOpnames = StokOpnameHistory::query()
            ->with('barang:id,nama,id_satuan_utama', 'barang.satuan_utama:id,nama')
            ->where('created_at', $created_at)->get();
    }

    #[Layout('components.layouts.cetak-laporan')]
    public function render()
    {
        return view('livewire.cetak.cetak-stok-opname');
    }
}
