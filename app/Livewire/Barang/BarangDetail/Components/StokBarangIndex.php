<?php
namespace App\Livewire\Barang\BarangDetail\Components;

use App\Helpers\FormatHelper;
use App\Models\Barang\Barang;
use App\Models\Barang\StokBatchBarang;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;
use Livewire\Component;

class StokBarangIndex extends Component
{

    public $id_barang, $nama_satuan_utama, $nama_barang, $stok_barangs, $stok_sistem_barang, $all_satuan, $isEditStok = false;
    public $total_stok_batch_barang;

    public function mount()
    {
        // dd($this->all_satuan);
        $this->getStokBarangs();
    }

    #[On('update-stok-barang')]
    public function getStokBarangs()
    {
        $data = StokBatchBarang::where('id_barang', $this->id_barang)
            ->orderBy('tgl_expired', 'asc')
            ->get();
        $this->total_stok_batch_barang = $data->sum('jumlah');
        $data                          = $data->each(function ($item) {
            $item->isEdit      = false;
            $item->jumlah      = FormatHelper::formatNumberToString($item->jumlah);
            $item->harga_beli  = FormatHelper::formatNumberToString($item->harga_beli);
            $item->tgl_expired = FormatHelper::formatDate($item->tgl_expired);
        });
        $this->stok_barangs = $data->toArray();
    }

    public function updateStokBarang($index)
    {
        try {
            $this->formattedForm($index);
            $this->validate([
                'stok_barangs.' . $index . '.jumlah'      => 'required|numeric|min:0',
                'stok_barangs.' . $index . '.harga_beli'  => 'nullable|numeric|min:0',
                'stok_barangs.' . $index . '.tgl_expired' => 'required|date|after_or_equal:today',
            ], ['required' => 'Kolom wajib di isi', 'date' => 'Format Tanggal Tidak Valid', 'after_or_equal' => 'Tanggal Expired harus sama atau Lebih Besar dari Hari Ini']);
            DB::beginTransaction();
            // $old_stok_barang = StokBatchBarang::with(['barang:id,id_satuan_utama,stok', 'barang.satuan_utama:id,nama'])->find($this->stok_barangs[$index]['id']);
            // $selisih_stok    = $this->stok_barangs[$index]['jumlah'] - $old_stok_barang->jumlah;
            // //update_stok dibarang
            // $old_stok_barang->barang->update(['stok' => $old_stok_barang->barang->stok + $selisih_stok]);
            // $old_stok_barang->update($this->stok_barangs[$index]);

            StokBatchBarang::find($this->stok_barangs[$index]['id'])->update($this->stok_barangs[$index]);
            $this->dispatch('update-stok-barang');
            $this->stok_barangs[$index]['isEdit'] = false;
            $this->dispatch('alert', status: 'success', message: 'Stok Barang Berhasil Diupdate');
            $this->formattedForm($index, false);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->formattedForm($index, false);
            $this->dispatch('alert', status: 'error', message: 'Gagal Mengupdate Stok Barang', subtitle: $e->getMessage());
            throw $e;
        }
    }
    public function cancelStokBarang($index)
    {
        $this->resetValidation(["stok_barangs.$index.jumlah", "stok_barangs.$index.tgl_expired", "stok_barangs.$index.harga_beli"]);
    }

    #[On('delete-stok-barang')]
    public function deleteStokBarang($id)
    {
        try {
            DB::beginTransaction();
            StokBatchBarang::find($id)->delete();
            $this->dispatch('update-stok-barang');
            $this->dispatch('alert', status: 'success', message: 'Stok Barang Berhasil Dihapus');
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('alert', status: 'error', message: 'Gagal Menghapus Stok Barang', subtitle: $e->getMessage());
            throw $e;
        }
    }

    public function formattedForm($id, $serverMode = true)
    {
        $this->stok_barangs[$id]['jumlah']      = $serverMode ? FormatHelper::formatStringToNumber($this->stok_barangs[$id]['jumlah']) : FormatHelper::formatNumberToString($this->stok_barangs[$id]['jumlah']);
        $this->stok_barangs[$id]['harga_beli']  = $serverMode ? FormatHelper::formatStringToNumber($this->stok_barangs[$id]['harga_beli']) : FormatHelper::formatNumberToString($this->stok_barangs[$id]['harga_beli']);
        $this->stok_barangs[$id]['tgl_expired'] = $serverMode ? FormatHelper::formatDateStringToDate($this->stok_barangs[$id]['tgl_expired']) : FormatHelper::formatDate($this->stok_barangs[$id]['tgl_expired']);
    }

    public function updateStokSistemBarang()
    {
        try {
            $this->stok_sistem_barang = FormatHelper::formatStringToNumber($this->stok_sistem_barang);
            $this->validate([
                'stok_sistem_barang' => 'required|numeric|min:0',
            ]);
            DB::beginTransaction();
            Barang::find($this->id_barang)->update(['stok' => $this->stok_sistem_barang]); /*  */
            $this->dispatch('update-stok-barang');
            $this->dispatch('update-stok-sistem-barang', stok_sistem: $this->stok_sistem_barang);
            $this->dispatch('alert', status: 'success', message: 'Stok Sistem Barang Berhasil Diupdate');
            $this->isEditStok         = false;
            $this->stok_sistem_barang = FormatHelper::formatNumberToString($this->stok_sistem_barang);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->stok_sistem_barang = FormatHelper::formatNumberToString($this->stok_sistem_barang);
            $this->dispatch('alert', status: 'error', message: 'Gagal Mengupdate Stok Sistem Barang', subtitle: $e->getMessage());
            throw $e;
        }
    }

    public function render()
    {
        return view('livewire.barang.barang-detail.components.stok-barang-index');
    }
}