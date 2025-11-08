<?php
namespace App\Livewire\DataMaster\Dokter\DokterDetail;

use App\Models\Transaksi\Transaksi;
use Livewire\Component;

class DokterTransaksiList extends Component
{
    public $id_dokter;

    public function getData()
    {
        $query = Transaksi::select('id', 'no_transaksi', )
            ->where('status', 'TERJUAL')->whereHas('dokter', function ($query) {
            $query->where('id', $this->id_dokter);
        })->get();
        return $query;
    }

    public function render()
    {
        return view('livewire.data-master.dokter.dokter-detail.dokter-transaksi-list');
    }
}
