<?php
namespace App\Livewire\Pemasukan;

use App\Models\Barang\Barang;
use Livewire\Component;

class PemasukanBarangPesananForm extends Component
{
    public $pesanan_kasir, $persen_resep;
    public $id_barang, $jumlah, $id_satuan, $harga_jual, $harga_satuan_utama, $sub_total, $id_promo_selected, $id_list_promo;
    public $list_total_jumlah_dalam_satuan_utama, $jumlah_dalam_satuan_utama, $total_jumlah_dalam_satuan_utama_by_barang;
    public $data_barang;

    public function mount($pesanan_kasir)
    {
        $this->pesanan_kasir = $pesanan_kasir;
    }

    public function getDataBarang($idBarang)
    {
        $this->pesanan_kasir->loadMissing(['promo_pesanan_kasir.promo_barang']);
        $id_promo_barang = $this->pesanan_kasir->promo_pesanan_kasir
            ->filter(fn($promo) => $promo->promo_barang?->id_barang == $idBarang)
            ->first()?->id_promo_barang;
        $this->dispatch('selectedPromoBarang', idPromoBarang: $id_promo_barang);
        return Barang::query()
            ->with(['satuan_utama', 'opsi_satuan', 'opsi_harga.satuan'])
            ->where('id', $idBarang)
            ->first();
    }

    public function render()
    {
        return view('livewire.pemasukan.pemasukan-barang-pesanan-form');
    }
}
