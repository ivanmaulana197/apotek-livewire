<?php
namespace App\Livewire\Cetak\Transaksi;

use App\Helpers\GeneralHelper;
use App\Models\Transaksi\Transaksi;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;

class CetakLaporanTransaksi extends Component
{
    public $transaksi;
    #[Url( as : 'date')]
    public $filter_date;
    #[Url( as : 'shift')]
    public $shift_transaksi;
    #[Url( as : 'by_user')]
    public $by_user;
    public $sum_total_bayar, $sum_sub_total, $sum_potongan_promo, $sum_pembulatan, $sum_total_komisi;
    public $id_barang;

    public function mount()
    {
        if (Auth::user()->can('transaksi.filter-transaksi')) {
            $this->filter_date = [
                'start' => request('date.start', now()->format('Y-m-d')),
                'end'   => request('date.end', now()->format('Y-m-d')),
            ];
        } else {
            $this->filter_date = [
                'start' => now()->format('Y-m-d'),
                'end'   => now()->format('Y-m-d'),
            ];
        }

        $this->shift_transaksi = request('shift', GeneralHelper::checkModeShift());
        if (Auth::user()->can('transaksi.cetak-laporan-all')) {
            $this->by_user = request('by_user', 'SEMUA');
        } elseif (Auth::user()->can('transaksi.cetak-laporan-own')) {
            $this->by_user = Auth::user()->id;
        } else {
            return redirect()->back()->with([
                'alert' => [
                    'title'   => 'Tidak Diizinkan',
                    'message' => 'Anda tidak memiliki akses yang diperlukan.',
                    'icon'    => 'error', // Ikon SweetAlert ('success', 'error', 'warning', 'info')
                ],
            ]);
        }

    }

    public function getTransaksi()
    {
        $this->transaksi = Transaksi::query()
            ->with(['detail_transaksi' => function ($q) {
                $q->select(
                    'detail_transaksi_barang.*',
                    'history_barang.id_barang',
                    'history_barang.jumlah',
                    'barang.nama as nama_barang',
                    'satuan.nama as nama_satuan',
                )
                    ->join('history_barang', 'history_barang.id', '=', 'detail_transaksi_barang.id_history_barang')
                    ->join('barang', 'barang.id', '=', 'history_barang.id_barang')
                    ->join('satuan', 'satuan.id', '=', 'history_barang.id_satuan');

            }, 'transaksi_list_promo' => function ($q) {
                $q->where('tipe_promo', '!=', 'TRANSAKSI')
                    ->select('id_transaksi', 'id_barang', 'tipe_promo', 'nominal_promo', 'deskripsi');
            }, 'dokter', 'pasien', 'member.profesi'])
            ->join('users', 'users.id', '=', 'transaksi.id_user')
        // ->leftJoin('dokter', 'dokter.id', '=', 'transaksi.id_dokter')
        // ->leftJoin('pasien', 'pasien.id', '=', 'transaksi.id_pasien')
        // ->leftJoin('pasien', 'pasien.id', '=', 'transaksi.id_pasien')
            ->join('metode_pembayaran', 'metode_pembayaran.id', '=', 'transaksi.id_metode_pembayaran')
            ->select('transaksi.*', 'users.name as nama_user', 'metode_pembayaran.nama as nama_metode_pembayaran')
            ->where('status', 'TERJUAL')
            ->whereBetween('transaksi.created_at', [
                "{$this->filter_date['start']} 00:00:00",
                "{$this->filter_date['end']} 23:59:59",
            ])
            ->when(in_array($this->shift_transaksi, ['MALAM', 'PAGI']), function ($q) {
                $q->where('transaksi.shift_transaksi', $this->shift_transaksi);
            })
            ->when($this->id_barang, function ($q) {
                $q->whereHas('detail_transaksi_barang', function ($q) {
                    $q->where('id_barang', $this->id_barang);
                });
            })
            ->when($this->by_user !== 'SEMUA', function ($q) {
                $q->where('transaksi.id_user', $this->by_user);
            })
            ->withSum([
                'transaksi_list_promo as total_promo_barang_bonus' => function ($query) {
                    $query->where('tipe_promo', 'BARANG BONUS');
                },
            ], 'nominal_promo')
            ->withSum('detail_transaksi as total_komisi', 'komisi')

            ->get()->map(function ($trx) {
            // Siapkan data promo agar Blade gak hitung lagi
            $trx->promo_barang = $trx->transaksi_list_promo
                ->where('tipe_promo', '!=', 'TRANSAKSI')
                ->keyBy('id_barang');

            // Hitung komisi bersih sekali saja
            $trx->komisi_bersih = ($trx->total_komisi ?? 0)
                 - ($trx->total_promo_barang_bonus ?? 0)
                 - ($trx->potongan_diskon ?? 0) + ($trx->pembulatan ?? 0);

            return $trx;
        });
        // dd($this->transaksi->toArray());
        $this->sum_total_bayar    = $this->transaksi->sum('total_bayar');
        $this->sum_sub_total      = $this->transaksi->sum('sub_total');
        $this->sum_potongan_promo = $this->transaksi->sum('potongan_diskon');
        $this->sum_pembulatan     = $this->transaksi->sum('pembulatan');
        $this->sum_total_komisi   = $this->transaksi->sum('komisi_bersih');
        // return $data;
    }

    #[Layout('components.layouts.cetak-laporan')]
    public function render()
    {
        $this->getTransaksi();
        return view('livewire.cetak.transaksi.cetak-laporan-transaksi');
    }

    // public function getTransaksiProperty()
    // {
    //     $transaksi = Transaksi::query()
    //         ->join('users', 'users.id', '=', 'transaksi.id_user')
    //         ->join('metode_pembayaran', 'metode_pembayaran.id', '=', 'transaksi.id_metode_pembayaran')
    //         ->leftJoin('detail_transaksi_barang', 'detail_transaksi_barang.id_transaksi', '=', 'transaksi.id')
    //         ->join('history_barang', 'history_barang.id', '=', 'detail_transaksi_barang.id_history_barang')
    //         ->leftJoin('transaksi_list_promo', 'transaksi_list_promo.id_transaksi', '=', 'transaksi.id')
    //         ->where('transaksi.status', 'TERJUAL')
    //         ->when($this->id_barang, function ($q) {
    //             $q->where('history_barang.id_barang', $this->id_barang);
    //         })
    //         ->whereBetween('transaksi.created_at', [
    //             "{$this->filter_date['start']} 00:00:00",
    //             "{$this->filter_date['end']} 23:59:59",
    //         ])
    //         ->when(in_array($this->shift_transaksi, ['MALAM', 'PAGI']), function ($q) {
    //             $q->where('transaksi.shift_transaksi', $this->shift_transaksi);
    //         })
    //         ->when($this->by_user !== 'SEMUA', function ($q) {
    //             $q->where('transaksi.id_user', $this->by_user);
    //         })
    //         ->selectRaw("
    //             transaksi.*,
    //             users.name as nama_user,
    //             metode_pembayaran.nama as nama_metode_pembayaran,
    //             COALESCE(SUM(detail_transaksi_barang.komisi), 0) as total_komisi,
    //             COALESCE(SUM(CASE WHEN transaksi_list_promo.tipe_promo = 'BARANG BONUS' THEN transaksi_list_promo.nominal_promo ELSE 0 END), 0) as total_promo_barang_bonus
    //         ")
    //         ->groupBy(
    //             'transaksi.id',
    //         )
    //         ->orderBy('transaksi.created_at', 'desc')
    //         ->get();

    //     $detailTransaksi = DB::table('detail_transaksi_barang')
    //         ->join('history_barang', 'history_barang.id', '=', 'detail_transaksi_barang.id_history_barang')
    //         ->join('barang', 'barang.id', '=', 'history_barang.id_barang')
    //         ->join('satuan', 'satuan.id', '=', 'history_barang.id_satuan')
    //         ->whereIn('detail_transaksi_barang.id_transaksi', $transaksi->pluck('id'))
    //         ->select(
    //             'detail_transaksi_barang.*',
    //             'history_barang.id_barang',
    //             'history_barang.jumlah',
    //             'barang.nama as nama_barang',
    //             'satuan.nama as nama_satuan',
    //         )
    //         ->get();

    //     $promoBarang = DB::table('transaksi_list_promo')
    //         ->whereIn('id_transaksi', $transaksi->pluck('id'))
    //         ->where('tipe_promo', '!=', 'TRANSAKSI')
    //         ->select('id_transaksi', 'id_barang', 'tipe_promo', 'nominal_promo', 'deskripsi')
    //         ->get()
    //         ->groupBy('id_transaksi');

    //     $groupedDetail = $detailTransaksi->groupBy('id_transaksi');

    //     $this->transaksi = $transaksi->map(function ($t) use ($groupedDetail, $promoBarang) {
    //         $t->komisi_bersih = ($t->total_komisi ?? 0)
    //              - ($t->total_promo_barang_bonus ?? 0)
    //              - ($t->potongan_diskon ?? 0)
    //              + ($t->pembulatan ?? 0);
    //         $t->detail_transaksi = $groupedDetail->get($t->id) ?? collect();
    //         $t->promo_barang     = collect($promoBarang->get($t->id) ?? [])->keyBy('id_barang');
    //         return $t;
    //     });
    //     // dd($this->transaksi);

    //     $this->sum_total_bayar    = $this->transaksi->sum('total_bayar');
    //     $this->sum_sub_total      = $this->transaksi->sum('sub_total');
    //     $this->sum_potongan_promo = $this->transaksi->sum('potongan_diskon');
    //     $this->sum_pembulatan     = $this->transaksi->sum('pembulatan');
    //     $this->sum_total_komisi   = $this->transaksi->sum('komisi_bersih');

    // }
}