<?php
namespace App\Livewire\Cetak\Dokter;

use App\Models\DataMaster\Dokter;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;

class CetakDokterDetail extends Component
{
    public $id_dokter;
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
        // return Transaksi::query()
        //     ->with(['pasien:id,nama_pasien', 'detail_transaksi.detail_barang.barang:id,nama', 'detail_transaksi.detail_barang.satuan:id,nama'])
        //     ->where('status', 'TERJUAL')
        //     ->where('id_dokter', $this->id_dokter)
        //     ->whereBetween('created_at', [
        //         $this->filter_date['start'] . ' 00:00:00',
        //         $this->filter_date['end'] . ' 23:59:59',
        //     ])
        //     ->orderBy('created_at', 'asc')
        //     ->get();

        return Dokter::query()
            ->with(['transaksi' => function ($query) {
                $query->with([
                    'pasien:id,nama_pasien',
                    'detail_transaksi:id,id_transaksi,id_history_barang,harga,total_harga',
                    'detail_transaksi.detail_barang:id,id_barang,jumlah,id_satuan',
                    'detail_transaksi.detail_barang.barang:id,nama',
                    'detail_transaksi.detail_barang.satuan:id,nama',
                ])
                    ->where('status', 'TERJUAL')
                    ->whereBetween('created_at', [
                        $this->filter_date['start'] . ' 00:00:00',
                        $this->filter_date['end'] . ' 23:59:59',
                    ])
                    ->orderBy('created_at', 'asc');
            }])
            ->withSum(['transaksi as total_penjualan' => function ($query) {
                $query->where('status', 'TERJUAL')
                    ->whereBetween('created_at', [
                        $this->filter_date['start'] . ' 00:00:00',
                        $this->filter_date['end'] . ' 23:59:59',
                    ]);
            }], 'total_bayar')
            ->findOrFail($this->id_dokter)
        ;
    }

    #[Layout('components.layouts.cetak-laporan')]
    public function render()
    {
        $data = $this->getData();
        // dd($data);

        // dd($data->toArray());
        return view('livewire.cetak.dokter.cetak-dokter-detail', compact('data'));
    }
}
