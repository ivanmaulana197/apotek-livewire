<?php
namespace App\Livewire\KasirCadangan\Helpers;

use Illuminate\Support\Facades\DB;

trait KasirModeHelper
{
    public function cekPromoSelected($id_barang)
    {
        return $this->pesanan_kasir->promo_pesanan_kasir->filter(fn($promo) => $promo->promo_barang?->id_barang == $id_barang)->first();
    }
    public function updatedModeKasir($value)
    {
        try {
            $this->validate(['mode_kasir' => 'required|in:UMUM,RESEP']);
            DB::beginTransaction();
            $this->pesanan_kasir->loadMissing([
                'promo_pesanan_kasir.promo_barang',
                'barang_pesanan_kasir.satuan:id,nama',
                'barang_pesanan_kasir.barang.opsi_satuan',
                'barang_pesanan_kasir.barang.satuan_utama',
                'barang_pesanan_kasir.barang.opsi_harga.satuan:id,nama',
                // 'barang_pesanan_kasir.barang.opsi_harga.barang:id,id_satuan_utama',
                // 'barang_pesanan_kasir.barang.opsi_harga.barang.opsi_satuan:id',
            ]);
            $status = 'success';
            foreach ($this->pesanan_kasir->barang_pesanan_kasir as $index => $barang_pesanan_kasir) {
                // $total_jumlah                                                             = $this->pesanan_kasir->barang_pesanan_kasir->where('id_barang', $barang_pesanan_kasir->id_barang)->sum('jumlah_dalam_satuan_utama');
                // [$barang_pesanan_kasir->harga, $barang_pesanan_kasir->harga_satuan_utama] = $barang_pesanan_kasir->barang->hargaJual($total_jumlah, $barang_pesanan_kasir->id_satuan);
                $total_jumlah                                                             = $this->getTotalJumlahDalamSatuanUtama($barang_pesanan_kasir->id_barang);
                [$barang_pesanan_kasir->harga, $barang_pesanan_kasir->harga_satuan_utama] = $this->getHargaJual($barang_pesanan_kasir->barang, $total_jumlah, $barang_pesanan_kasir->id_satuan);
                if ($value == 'RESEP') {
                    $barang_pesanan_kasir->harga              = $this->hargaJualResep($barang_pesanan_kasir->harga, $this->persen_resep);
                    $barang_pesanan_kasir->harga_satuan_utama = $this->hargaJualResep($barang_pesanan_kasir->harga_satuan_utama, $this->persen_resep);
                }
                $barang_pesanan_kasir->sub_total = $barang_pesanan_kasir->jumlah * $barang_pesanan_kasir->harga;
                $barang_pesanan_kasir->save();
                $next = $this->pesanan_kasir->barang_pesanan_kasir->get($index + 1);
                if ($next?->id_barang == $barang_pesanan_kasir->id_barang) {
                    continue;
                }
                $sub_total         = $this->pesanan_kasir->barang_pesanan_kasir->where('id_barang', $barang_pesanan_kasir->id_barang)->sum('sub_total');
                $id_promo_selected = $this->cekPromoSelected($barang_pesanan_kasir->id_barang);
                $promo             = $this->applyPromoBarang($barang_pesanan_kasir->id_barang, $total_jumlah, $sub_total, $id_promo_selected?->promo_barang?->id);
                if ($promo && $promo['status'] == 'warning') {
                    $status = 'warning';
                }
            }
            $id_promo_transaksi_selected = $this->pesanan_kasir->promo_pesanan_kasir->whereNotNull('id_promo_transaksi')->first()?->id_promo_transaksi ?? null;
            $promo_transaksi             = $this->sinkronPromoTransaksi($id_promo_transaksi_selected);
            if ($promo_transaksi && $promo_transaksi['status'] == 'error') {
                $this->dispatch('alert', message: $promo_transaksi['message'], status: 'warning');
                DB::rollback();
                return;
            }

            $this->pesanan_kasir->update([
                'mode_kasir'  => $value,
                'total_harga' => $this->pesanan_kasir->barang_pesanan_kasir->sum('sub_total'),
            ]);

            $this->dispatch('updatedModeKasir', value: $value);
            DB::commit();

            $this->syncFrontEnd();
            if ($status == 'success' && (empty($promo_transaksi) || ($promo_transaksi['status'] ?? null) === 'success')) {
                $this->dispatch('alert', message: 'Berhasil Mengubah Mode Kasir', status: 'success');
            }
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
