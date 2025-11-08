<?php
namespace App\Livewire\Transaksi;

use App\Helpers\FormatHelper;
use App\Models\Transaksi\Transaksi;
use Livewire\Component;

class TransaksiDetail extends Component
{
    public $transaksi;

    public function mount($id_transaksi)
    {
        $this->transaksi = Transaksi::with([
            'detail_transaksi.detail_barang' => function ($q) {
                $q->with(['barang:id,nama,id_satuan_utama', 'barang.satuan_utama:id,nama', 'satuan:id,nama'])->orderBy('id_barang');
            },
            'transaksi_list_promo.barang:id,nama',
            'member:id,nama',
            'pasien',
            'dokter:id,nama',
            'metode_pembayaran:id,nama',
        ])
            ->findOrFail($id_transaksi);

        $diskonPromos = $this->transaksi->transaksi_list_promo
            ->where('tipe_promo', '!=', 'TRANSAKSI')
            ->keyBy('id_barang');

        $this->transaksi->detail_transaksi->map(function ($item, $key) use ($diskonPromos) {
            $item->harga                 = FormatHelper::formatNumberToString($item->harga);
            $item->harga_satuan_utama    = FormatHelper::formatNumberToString($item->harga_satuan_utama);
            $item->total_harga           = FormatHelper::formatNumberToString($item->total_harga);
            $item->detail_barang->jumlah = FormatHelper::formatNumberToString($item->detail_barang->jumlah);
            $next                        = $this->transaksi->detail_transaksi->get($key + 1)?->detail_barang;
            $item->promo_barang          = (! $next || $item->id_barang != $next->id_barang)
                ? ($diskonPromos[$item->id_barang] ?? null)
                : null;
            return $item;
        });
        $this->setupFormatValue();
    }

    public function setupFormatValue()
    {
        $this->transaksi->total_bayar     = FormatHelper::formatNumberToString($this->transaksi->total_bayar);
        $this->transaksi->sub_total       = FormatHelper::formatNumberToString($this->transaksi->sub_total);
        $this->transaksi->bayar           = FormatHelper::formatNumberToString($this->transaksi->bayar);
        $this->transaksi->kembalian       = FormatHelper::formatNumberToString($this->transaksi->kembalian);
        $this->transaksi->potongan_diskon = FormatHelper::formatNumberToString($this->transaksi->potongan_diskon);
        $this->transaksi->pembulatan      = FormatHelper::formatNumberToString($this->transaksi->pembulatan);
    }

    public function render()
    {
        return view('livewire.transaksi.transaksi-detail');
    }
}
