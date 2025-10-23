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
            'transaksi_barang' => function ($q) {
                $q->with(['detail_transaksi', 'barang', 'satuan'])->orderBy('id_barang');
            },
            'transaksi_list_promo.barang:id,nama',
        ])
            ->findOrFail($id_transaksi);

        $diskonPromos = $this->transaksi->transaksi_list_promo
            ->where('tipe_promo', 'BARANG DISKON')
            ->keyBy('id_barang');

        $this->transaksi->transaksi_barang->map(function ($item, $key) use ($diskonPromos) {
            $next               = $this->transaksi->transaksi_barang->get($key + 1);
            $item->promo_barang = (! $next || $item->id_barang != $next->id_barang)
                ? ($diskonPromos[$item->id_barang] ?? null)
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
