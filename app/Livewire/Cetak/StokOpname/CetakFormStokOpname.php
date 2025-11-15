<?php
namespace App\Livewire\Cetak\StokOpname;

use App\Models\Barang\Barang;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

class CetakFormStokOpname extends Component
{
    // public $stokOpnames;

    public function mount()
    {
        // $this->getData();
    }

    public function getData()
    {
        // $this->stokOpnames = Barang::query()
        //     ->with('raks:id,nama')
        //     ->join('satuan as satuan_utama', 'barang.id_satuan_utama', '=', 'satuan_utama.id')
        //     ->select('barang.id', 'barang.nama', 'barang.stok', 'barang.stok_real', 'satuan_utama.nama as nama_satuan_utama')
        //     ->get();
    }

    #[Layout('components.layouts.cetak-laporan')]
    public function render()
    {
        return view('livewire.cetak.stok-opname.cetak-form-stok-opname');
    }

    #[Computed(persist: true, seconds: 3600, cache: true)]
    public function stokOpnames()
    {
        return Barang::query()
        // ->with('raks:id,nama')
            ->join('satuan as satuan_utama', 'barang.id_satuan_utama', '=', 'satuan_utama.id')
            ->select('barang.id', 'barang.nama', 'barang.stok', 'barang.stok_real', 'satuan_utama.nama as nama_satuan_utama')
            ->get();
    }
}
