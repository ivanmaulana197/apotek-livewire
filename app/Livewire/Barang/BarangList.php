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

    public function mount()
    {
        $this->sortBy  = 'barang.updated_at';
        $this->sortDir = 'desc';
    }

    public function getDataBarang()
    {
        return Barang::query()
            ->with([
                'kategories:id,nama',
                'raks:id,nama',
                // 'opsi_harga:id,id_barang,harga,jumlah_min,id_satuan',
                'opsi_harga.satuan:id,nama',
                'opsi_satuan',
                // 'satuan_utama:id,nama',
            ])
            ->join('satuan as satuan_utama', 'satuan_utama.id', '=', 'barang.id_satuan_utama')
            ->select('barang.*', 'satuan_utama.nama as nama_satuan_utama')
        // ->leftjoin('kategori_barang', 'kategori_barang.id_barang', '=', 'barang.id')
        // ->leftjoin('kategori', 'kategori.id', '=', 'kategori_barang.id_kategori')
        // ->leftjoin('rak_barang', 'rak_barang.id_barang', '=', 'barang.id')
        // ->leftjoin('rak', 'rak.id', '=', 'rak_barang.id_rak')
        // ->leftjoin('satuan as satuan_utama', 'satuan_utama.id', '=', 'barang.id_satuan_utama')
        // ->leftjoin('satuan_barang', 'satuan_barang.id_barang', '=', 'barang.id')
        // ->leftjoin('satuan as opsi_satuan', 'opsi_satuan.id', '=', 'satuan_barang.id_satuan')
            ->search($this->search,
                ['barang.kode', 'barang.nama', 'barang.stok', 'barang.harga_beli_default'],
                [
                    'kategories'   => ['nama'],
                    'raks'         => ['nama'],
                    'satuan_utama' => ['satuan_utama.nama'],
                    'opsi_satuan'  => ['nama'],
                ])
            ->filter($this->filters)
            ->groupBy('barang.id')
            ->orderBy($this->sortBy, $this->sortDir)->paginate($this->perPage);
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
        // dd($data->toArray());
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