<?php
namespace App\Livewire\Home;

use App\Helpers\FormatHelper;
use App\Models\Barang\Barang;
use App\Models\Barang\StokBatchBarang;
use App\Models\Transaksi\Transaksi;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class HomeIndex extends Component
{
    public $pendapatan, $persentase_pendapatan, $total_transaksi, $persentase_transaksi, $barang_habis, $barang_kadaluarsa, $obat_hampir_habis;
    public $tipe_chart = 'PENDAPATAN';
    public $chartDate, $selectPendapatan, $selectDateChart;
    public $chartPendapatan, $chartTransaksi;

    public function mount()
    {
        $this->chartDate = [
            'start' => Carbon::now()->startOfMonth()->toDateString(), // awal bulan
            'end'   => Carbon::today()->toDateString(),               // hari ini
        ];

        $this->chartPendapatan = $this->getDataChartPendapatan();
    }

    public function updatedTipeChart($value)
    {
        $this->reset('chartPendapatan');
        // Update data chart
        if ($this->tipe_chart == 'PENDAPATAN') {
            $data = $this->chartPendapatan = $this->getDataChartPendapatan();
        } else if ($this->tipe_chart == 'TRANSAKSI') {
            $data = $this->chartTransaksi = $this->getDataChartTransaksi();
        }
        // $this->chartPendapatan = $this->getDataChartPendapatan();

        // Dispatch event dengan parameter yang benar

        $this->dispatch('updateChart', data: $data, tipe: $value);
    }

    public function updatedChartDate($value)
    {
        // Pastikan chartDate tidak null
        if (! $this->chartDate || ! isset($this->chartDate['start']) || ! isset($this->chartDate['end'])) {
            return;
        }

        $this->reset('chartPendapatan');
        // Update data chart
        if ($this->tipe_chart == 'PENDAPATAN') {
            $data = $this->chartPendapatan = $this->getDataChartPendapatan();
        } else if ($this->tipe_chart == 'TRANSAKSI') {
            $data = $this->chartTransaksi = $this->getDataChartTransaksi();
        }

        // Dispatch event dengan parameter yang benar
        $this->dispatch('updateChart', data: $data, tipe: $this->tipe_chart);
    }

    public function getDataChartTransaksi()
    {
        if (! $this->chartDate || ! isset($this->chartDate['start']) || ! isset($this->chartDate['end'])) {
            return [];
        }

        $transaksi = DB::table('transaksi')
            ->selectRaw('DATE(created_at) as tanggal, COUNT(*) as total_transaksi')
            ->where('status', 'TERJUAL')
            ->whereBetween(DB::raw('DATE(created_at)'), [$this->chartDate['start'], $this->chartDate['end']])
            ->groupByRaw('DATE(created_at)')
            ->orderBy('tanggal')
            ->pluck('total_transaksi', 'tanggal');

        $periode = CarbonPeriod::create($this->chartDate['start'], $this->chartDate['end']);
        $data    = [];
        foreach ($periode as $tanggal) {
            $tgl    = FormatHelper::formatDate($tanggal->toDateString());
            $data[] = [
                'tanggal' => $tgl,
                'data'    => (int) ($transaksi[$tanggal->toDateString()] ?? 0),
            ];
        }

        return $data;
    }

    public function getDataChartPendapatan()
    {
        // Pastikan chartDate ada dan valid
        if (! $this->chartDate || ! isset($this->chartDate['start']) || ! isset($this->chartDate['end'])) {
            return [];
        }

        $pendapatan = DB::table('transaksi')
            ->selectRaw('DATE(created_at) as tanggal, SUM(total_bayar) as total_pendapatan')
            ->where('status', 'TERJUAL')
            ->whereBetween(DB::raw('DATE(created_at)'), [$this->chartDate['start'], $this->chartDate['end']])
            ->groupByRaw('DATE(created_at)')
            ->orderBy('tanggal')
            ->pluck('total_pendapatan', 'tanggal');

        $periode = CarbonPeriod::create($this->chartDate['start'], $this->chartDate['end']);
        $data    = [];

        foreach ($periode as $tanggal) {
            $tgl    = FormatHelper::formatDate($tanggal->toDateString());
            $data[] = [
                'tanggal' => $tgl,
                'data'    => (int) ($pendapatan[$tanggal->toDateString()] ?? 0),
            ];
        }

        return $data;
    }

    public function fetchData()
    {
        $this->reset('chartPendapatan');
        // Update data chart
        if ($this->tipe_chart == 'PENDAPATAN') {
            $data = $this->chartPendapatan = $this->getDataChartPendapatan();
        } else if ($this->tipe_chart == 'TRANSAKSI') {
            $data = $this->chartTransaksi = $this->getDataChartTransaksi();
        }

        $this->dispatch('updateChart', data: $data, tipe: $this->tipe_chart);
        $this->getData();

    }

    private function getPersentaseTransaksi($data_transaksi)
    {
        $total_bulan_ini = Transaksi::where('status', 'TERJUAL')
            ->whereYear('created_at', Carbon::now()->year)
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereDay('created_at', '<', Carbon::now()->day)
            ->selectRaw('SUM(total_bayar) as pendapatan, COUNT(*) as total_transaksi')->first();

        $rata_pendapatan_bulan_ini = $total_bulan_ini->pendapatan / (Carbon::now()->day - 1);
        $rata_transaksi_bulan_ini  = $total_bulan_ini->total_transaksi / (Carbon::now()->day - 1);
        if ($rata_pendapatan_bulan_ini > 0) {
            $selisih_pendapatan    = $data_transaksi->pendapatan - $rata_pendapatan_bulan_ini;
            $persentase_pendapatan = ($selisih_pendapatan / $rata_pendapatan_bulan_ini) * 100;
        } else {
            $selisih_pendapatan    = 0;
            $persentase_pendapatan = 0;
        }

        if ($rata_transaksi_bulan_ini > 0) {
            $selisih_transaksi    = $data_transaksi->total_transaksi - $rata_transaksi_bulan_ini;
            $persentase_transaksi = ($selisih_transaksi / $rata_transaksi_bulan_ini) * 100;
        } else {
            $selisih_transaksi    = 0;
            $persentase_transaksi = 0;
        }
        return [$persentase_pendapatan, $persentase_transaksi];
    }

    public function getData()
    {
        $data_transaksi                                             = Transaksi::where('status', 'TERJUAL')->whereDate('created_at', now())->selectRaw('SUM(total_bayar) as pendapatan, COUNT(*) as total_transaksi')->first();
        [$this->persentase_pendapatan, $this->persentase_transaksi] = $this->getPersentaseTransaksi($data_transaksi);
        $this->pendapatan                                           = $data_transaksi->pendapatan;
        $this->obat_hampir_habis                                    = Barang::whereColumn('stok', '<=', 'stok_min')->count();
        $this->total_transaksi                                      = $data_transaksi->total_transaksi;
        // $data_barang = Transaksi::where('status', 'TERJUAL')->whereDate('created_at', now())->selectRaw('COUNT(*) as barang_habis, COUNT(*) as barang_expired')->first();
        $this->barang_kadaluarsa = StokBatchBarang::whereDate('tgl_expired', '<=', Carbon::now()->addMonths(3))->count();
        // dd($data_transaksi);
    }

    public function render()
    {
        $this->getData();
        return view('livewire.home.home-index');
    }
}
