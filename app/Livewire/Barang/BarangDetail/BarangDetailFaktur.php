<?php
namespace App\Livewire\Barang\BarangDetail;

use App\Models\Barang\Barang;
use Livewire\Component;

class BarangDetailFaktur extends Component
{
    public $barang;

    public function mount($id)
    {
        $this->barang = Barang::select('id', 'nama')->find($id)->toArray();
    }

    public function render()
    {
        return view('livewire.barang.barang-detail.barang-detail-faktur');
    }
}
