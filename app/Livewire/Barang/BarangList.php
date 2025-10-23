<?php
namespace App\Livewire\Barang;

use App\Models\Barang\Barang;
use App\Models\DataMaster\Kategori;
use App\Models\DataMaster\Rak;
use App\Traits\CustomDataTable;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;

class BarangList extends Component
{
    use WithPagination, WithoutUrlPagination, CustomDataTable;
    public $filters = [
        'id_kategories' => [],
        'id_raks'       => [],
    ];

    public function getDataBarang()
    {
        try {
            return Barang::with(['kategories', 'raks', 'opsi_harga', 'opsi_satuan', 'satuan_utama', 'opsi_harga.satuan'])
                ->select('barang.*')
                ->join('kategori_barang', 'barang.id', '=', 'kategori_barang.id_barang')
                ->join('kategori', 'kategori_barang.id_kategori', '=', 'kategori.id')
                ->join('satuan', 'barang.id_satuan_utama', '=', 'satuan.id')
                ->join('rak_barang', 'barang.id', '=', 'rak_barang.id_barang')
                ->join('rak', 'rak_barang.id_rak', '=', 'rak.id')
                ->search($this->search, ['barang.kode', 'barang.nama', 'barang.stok', 'barang.harga_beli_default'], ['kategories' => ['kategori.nama'], 'raks' => ['rak.nama'], 'satuan_utama' => ['satuan.nama']])
                ->filter($this->filters)
                ->orderBy($this->sortBy, $this->sortDir)
                ->groupBy('barang.id')
                ->paginate($this->perPage);
        } catch (\Exception $e) {
            $this->dispatch('alert', message: 'Gagal Mengambil Data Barang', status: 'error');
            $this->sortBy  = 'updated_at';
            $this->sortDir = 'desc';

            return Barang::with(['kategories', 'raks', 'opsi_harga', 'opsi_satuan', 'satuan_utama', 'opsi_harga.satuan'])
                ->select('barang.*')
                ->join('kategori_barang', 'barang.id', '=', 'kategori_barang.id_barang')
                ->join('kategori', 'kategori_barang.id_kategori', '=', 'kategori.id')
                ->join('satuan', 'barang.id_satuan_utama', '=', 'satuan.id')
                ->join('rak_barang', 'barang.id', '=', 'rak_barang.id_barang')
                ->join('rak', 'rak_barang.id_rak', '=', 'rak.id')
                ->search($this->search, ['barang.kode', 'barang.nama', 'barang.stok', 'barang.harga_beli_default'], ['kategories' => ['kategori.nama'], 'raks' => ['rak.nama'], 'satuan_utama' => ['satuan.nama']])
                ->filter($this->filters)
                ->orderBy($this->sortBy, $this->sortDir)
                ->groupBy('barang.id')
                ->paginate($this->perPage);
        }
    }

    #[On('delete-barang')]
    public function delete($id): void
    {
        try {
            DB::BeginTransaction();
            $barang = Barang::findOrFail($id);
            // Hapus relasi dengan rak (pivot table)

            $barang->kategories()->detach();
            $barang->raks()->detach();

            // Hapus relasi dengan opsi_satuan (pivot table)
            $barang->opsi_satuan()->detach();

            // Hapus relasi dengan opsi_harga (menggunakan relasi hasMany)
            $barang->opsi_harga()->delete();

            // Hapus barang
            $barang->delete();
            DB::commit();

            $this->dispatch('alert', message: 'Berhasil Menghapus Barang');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('alert', message: 'Gagal Menghapus Barang', status: 'error', subtitle: $e->getMessage());
        }
    }

    public function render()
    {
        $data = $this->getDataBarang();
        return view('livewire.barang.barang-list', [
            'barangs' => $data,
        ]);

    }

    #[Computed(persist: true, seconds: 3600, cache: true)]
    public function kategories()
    {
        return Kategori::select('id', 'nama')->get();
    }
    #[Computed(persist: true, seconds: 3600, cache: true)]
    public function raks()
    {
        return Rak::select('id', 'nama')->get();
    }
}