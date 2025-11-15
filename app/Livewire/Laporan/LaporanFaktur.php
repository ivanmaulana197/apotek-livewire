<?php
namespace App\Livewire\Laporan;

use App\Models\DataMaster\Supplier;
use Livewire\Attributes\Computed;
use Livewire\Component;

class LaporanFaktur extends Component
{
    public $created_at, $tgl_jatuh_tempo, $id_suppliers, $status;

    public function render()
    {
        return view('livewire.laporan.laporan-faktur');
    }

    #[Computed(persist: true, seconds: 3600, cache: true)]
    public function suppliers()
    {
        return Supplier::select('id', 'nama')->get();
    }
}
