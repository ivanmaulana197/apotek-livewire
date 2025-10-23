<?php
namespace App\Livewire\Barang;

use App\Helpers\PembulatanHelper;
use App\Models\Barang\Barang;
use Livewire\Component;

class BarangIndex extends Component
{

    public function pembulatanAll()
    {
        PembulatanHelper::init();
        $barangs = Barang::query()
            ->with(['opsi_harga'])
            ->get();

        foreach ($barangs as $barang) {
            $barang->append('all_satuan');
            // dd($barang->toArray());
            $temp                      = PembulatanHelper::pembulatanHargaBarang($barang->toArray());
            $barang->harga_jual_utama  = $temp['harga_jual_utama'];
            $barang->persen_jual_utama = $temp['persen_jual_utama'];

            // Hapus opsi harga lama
            $barang->opsi_harga()->delete();

            // Tambah opsi harga baru
            foreach ($temp['opsi_harga'] as $opsi_harga) {
                // pastikan kolom id_barang dihapus agar tidak bentrok (karena create() akan otomatis isi)
                unset($opsi_harga['id_barang']);
                unset($opsi_harga['id']); // jika ada id lama
                $barang->opsi_harga()->create($opsi_harga);
            }
            $barang->save();
        }
    }

    public function render()
    {
        return view('livewire.barang.barang-index');
    }
}