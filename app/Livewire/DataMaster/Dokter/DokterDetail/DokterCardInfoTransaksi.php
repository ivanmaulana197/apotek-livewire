<?php
namespace App\Livewire\DataMaster\Dokter\DokterDetail;

use App\Models\Transaksi\Transaksi;
use Livewire\Attributes\On;
use Livewire\Component;

class DokterCardInfoTransaksi extends Component
{
    public $id_dokter;
    public $filter_date;

    #[On('update_value_filter_date')]
    public function filterDate($value)
    {
        $this->filter_date = $value;
    }

    public function getData()
    {
        $data = Transaksi::query()
            ->where('status', 'TERJUAL')
            ->whereBetween('created_at', [
                "{$this->filter_date['start']} 00:00:00",
                "{$this->filter_date['end']} 23:59:59",
            ])
            ->whereRelation('dokter', 'id', $this->id_dokter)
            ->selectRaw('COUNT(*) as total_transaksi, SUM(total_bayar) as total_bayar')
            ->first();

        return $data;
    }

    public function render()
    {

        // $this->data = $this->getData();
        $data = $this->getData();
        return view('livewire.data-master.dokter.dokter-detail.dokter-card-info-transaksi', compact('data'));
    }
}