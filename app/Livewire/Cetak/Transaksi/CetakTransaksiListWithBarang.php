<?php
namespace App\Livewire\Cetak\Transaksi;

use App\Helpers\GeneralHelper;
use App\Models\Transaksi\Transaksi;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;

class CetakTransaksiListWithBarang extends Component
{
    public $transaksi;
    #[Url( as : 'date')]
    public $filter_date;
    #[Url( as : 'shift')]
    public $shift_transaksi;
    public $sum_total_bayar, $sum_sub_total, $sum_potongan_promo, $sum_pembulatan, $sum_total_komisi;
    public $id_barang;

    public function mount()
    {
        $this->filter_date = [
            'start' => request('date.start', now()->format('Y-m-d')),
            'end'   => request('date.end', now()->format('Y-m-d')),
        ];
        if (request()->has('id_barang')) {
            $this->id_barang = request('id_barang');
        }

        $this->shift_transaksi = request('shift', GeneralHelper::checkModeShift());
    }

    public function getTransaksiProperty()
    {
        $this->transaksi = Transaksi::query()
            ->select([
                'id', 'no_transaksi', 'created_at', 'id_user', 'id_metode_pembayaran',
                'sub_total', 'potongan_diskon', 'pembulatan', 'total_bayar',
                'shift_transaksi', 'status',
            ])
            ->where('status', 'TERJUAL')
            ->when($this->id_barang, fn($q) =>
                $q->whereHas('detail_transaksi_barang', function ($q) {
                    $q->where('id_barang', $this->id_barang);
                })
            )
            ->whereBetween('created_at', [
                $this->filter_date['start'] . ' 00:00:00',
                $this->filter_date['end'] . ' 23:59:59',
            ])
            ->when(
                in_array($this->shift_transaksi, ['MALAM', 'PAGI']),
                fn($q) => $q->where('shift_transaksi', $this->shift_transaksi)
            )
            ->with([
                'user:id,name',
                'metode_pembayaran:id,nama',
                'transaksi_list_promo:id,id_transaksi,tipe_promo,id_barang,deskripsi,nominal_promo',
                'detail_transaksi.detail_barang' => function ($q) {
                    $q->select(['id', 'id_barang', 'jumlah', 'id_satuan'])
                        ->with([
                            'satuan:id,nama',
                            'barang:id,stok,id_satuan_utama,nama',
                        ]);
                },
            ])
            ->withSum([
                'transaksi_list_promo as total_promo_barang_bonus' => function ($query) {
                    $query->where('tipe_promo', 'BARANG BONUS');
                },
            ], 'nominal_promo')
            ->withSum('detail_transaksi as total_komisi', 'komisi')
        // ->get()
            ->cursor()
            ->map(function ($trx) {
                // Siapkan data promo agar Blade gak hitung lagi
                $trx->promoBarang = $trx->transaksi_list_promo
                    ->where('tipe_promo', '!=', 'TRANSAKSI')
                    ->keyBy('id_barang');

                // Hitung komisi bersih sekali saja
                $trx->komisi_bersih = ($trx->total_komisi ?? 0)
                     - ($trx->total_promo_barang_bonus ?? 0)
                     - ($trx->potongan_diskon ?? 0);

                return $trx;
            })->collect();
        // dd($this->transaksi->toArray());

        $this->sum_total_bayar    = $this->transaksi->sum('total_bayar');
        $this->sum_sub_total      = $this->transaksi->sum('sub_total');
        $this->sum_potongan_promo = $this->transaksi->sum('potongan_diskon');
        $this->sum_total_komisi   = $this->transaksi->sum('komisi_bersih');
        $this->sum_pembulatan     = $this->transaksi->sum('pembulatan');
    }

    #[Layout('components.layouts.cetak-laporan')]
    public function render()
    {
        $this->getTransaksiProperty();
        // dd($this->transaksi->toArray());
        return view('livewire.cetak.transaksi.cetak-transaksi-list-with-barang');
    }
}
