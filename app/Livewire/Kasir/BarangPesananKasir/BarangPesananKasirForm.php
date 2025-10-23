<?php
namespace App\Livewire\Kasir\BarangPesananKasir;

use App\Helpers\FormatHelper;
use App\Livewire\Kasir\Helpers\KasirHelper;
use App\Models\Barang\Barang;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;
use Livewire\Component;

class BarangPesananKasirForm extends Component
{
    use KasirHelper;
    public $id_barang, $jumlah, $id_satuan, $harga_jual, $harga_satuan_utama, $subtotal, $diskon, $id_list_diskon, $id_promo_barang;
    public $pesanan_kasir, $persen_resep;
    public $data_barang;

    #[On('selectedBarang')]
    public function selectedBarang($idBarang)
    {
        if (empty($idBarang)) {
            $this->resetForm();
            return;
        }
        $this->id_barang   = $idBarang;
        $this->data_barang = Barang::query()
        // ->select('id', 'id_satuan_utama', 'harga_jual_markup_utama', 'stok')
            ->with(['opsi_satuan', 'opsi_harga.satuan'])
            ->where('id', $idBarang)
            ->first();
        $this->id_satuan = $this->data_barang->id_satuan_utama;

        // karena jumlah masih kosong maka harga jual nya asih belum grosir
        // $this->harga_jual = $this->data_barang->hargaJual();
        $this->updateHarga();
        $this->id_promo_barang = $this->pesanan_kasir->promo_pesanan_kasir()
            ->whereHas('promo_barang', function ($q) {
                $q->where('id_barang', $this->id_barang);
            })->first()?->id_promo_barang;

        $this->dispatch('update_satuan', data: FormatHelper::formatTomSelect($this->data_barang->all_satuan, 'id_satuan', 'satuan'), selected: $this->data_barang->id_satuan_utama);
    }

    public function showDiskon()
    {
        $this->dispatch('toggle-modal-diskon', diskonModal: true);
        // $promo_barang = $this->pesanan_kasir->barang_pesanan_kasir()->where('id_barang', $this->id_barang)->first();
        // if ($promo_barang && $promo_barang->id_promo_barang) {
        //     $this->id_promo_barang = $promo_barang->id_promo_barang;
        // }
        $this->dispatch('list-diskon', id_promo_barang_selected: $this->id_promo_barang, id_promo_barangs: $this->id_list_diskon);
    }

    public function updateHarga()
    {
        //menghitung jumlah dalam satuan utama
        $jumlah = FormatHelper::formatStringToNumber($this->jumlah);
        // $jumlah_dalam_satuan_utama = $this->calculateJumlahDalamSatuanUtama($jumlah, $this->id_satuan, $this->data_barang);
        $jumlah_dalam_satuan_utama = $this->data_barang->calculateJumlahDalamSatuanUtama($jumlah, $this->id_satuan);
        $total_jumlah              = $this->pesanan_kasir->barang_pesanan_kasir()->totalJumlahDalamSatuanUtama($this->id_barang, $jumlah_dalam_satuan_utama);

        //menggunakan total_jumlah agar mendapatkan harga jual dan harga satuan utama untuk seluruh nya
        [$this->harga_jual, $this->harga_satuan_utama] = $this->data_barang->hargaJual($total_jumlah, $this->id_satuan);
        $this->subtotal                                = $jumlah * $this->harga_jual;
        if ($this->pesanan_kasir->mode_kasir == 'RESEP') {
            $this->harga_jual         = $this->hargaJualResep($this->harga_jual, $this->persen_resep);
            $this->harga_satuan_utama = $this->hargaJualResep($this->harga_satuan_utama, $this->persen_resep);
            $this->subtotal           = $jumlah * $this->harga_jual;
        } else {
            $this->id_list_diskon = $this->getListPromoBarang($this->id_barang, $total_jumlah, $this->pesanan_kasir->id_member);
            // $this->dispatch('get-diskon-from-inptan',id_pesanan_kasir: $this->pesanan_kasir->id, id_barang: $this->id_barang, jumlah_dalam_satuan_utama: $total_jumlah, id_member: $this->pesanan_kasir->id_member);
        }
    }

    #[On('select-diskon')]
    public function setDiskon($id_promo_barang)
    {
        $this->id_promo_barang = $id_promo_barang;
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
            $jumlah_dalam_satuan_utama = $this->data_barang->calculateJumlahDalamSatuanUtama($this->jumlah, $this->id_satuan);
            $total_jumlah              = $this->pesanan_kasir->barang_pesanan_kasir()->totalJumlahDalamSatuanUtama($this->id_barang, $jumlah_dalam_satuan_utama);

            if ($total_jumlah > $this->data_barang->stok) {
                $this->jumlah = FormatHelper::formatNumberToString($this->jumlah);
                $this->dispatch('alert', message: 'Stok barang tidak mencukupi.', status: 'error');
                $this->dispatch('focus-input-jumlah');
                DB::rollback();
                return;
            }

            $barang_pesanan = $this->pesanan_kasir->barang_pesanan_kasir->where('id_barang', $this->id_barang)->where('id_satuan', $this->id_satuan)->first();
            $barang_pesanan ? $this->updateExistingBarangPesanan($barang_pesanan, $jumlah_dalam_satuan_utama) : $this->createNewBarangPesanan($jumlah_dalam_satuan_utama);
            $this->setPromoBarangKasir($this->id_barang, $this->id_promo_barang);
            if (! $this->cekStokPromoBarang($this->id_barang, $total_jumlah)) {
                $this->dispatch('alert', message: 'Stok barang bonus tidak mencukupi.', status: 'error');
                $this->dispatch('focus-input-jumlah');
                DB::rollback();
                return;
            }
            $this->pesanan_kasir->update([
                'total_harga' => $this->pesanan_kasir->barang_pesanan_kasir()->sum('sub_total'),
            ]);
            DB::commit();
            $this->dispatch('focus-select-barang');
            $this->dispatch('update_pesanan_kasir');
            $this->resetForm();
            $this->dispatch('alert', message: 'Barang berhasil ditambahkan.', status: 'success');
        } catch (\Exception $e) {
            DB::rollback();
            $this->jumlah = FormatHelper::formatNumberToString($this->jumlah);
            $this->dispatch('alert', status: 'error', message: $e->getMessage());
            throw $e;
        }
    }

    private function updateExistingBarangPesanan($barangPesanan, $jumlah_dalam_satuan_utama)
    {
        $barangPesanan->update([
            'jumlah'                    => $barangPesanan->jumlah + $this->jumlah,
            'jumlah_dalam_satuan_utama' => $barangPesanan->jumlah_dalam_satuan_utama + $jumlah_dalam_satuan_utama,
            'harga'                     => $this->harga_jual,
            'harga_satuan_utama'        => $this->harga_satuan_utama,
            // 'id_promo_barang'           => $this->id_promo_barang,
            'sub_total'                 => ($barangPesanan->jumlah + $this->jumlah) * $this->harga_jual,
        ]);

    }

    private function createNewBarangPesanan($jumlah_dalam_satuan_utama)
    {
        $this->pesanan_kasir->barang_pesanan_kasir()->create([
            'id_barang'                 => $this->id_barang,
            'jumlah'                    => $this->jumlah,
            'id_satuan'                 => $this->id_satuan,
            // 'id_promo_barang'           => $this->id_promo_barang,
            'jumlah_dalam_satuan_utama' => $jumlah_dalam_satuan_utama,
            'harga'                     => $this->harga_jual,
            'harga_satuan_utama'        => $this->harga_satuan_utama,
            'sub_total'                 => $this->subtotal,
        ]);
    }

    public function render()
    {
        return view('livewire.kasir.barang-pesanan-kasir.barang-pesanan-kasir-form');
    }

    private function resetForm()
    {
        $this->reset(['id_barang', 'jumlah', 'id_satuan', 'data_barang', 'harga_jual', 'harga_satuan_utama', 'subtotal', 'diskon', 'id_list_diskon', 'id_promo_barang']);
        $this->dispatch('refresh_selected_barang');
        $this->dispatch('update_barang', selected: null);
        $this->dispatch('update_satuan', data: [], selected: null);
    }

    #[On('update_pesanan_kasir')]
    public function update_pesanan_kasir()
    {
        if ($this->data_barang) {
            $this->updateHarga();
        }
    }

    public function updatedJumlah($jumlah)
    {
        if ($this->id_barang) {
            $this->updateHarga();
        }
    }

    public function updatedIdSatuan($id_satuan)
    {
        if ($this->id_barang) {
            $this->updateHarga();
        }
    }
}
