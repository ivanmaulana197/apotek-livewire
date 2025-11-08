<?php
namespace App\Livewire\Cetak;

use App\Models\Setting;
use App\Models\Transaksi\Transaksi;
use Livewire\Attributes\Layout;
use Livewire\Component;

class StrukPembayaran extends Component
{
    public $transaksi, $setting;

    public function mount($id_transaksi)
    {
        $this->transaksi = Transaksi::with([
            'detail_transaksi.detail_barang' => function ($q) {
                $q->with(['barang:id,nama', 'satuan:id,nama'])->orderBy('id_barang');
            },
            'transaksi_list_promo.barang:id,nama',
        ])
            ->findOrFail($id_transaksi);

        $diskonPromos = $this->transaksi->transaksi_list_promo
            ->where('tipe_promo', 'BARANG DISKON')
            ->keyBy('id_barang');

        $this->transaksi->detail_transaksi->map(function ($item, $key) use ($diskonPromos) {
            $next               = $this->transaksi->detail_transaksi->get($key + 1)?->detail_barang;
            $item->promo_barang = (! $next || $item->detail_barang->id_barang != $next->id_barang)
                ? ($diskonPromos[$item->detail_barang->id_barang] ?? null)
                : null;
            return $item;
        });
        $this->setting = Setting::first();
    }

    #[Layout('components.layouts.cetak')]
    public function render()
    {
        return view('livewire.cetak.struk-pembayaran');
    }
}
