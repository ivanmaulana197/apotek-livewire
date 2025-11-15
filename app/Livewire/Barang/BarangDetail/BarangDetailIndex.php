<?php
namespace App\Livewire\Barang\BarangDetail;

use App\Models\Barang\Barang;
use Livewire\Attributes\On;
use Livewire\Component;

class BarangDetailIndex extends Component
{

    public $barang;

    public function mount($barang)
    {

        $this->barang = Barang::select('id', 'kode', 'nama', 'stok', 'stok_min', 'id_satuan_utama', 'harga_beli_default', 'harga_jual_utama')
            ->with(['kategories:id,nama', 'raks:id,nama', 'opsi_satuan:id,nama', 'satuan_utama:id,nama', 'opsi_harga.satuan:id,nama'])->findOrFail($barang);
        // dd($this->barang->toArray());
    }

    #[On('update-stok-sistem-barang')]
    public function getBarang($stok_sistem)
    {
        $this->barang->stok = $stok_sistem;
    }
    public function render()
    {
        return view('livewire.barang.barang-detail.barang-detail-index');
    }
}