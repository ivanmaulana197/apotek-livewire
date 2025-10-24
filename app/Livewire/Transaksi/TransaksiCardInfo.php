<?php
namespace App\Livewire\Transaksi;

use App\Helpers\FormatHelper;
use App\Models\Transaksi\Transaksi;
use Carbon\Carbon;
use Livewire\Attributes\On;
use Livewire\Component;

class TransaksiCardInfo extends Component
{

    // #[Url( as :'date')]
    public $filter_date;
    // #[Url( as :'shift')]
    public $shift_transaksi;
    public $status_tanggal;
    public $total_bayar, $total_bayar_tunai, $total_bayar_non_tunai;
    public $total_bayar_non_resep, $total_bayar_resep, $total_transaksi;

    #[On('update_value_shift_transaksi')]
    public function updateShiftTransaksi($value)
    {
        $this->shift_transaksi = $value;
    }

    #[On('update_value_filter_date')]
    public function updateFilterDate($value)
    {
        $this->filter_date = $value;
    }

    protected function updateStatusTanggal()
    {
        if (! empty($this->filter_date['start']) && ! empty($this->filter_date['end'])) {
            $start = Carbon::parse($this->filter_date['start']);
            $end   = Carbon::parse($this->filter_date['end']);

            if ($start->equalTo($end)) {
                $this->status_tanggal = $start->isToday()
                    ? 'hari ini'
                    : $start->diffForHumans(now()->startOfDay(), Carbon::DIFF_RELATIVE_TO_NOW, false, 1);
            } else {
                $this->status_tanggal =
                FormatHelper::formatDate($this->filter_date['start']) .
                ' - ' .
                FormatHelper::formatDate($this->filter_date['end']);
            }
        } else {
            $this->status_tanggal = 'SEMUA';
        }
    }

    public function getDataTransaksi()
    {
        $query = Transaksi::query()
            ->when($this->filter_date ?? null, function ($query, $item) {
                $query->whereBetween('created_at', ["{$item['start']} 00:00:00", "{$item['end']} 23:59:59"]);
            })
            ->when(in_array($this->shift_transaksi, ['MALAM', 'PAGI']), function ($query) {
                $query->where('shift_transaksi', $this->shift_transaksi);
            })
            ->where('status', 'TERJUAL');
        $rekap = $query
            ->selectRaw("
                SUM(total_bayar) AS total_bayar,
                SUM(CASE WHEN id_metode_pembayaran = 1 THEN total_bayar ELSE 0 END) AS total_bayar_tunai,
                SUM(CASE WHEN id_metode_pembayaran != 1 THEN total_bayar ELSE 0 END) AS total_bayar_non_tunai,
                SUM(CASE WHEN jenis_transaksi LIKE '%RESEP%' THEN total_bayar ELSE 0 END) AS total_bayar_resep,
                SUM(CASE WHEN jenis_transaksi NOT LIKE '%RESEP%' THEN total_bayar ELSE 0 END) AS total_bayar_non_resep,
                COUNT(*) AS total_transaksi")
            ->first();

        $this->total_bayar           = FormatHelper::formatNumberToString($rekap->total_bayar ?? 0);
        $this->total_bayar_tunai     = FormatHelper::formatNumberToString($rekap->total_bayar_tunai ?? 0);
        $this->total_bayar_non_tunai = FormatHelper::formatNumberToString($rekap->total_bayar_non_tunai ?? 0);
        $this->total_bayar_resep     = FormatHelper::formatNumberToString($rekap->total_bayar_resep ?? 0);
        $this->total_bayar_non_resep = FormatHelper::formatNumberToString($rekap->total_bayar_non_resep ?? 0);
        $this->total_transaksi       = FormatHelper::formatNumberToString($rekap->total_transaksi ?? 0);

    }

    #[On('update_transaksi')]
    public function render()
    {
        $this->updateStatusTanggal();
        $this->getDataTransaksi();
        return view('livewire.transaksi.transaksi-card-info');
    }
}
