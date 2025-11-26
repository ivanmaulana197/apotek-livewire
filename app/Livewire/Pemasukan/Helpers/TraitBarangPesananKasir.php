<?php
namespace App\Livewire\Pemasukan\Helpers;

use App\Helpers\FormatHelper;
use App\Models\Barang\Barang;
use Illuminate\Support\Facades\DB;

trait TraitBarangPesananKasir
{
    public $id_list_promo, $jumlah_dalam_satuan_utama;
    // #[On('selectedBarang')]

    public function getDataBarang($idBarang)
    {
        $id_promo_barang = $this->pesanan_kasir->promo_pesanan_kasir
            ->filter(fn($promo) => $promo->promo_barang?->id_barang == $idBarang)
            ->first()?->id_promo_barang;
        $this->dispatch('selectedPromoBarang', idPromoBarang: $id_promo_barang);
        return Barang::query()
            ->with(['satuan_utama', 'opsi_satuan', 'opsi_harga.satuan'])
            ->where('id', $idBarang)
            ->first();
    }

    public function selectedBarang($idBarang)
    {
        // dd($idBarang);
        if (empty($idBarang)) {
            $this->resetBarangPesananKasirForm();
            return;
        }
        $this->id_barang   = $idBarang;
        $this->data_barang = Barang::query()
            ->with(['satuan_utama', 'opsi_satuan', 'opsi_harga.barang', 'opsi_harga.satuan'])
            ->where('id', $idBarang)
            ->first();
        // dd($this->data_barang->toArray());
        $this->id_satuan = $this->data_barang->id_satuan_utama;
        // karena jumlah masih kosong maka harga jual nya asih belum grosir
        [$this->harga_jual, $this->harga_satuan_utama] = $this->data_barang->hargaJual();
        $this->pesanan_kasir->load('promo_pesanan_kasir.promo_barang');
        $this->updateHarga();
        $this->id_promo_barang = $this->pesanan_kasir->promo_pesanan_kasir
            ->filter(fn($promo) => $promo->promo_barang?->id_barang == $this->id_barang)
            ->first()?->id_promo_barang;

        $this->dispatch('update_satuan', data: FormatHelper::formatTomSelect($this->data_barang->all_satuan, 'id_satuan', 'satuan'), selected: $this->data_barang->id_satuan_utama);
    }

    public function updateHarga()
    {
        $this->loadRelation();
        $this->loadRelationDataBarang();
        $jumlah                                        = FormatHelper::formatStringToNumber($this->jumlah);
        $jumlah_dalam_satuan_utama                     = $this->data_barang->calculateJumlahDalamSatuanUtama($jumlah, $this->id_satuan);
        $total_jumlah                                  = $this->getTotalJumlahDalamSatuanUtama($this->id_barang) + $jumlah_dalam_satuan_utama;
        [$this->harga_jual, $this->harga_satuan_utama] = $this->data_barang->hargaJual($total_jumlah, $this->id_satuan);
        $this->sub_total                               = $jumlah * $this->harga_jual;
        if ($this->pesanan_kasir->mode_kasir == 'RESEP') {
            $this->harga_jual         = $this->hargaJualResep($this->harga_jual, $this->persen_resep);
            $this->harga_satuan_utama = $this->hargaJualResep($this->harga_satuan_utama, $this->persen_resep);
            $this->sub_total          = $jumlah * $this->harga_jual;
        }
        $this->id_list_promo = $this->getListPromoBarang($this->id_barang, $total_jumlah, $this->pesanan_kasir->id_member);
    }

    public function addBarangPesananKasir()
    {
        try {
            $this->jumlah = FormatHelper::formatStringToNumber($this->jumlah);
            $this->validate(
                [
                    'id_barang' => 'required|exists:barang,id',
                    'jumlah'    => 'required|numeric|min:1',
                    'id_satuan' => 'required|exists:satuan,id',
                ],
                [
                    'jumlah.min' => 'Minimal pembelian 1 item.',
                ],
            );
            DB::beginTransaction();
            $total_jumlah = $this->list_total_jumlah_dalam_satuan_utama[$this->id_barang] + $this->jumlah_dalam_satuan_utama;
            dd($this->pesanan_kasir);
            // $jumlah_dalam_satuan_utama = $this->data_barang->calculateJumlahDalamSatuanUtama($this->jumlah, $this->id_satuan);
            // $total_jumlah              = $this->pesanan_kasir->barang_pesanan_kasir()->totalJumlahDalamSatuanUtama($this->id_barang, $jumlah_dalam_satuan_utama);

            if ($total_jumlah > $this->data_barang['stok']) {
                $this->jumlah = FormatHelper::formatNumberToString($this->jumlah);
                $this->dispatch('alert', message: 'Stok barang tidak mencukupi.', status: 'error');
                $this->dispatch('focus-input-jumlah');
                DB::rollback();
                return;
            }
            // $barang_pesanan = $this->pesanan_kasir->barang_pesanan_kasir->where('id_barang', $this->id_barang)->where('id_satuan', $this->id_satuan)->first();
            // $barang_pesanan ? $this->updateExistingBarangPesanan($barang_pesanan, $this->jumlah_dalam_satuan_utama) : $this->createNewBarangPesanan($this->jumlah_dalam_satuan_utama);

            // $this->setPromoBarangKasir($this->id_barang, $this->id_promo_barang);
            // if (! $this->cekStokPromoBarang($this->id_barang, $total_jumlah)) {
            //     $this->dispatch('alert', message: 'Stok barang bonus tidak mencukupi.', status: 'error');
            //     $this->dispatch('focus-input-jumlah');
            //     DB::rollback();
            //     return;
            // }
            $this->pesanan_kasir->update([
                'total_harga' => $this->pesanan_kasir->barang_pesanan_kasir()->sum('sub_total'),
            ]);
            // DB::commit();
            // $this->dispatch('focus-select-barang');
            // $this->dispatch('update_pesanan_kasir');
            // $this->resetForm();
            // $this->dispatch('alert', message: 'Barang berhasil ditambahkan.', status: 'success');
        } catch (\Exception $e) {
            DB::rollback();
            $this->jumlah = FormatHelper::formatNumberToString($this->jumlah);
            $this->dispatch('alert', status: 'error', message: $e->getMessage());
            throw $e;
        }
    }

    public function showPromo()
    {
        $this->dispatch('toggle-modal-promo_barang', promo_barangModal: true);
        $this->dispatch('list-promo-barang', id_promo_barang_selected: $this->id_promo_barang, id_promo_barangs: $this->id_list_promo);
    }

    public function loadRelationDataBarang()
    {
        if ($this->data_barang) {
            $this->data_barang->loadMissing(['satuan_utama', 'opsi_satuan', 'opsi_harga.barang', 'opsi_harga.satuan']);
        }
    }

    public function resetBarangPesananKasirForm()
    {
        $this->reset(['data_barang', 'id_barang', 'jumlah', 'id_satuan', 'harga_jual', 'harga_satuan_utama', 'sub_total', 'diskon', 'id_promo_barang']);
        $this->dispatch('refresh_selected_barang');
        $this->dispatch('update_barang', selected: null);
        $this->dispatch('update_satuan', data: [], selected: null);
    }
}
