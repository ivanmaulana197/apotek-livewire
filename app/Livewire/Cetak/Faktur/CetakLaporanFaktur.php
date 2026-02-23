<?php
namespace App\Livewire\Cetak\Faktur;

use App\Models\DataMaster\Supplier;
use App\Models\Faktur\Faktur;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;

class CetakLaporanFaktur extends Component
{

    #[Url( as : 'date')]
    public $created_at;
    #[Url( as : 'status')]
    public $status;
    #[Url( as : 'tgl_jatuh_tempo')]
    public $tgl_jatuh_tempo;
    #[Url( as : 'id_suppliers')]
    public $id_suppliers;
    public $suppliers;

    public function mount()
    {

        $this->created_at = [
            'start' => request('date.start', now()->startOfMonth()->format('Y-m-d')),
            'end'   => request('date.end', now()->endOfMonth()->format('Y-m-d')),
        ];
        $this->status = request('status', 'SEMUA');
        if (request('tgl_jatuh_tempo.start') || request('tgl_jatuh_tempo.end')) {
            $this->tgl_jatuh_tempo = [
                'start' => request('tgl_jatuh_tempo.start', now()->startOfMonth()->format('Y-m-d')),
                'end'   => request('tgl_jatuh_tempo.end', now()->endOfMonth()->format('Y-m-d')),
            ];
        } else {
            $this->tgl_jatuh_tempo = 'SEMUA';
        }
        $this->id_suppliers = request('id_suppliers', 'SEMUA');

        if ($this->id_suppliers != 'SEMUA') {
            $this->suppliers = Supplier::select('id', 'nama')->whereIn('id', $this->id_suppliers)->get();
        }

        // dd($this->id_suppliers);
    }

    public function getDataInfo()
    {
        $data = Faktur::query()
            ->whereBetween('created_at', [
                "{$this->created_at['start']} 00:00:00",
                "{$this->created_at['end']} 23:59:59",
            ])
            ->when($this->status != 'SEMUA', function ($query) {
                $query->where('status', $this->status);
            })
            ->when($this->tgl_jatuh_tempo != 'SEMUA', function ($query) {
                $query->whereBetween('tgl_jatuh_tempo', [
                    "{$this->tgl_jatuh_tempo['start']} 00:00:00",
                    "{$this->tgl_jatuh_tempo['end']} 23:59:59",
                ]);
            })
            ->when($this->id_suppliers != 'SEMUA', function ($query) {
                $query->whereIn('id_supplier', $this->id_suppliers);
            })
            ->selectRaw('
                COUNT(*) as jumlah_faktur,
                SUM(total_biaya) as total_belanja,
                SUM(CASE WHEN status = "LUNAS" THEN total_biaya ELSE 0 END) as total_pengeluaran,
                SUM(CASE WHEN status = "BELUM LUNAS" THEN total_biaya ELSE 0 END) as total_hutang
            ')
            ->first();
        return $data;
    }

    public function getData()
    {
        $data_transaksi = Faktur::query()
            ->with([
                'detail_faktur.detail_barang' => function ($q) {
                    $q->select('history_barang.id', 'history_barang.jumlah', 'history_barang.id_satuan', 'history_barang.tgl_expired', 'history_barang.id_barang', 'barang.nama as nama_barang', 'satuan.nama as nama_satuan', 'barang.harga_beli_default')
                    // ->with(['barang.opsi_satuan'])
                        ->join('barang', 'barang.id', '=', 'history_barang.id_barang')
                        ->join('satuan', 'satuan.id', '=', 'history_barang.id_satuan');
                },
            ])
            ->join('supplier', 'faktur.id_supplier', '=', 'supplier.id')
            ->whereBetween('faktur.created_at', [
                "{$this->created_at['start']} 00:00:00",
                "{$this->created_at['end']} 23:59:59",
            ])
            ->when($this->status != 'SEMUA', function ($query) {
                $query->where('status', $this->status);
            })
            ->when($this->tgl_jatuh_tempo != 'SEMUA', function ($query) {
                $query->whereBetween('tgl_jatuh_tempo', [
                    "{$this->tgl_jatuh_tempo['start']} 00:00:00",
                    "{$this->tgl_jatuh_tempo['end']} 23:59:59",
                ]);
            })
            ->when($this->id_suppliers != 'SEMUA', function ($query) {
                $query->whereIn('id_supplier', $this->id_suppliers);
            })
            ->selectRaw('
                faktur.*,
                supplier.nama as nama_supplier
            ')
            ->get();

        return $data_transaksi;
    }

    #[Layout('components.layouts.cetak-laporan')]
    public function render()
    {
        if ($this->id_suppliers != 'SEMUA') {
            $ids             = is_array($this->id_suppliers) ? $this->id_suppliers : [$this->id_suppliers];
            $this->suppliers = Supplier::select('id', 'nama')->whereIn('id', $ids)->get();
        }
        $data = $this->getData();
        // dd($data->toArray());
        $dataInfo = $this->getDataInfo();
        return view('livewire.cetak.faktur.cetak-laporan-faktur', compact('data', 'dataInfo'));
    }
}