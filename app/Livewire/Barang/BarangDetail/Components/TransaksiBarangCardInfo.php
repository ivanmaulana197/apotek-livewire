<?php
namespace App\Livewire\Barang\BarangDetail\Components;

use App\Helpers\FormatHelper;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;
use Livewire\Component;

class TransaksiBarangCardInfo extends Component
{
    public $id_barang;
    public $shift_transaksi, $filter_date, $all_satuan, $nama_barang;

    public function mount()
    {
        $this->shift_transaksi = 'SEMUA';
        $this->filter_date     = [
            'start' => now()->startOfMonth()->format('Y-m-d'),
            'end'   => now()->endOfMonth()->format('Y-m-d'),
        ];
    }

    #[On('update_value_filter_date')]
    public function updateFilterDate($value)
    {
        $this->filter_date = $value;
    }

    #[On('update_value_shift_transaksi')]
    public function updateShiftTransaksi($value)
    {
        $this->shift_transaksi = $value;
    }

    public function formattedStok($stok)
    {
        $hasil = [];
        foreach ($this->all_satuan as $satuan) {
            $rasio = $satuan['rasio'];
            if ($rasio <= 0) {
                continue; // skip kalau rasio tidak valid
            }
            $jumlah = floor($stok / $rasio);
            if ($jumlah > 0) {
                $hasil[] = FormatHelper::formatNumberToString($jumlah) . " {$satuan['satuan']}";
            }

            $stok = fmod($stok, $rasio);

        }
        return implode(' ', $hasil);
    }

    public function getData()
    {

        $data = DB::table('history_barang as hb')
            ->join('barang as b', 'hb.id_barang', '=', 'b.id')
            ->leftJoin('detail_transaksi_barang as dtb', 'dtb.id_history_barang', '=', 'hb.id')
            ->leftJoin('transaksi as t', 't.id', '=', 'dtb.id_transaksi')
            ->leftJoin('satuan_barang as sb', function ($join) {
                $join->on('sb.id_barang', '=', 'b.id')
                    ->on('sb.id_satuan', '=', 'hb.id_satuan');
            })
            ->leftJoin('satuan as s_utama', 's_utama.id', '=', 'b.id_satuan_utama')
            ->select(
                'hb.id_barang',
                'b.nama',
                's_utama.nama as nama_satuan_utama',
                DB::raw('
                    SUM(hb.jumlah * COALESCE(sb.rasio_satuan_opsi / sb.rasio_satuan_utama, 1)) AS total_jumlah_dalam_satuan_utama
                '),
                DB::raw('
                    SUM(dtb.total_harga) AS total_harga
                ')
            )
            ->where('hb.status', 'TERJUAL')
            ->where('hb.id_barang', $this->id_barang)
            ->whereBetween('hb.created_at', [
                "{$this->filter_date['start']} 00:00:00",
                "{$this->filter_date['end']} 23:59:59",
            ])
            ->when(in_array($this->shift_transaksi, ['PAGI', 'MALAM']), function ($query) {
                $query->where('t.shift_transaksi', $this->shift_transaksi);
            })
            ->groupBy('hb.id_barang', 'b.nama')
            ->first();

        return $data;
    }

    public function render()
    {
        $data                  = $this->getData();
        $format_jumlah_terjual = $this->formattedStok($data->total_jumlah_dalam_satuan_utama ?? 0);
        return view('livewire.barang.barang-detail.components.transaksi-barang-card-info', compact('data', 'format_jumlah_terjual'));
    }
}
