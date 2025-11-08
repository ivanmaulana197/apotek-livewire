<?php
namespace App\Livewire\Barang\BarangDetail\Components;

use App\Helpers\FormatHelper;
use App\Models\Barang\StokBatchBarang;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class StokBarangForm extends Component
{
    public $id_barang, $all_satuan, $nama_satuan_utama;
    public $jumlah, $tgl_expired, $harga_beli;

    public function save()
    {
        try {
            $this->formattedForm();
            $this->validate([
                'jumlah'      => 'required|numeric|min:0',
                'tgl_expired' => 'required|date|after_or_equal:today',
                'harga_beli'  => 'nullable|numeric',
            ]);
            DB::beginTransaction();
            StokBatchBarang::create([
                'id_barang'   => $this->id_barang,
                'jumlah'      => $this->jumlah,
                'harga_beli'  => $this->harga_beli,
                'tgl_expired' => $this->tgl_expired,
            ]);
            $this->dispatch('update-stok-barang');
            $this->dispatch('alert', status: 'success', message: 'Stok Barang Berhasil Diupdate');
            $this->dispatch('toggle-modal-edit_stok_barang', [
                'edit_stok_barangModal' => false,
            ]);
            DB::commit();
            $this->resetForm();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->formattedForm(false);
            $this->dispatch('alert', status: 'error', message: 'Gagal Mengupdate Stok Barang', subtitle: $e->getMessage());
            throw $e;
        }
    }

    public function resetForm()
    {
        $this->jumlah      = '';
        $this->tgl_expired = '';
        $this->harga_beli  = '';
    }

    public function formattedForm($serverMode = true)
    {
        $this->jumlah      = $serverMode ? FormatHelper::formatStringToNumber($this->jumlah) : FormatHelper::formatNumberToString($this->jumlah);
        $this->harga_beli  = $serverMode ? FormatHelper::formatStringToNumber($this->harga_beli) : FormatHelper::formatNumberToString($this->harga_beli);
        $this->tgl_expired = $serverMode ? FormatHelper::formatDateStringToDate($this->tgl_expired) : FormatHelper::formatDate($this->tgl_expired);
    }

    public function render()
    {
        return view('livewire.barang.barang-detail.components.stok-barang-form');
    }
}
