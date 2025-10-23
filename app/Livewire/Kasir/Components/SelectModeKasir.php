<?php
namespace App\Livewire\Kasir\Components;

use App\Livewire\Kasir\Helpers\KasirHelper;
use App\Models\Kasir\PesananKasir;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class SelectModeKasir extends Component
{
    use KasirHelper;
    public $id_pesanan_kasir, $mode_kasir, $setting;

    public function updatedModeKasir($value)
    {
        try {
            $this->validate(['mode_kasir' => 'required|in:UMUM,RESEP']);
            DB::beginTransaction();
            $pesanan_kasir             = PesananKasir::with(['barang_pesanan_kasir.barang'])->findOrFail($this->id_pesanan_kasir);
            $pesanan_kasir->mode_kasir = $value;
            $pesanan_kasir->save();
            foreach ($pesanan_kasir->barang_pesanan_kasir as $barang_pesanan_kasir) {
                $total_jumlah                                                             = $pesanan_kasir->barang_pesanan_kasir()->totalJumlahDalamSatuanUtama($barang_pesanan_kasir->id_barang);
                [$barang_pesanan_kasir->harga, $barang_pesanan_kasir->harga_satuan_utama] = $barang_pesanan_kasir->barang->hargaJual($total_jumlah, $barang_pesanan_kasir->id_satuan);
                if ($pesanan_kasir->mode_kasir == 'RESEP') {
                    $barang_pesanan_kasir->harga              = $this->hargaJualResep($barang_pesanan_kasir->harga, $this->setting->persentase_jual_resep);
                    $barang_pesanan_kasir->harga_satuan_utama = $this->hargaJualResep($barang_pesanan_kasir->harga_satuan_utama, $this->setting->persentase_jual_resep);
                }
                $barang_pesanan_kasir->sub_total = $barang_pesanan_kasir->jumlah * $barang_pesanan_kasir->harga;
                $barang_pesanan_kasir->save();
            }
            DB::commit();
            $this->dispatch('update_pesanan_kasir');
            $this->dispatch('alert', message: 'Berhasil Mengubah Mode Kasir', status: 'success');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('alert', message: $e->getMessage(), status: 'error');
            $this->mode_kasir = $value == 'RESEP' ? 'UMUM' : 'RESEP';
            $this->dispatch('selected-mode_kasir', selected: $this->mode_kasir);
        }

    }

    public function render()
    {
        return view('livewire.kasir.components.select-mode-kasir');
    }
}
