<?php
namespace App\Livewire\Cetak\Dokter;

use App\Models\DataMaster\Dokter;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;

class CetakLaporanDokter extends Component
{
    #[Url( as : 'date')]
    public $filter_date;

    public function mount()
    {
        $this->filter_date = [
            'start' => request('date.start', now()->startOfMonth()->format('Y-m-d')),
            'end'   => request('date.end', now()->endOfMonth()->format('Y-m-d')),
        ];
    }

    public function getData()
    {

        return Dokter::query()
            ->whereHas('transaksi', function ($query) {
                $query->where('status', 'TERJUAL')
                    ->whereBetween('created_at', [
                        $this->filter_date['start'] . ' 00:00:00',
                        $this->filter_date['end'] . ' 23:59:59',
                    ]);
            })
            ->withSum(['transaksi as total_transaksi' => function ($query) {
                $query->where('status', 'TERJUAL')
                    ->whereBetween('created_at', [
                        $this->filter_date['start'] . ' 00:00:00',
                        $this->filter_date['end'] . ' 23:59:59',
                    ]);
            }], 'total_bayar')
            ->withCount(['transaksi as jumlah_transaksi' => function ($query) {
                $query->where('status', 'TERJUAL')
                    ->whereBetween('created_at', [
                        $this->filter_date['start'] . ' 00:00:00',
                        $this->filter_date['end'] . ' 23:59:59',
                    ]);
            }])
            ->orderBy('total_transaksi', 'desc')
            ->get()
        ;
    }

    #[Layout('components.layouts.cetak-laporan')]
    public function render()
    {
        $data = $this->getData();
        // dd($this->getData()->toArray());
        return view('livewire.cetak.dokter.cetak-laporan-dokter', compact('data'));
    }
}
