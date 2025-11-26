<?php
namespace App\Livewire\Pemasukan\Helpers;

use Illuminate\Support\Facades\DB;

trait TraitKasirMode
{
    public function updatedModeKasir($value)
    {
        try {
            $this->validate(['mode_kasir' => 'required|in:UMUM,RESEP']);
            DB::beginTransaction();
            $this->loadRelation();
            // dd($this->pesanan_kasir->toArray());
            foreach ($this->pesanan_kasir->barang_pesanan_kasir as $barang_pesanan_kasir) {
                // $total_jumlah                                                             = $this->pesanan_kasir->barang_pesanan_kasir->where('id_barang', $barang_pesanan_kasir->id_barang)->sum('jumlah_dalam_satuan_utama');
                $total_jumlah                                                             = $this->getTotalJumlahDalamSatuanUtama($barang_pesanan_kasir->id_barang);
                [$barang_pesanan_kasir->harga, $barang_pesanan_kasir->harga_satuan_utama] = $barang_pesanan_kasir->barang->hargaJual($total_jumlah, $barang_pesanan_kasir->id_satuan);
                if ($value == 'RESEP') {
                    $barang_pesanan_kasir->harga              = $this->hargaJualResep($barang_pesanan_kasir->harga, $this->setting->persentase_jual_resep);
                    $barang_pesanan_kasir->harga_satuan_utama = $this->hargaJualResep($barang_pesanan_kasir->harga_satuan_utama, $this->setting->persentase_jual_resep);
                }
                $barang_pesanan_kasir->sub_total = $barang_pesanan_kasir->jumlah * $barang_pesanan_kasir->harga;
                $barang_pesanan_kasir->save();
            }
            $this->pesanan_kasir->update(['mode_kasir' => $value]);
            $this->dispatch('updatedModeKasir',value:$value);
            DB::commit();
            $this->dispatch('alert', message: 'Berhasil Mengubah Mode Kasir', status: 'success');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('alert', message: $e->getMessage(), status: 'error');
            $this->mode_kasir = $value == 'RESEP' ? 'UMUM' : 'RESEP';
            $this->dispatch('selected-mode_kasir', selected: $this->mode_kasir);
        }
    }

    public function updatedModeShift($value)
    {
        try {
            $this->validate(['mode_shift' => 'required|in:PAGI,MALAM']);
            $this->pesanan_kasir->update(['mode_shift' => $value]);
            $this->dispatch('alert', message: 'Berhasil Mengubah Mode Shift', status: 'success');
        } catch (\Exception $e) {
            $this->dispatch('alert', message: $e->getMessage(), status: 'error');
            throw $e;
        }

    }
}