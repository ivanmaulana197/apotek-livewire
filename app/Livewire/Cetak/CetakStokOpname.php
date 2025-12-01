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
            ->leftJoin('barang', 'stok_opname_histori.id_barang', '=', 'barang.id')
            ->leftJoin('satuan as satuan_utama', 'satuan_utama.id', '=', 'barang.id_satuan_utama')
            ->select('stok_opname_histori.*', 'barang.nama', 'satuan_utama.nama as satuan_utama_nama')
            ->where('stok_opname_histori.created_at', $created_at)->get();
    }

    #[Layout('components.layouts.cetak-laporan')]
    public function render()
    {
        return view('livewire.cetak.cetak-stok-opname');
    }
}
