<?php
namespace App\Livewire\KasirCadangan\Helpers;

trait BarangPesananHelper
{

    private function updateHargaPesananLain($barang, $id_barang_pesanan, $harga_satuan_utama)
    {
        $pesanans = $this->pesanan_kasir->barang_pesanan_kasir->where('id_barang', $barang['id'])->where('id', '!=', $id_barang_pesanan)->values();
        foreach ($pesanans as $pesanan) {
            $harga_new = $this->calculateJumlahDalamSatuanUtama(
                $barang,
                $harga_satuan_utama,
                $pesanan->id_satuan
            );

            $pesanan->update([
                'harga'              => $harga_new,
                'harga_satuan_utama' => $harga_satuan_utama,
                'sub_total'          => $pesanan->jumlah * $harga_new,
            ]);
        }
    }

    // pesanan_kasir harus ada barang_pesanan_kasir.barang.opsi_satuan
    private function updateHargaPesananBarang($barang, $harga_satuan_utama, $except_id_barang_pesanan = null)
    {
        $pesanans = $this->pesanan_kasir->barang_pesanan_kasir
            ->where('id_barang', $barang['id'])
            ->when($except_id_barang_pesanan, fn($q) => $q->where('id', '!=', $except_id_barang_pesanan))
            ->values();
        foreach ($pesanans as $pesanan) {
            $harga_new = $this->calculateJumlahDalamSatuanUtama(
                $barang,
                $harga_satuan_utama,
                $pesanan->id_satuan
            );

            $pesanan->update([
                'harga'              => $harga_new,
                'harga_satuan_utama' => $harga_satuan_utama,
                'sub_total'          => $pesanan->jumlah * $harga_new,
            ]);
        }
    }
    private function cekStokBarang($total_jumlah, $stok)
    {
        if ($total_jumlah > $stok) {
            $this->dispatch('alert', message: 'Stok barang tidak mencukupi.', status: 'error');
            return false;
        }
        return true;
    }

}
