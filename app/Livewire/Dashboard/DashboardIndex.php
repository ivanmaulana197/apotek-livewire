<?php
namespace App\Livewire\Dashboard;

use App\Helpers\FormatHelper;
use App\Helpers\FormatInputHelper;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class DashboardIndex extends Component
{
    public $pendapatan, $persentase_pendapatan, $total_transaksi, $persentase_transaksi, $barang_habis, $barang_expired;

    public $tipe_chart = 'PENDAPATAN', $tgl_chart;
    public $chart;

    public function mount(){
        $this->tgl_chart = [
             // 'start' => Carbon::today()->subMonth()->toDateString(), // 1 bulan yang lalu
            'start' => Carbon::now()->startOfMonth()->toDateString(), // 6 hari yang lalu
            'end'   => Carbon::today()->toDateString(),               // hari ini
        ];
        $this->chart = $this->getDataChart($this->tipe_chart);
    }

    public function updatedTipeChart(){ $this->fetchData(); }
    public function updatedChartDate(){ $this->fetchData(); }

    public function fetchData()
    {
        $this->chart = $this->getDataChart($this->tipe_chart);
        $this->dispatch('updateChart', data: $this->chart, tipe: $this->tipe_chart);
    }


    private function getDataChart($tipe)
    {
        if (! isset($this->tgl_chart['start']) || ! isset($this->tgl_chart['end']) || ! $this->tgl_chart ) return [];

        $query = DB::table('transaksi')
            ->where('status', 'TERJUAL')
            ->whereBetween(DB::raw('DATE(created_at)'), [$this->tgl_chart['start'], $this->tgl_chart['end']]);

        $result = $tipe == 'PENDAPATAN'
            ? $query->selectRaw('DATE(created_at) tgl, SUM(total_bayar) val')->groupBy('tgl')->pluck('val','tgl')
            : $query->selectRaw('DATE(created_at) tgl, COUNT(*) val')->groupBy('tgl')->pluck('val','tgl');

        $data = [];
        foreach (CarbonPeriod::create($this->chartDate['start'], $this->chartDate['end']) as $tgl) {
            $data[] = [
                'tanggal' => FormatHelper::formatDate($tgl->toDateString()),
                'data'    => (int)($result[$tgl->toDateString()] ?? 0),
            ];
        }

        return $data;
    }




    public function render()
    {
        return view('livewire.dashboard.dashboard-index');
    }
}
