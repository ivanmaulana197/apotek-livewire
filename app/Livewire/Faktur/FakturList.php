<?php
namespace App\Livewire\Faktur;

use App\Models\Barang\Barang;
use App\Models\DataMaster\Supplier;
use App\Models\Faktur\Faktur;
use App\Traits\CustomDataTable;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;

class FakturList extends Component
{
    use WithPagination, WithoutUrlPagination, CustomDataTable;

    public $filters = [
        'tgl_jatuh_tempo' => null,
        'id_supplier'     => '',
        'created_at'      => null,
        'status'          => '',
    ];

    public $id_barang;

    public function mount()
    {
        $this->sortBy = 'faktur.updated_at';
    }

    #[On('update-status-faktur')]
    public function updateStatusFaktur($id)
    {
        $faktur         = Faktur::find($id);
        $faktur->status = $faktur->status == 'LUNAS' ? 'BELUM LUNAS' : 'LUNAS';
        $faktur->save();
    }

    #[On('delete-faktur')]
    public function deleteFaktur($id)
    {
        try {
            DB::beginTransaction(); // Hapus faktur
            $faktur = Faktur::with(['faktur_barang.detail_faktur.stok_batch'])->findOrFail($id);
            foreach ($faktur->faktur_barang as $faktur_barang) { //history_barang
                $barang = Barang::find($faktur_barang->id_barang);
                //delete history_barangnya juga
                // $faktur_barang->append('jumlah_dalam_satuan_utama');
                $jumlah_hapus = $faktur_barang->jumlah_dalam_satuan_utama;

                if ($barang) {
                    // Kurangi stok barang
                    $barang->decrement('stok', $jumlah_hapus);
                }
                foreach ($faktur_barang->detail_faktur->stok_batch as $stok_batch) {
                    $stok_batch->decrement('jumlah', $jumlah_hapus);
                    // kalau jumlah sudah 0 bisa sekalian dihapus
                    if ($stok_batch->jumlah <= 0) {
                        $stok_batch->delete();
                    }
                }
                $faktur_barang->detail_faktur->stok_batch()->detach();
                $faktur_barang->detail_faktur->delete();
                // Hapus barang masuk yang terkait
                $faktur_barang->delete();
            }
            $faktur->delete();
            DB::commit();
            $this->dispatch('alert', message: 'Berhasil Menghapus Faktur');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('alert', message: 'Gagal Menghapus Faktur', status: 'error');
            throw $e;
        }
    }

    public function getFaktur()
    {
        return Faktur::with(['supplier', 'faktur_barang'])
            ->select('faktur.*')
            ->join('supplier', 'faktur.id_supplier', '=', 'supplier.id')
            ->join('users', 'faktur.id_user', '=', 'users.id')
            ->search($this->search, ['faktur.no_faktur', 'faktur.total_biaya', 'faktur.status'], ['supplier' => ['supplier.nama'], 'user' => ['nama']])
            ->filter($this->filters);
    }

    public function render()
    {
        $data = $this->getFaktur();

        if ($this->id_barang) {
            $data = $data->join('history_barang', 'history_barang.referensi_id', '=', 'faktur.id')
                ->join('detail_faktur_barang', 'detail_faktur_barang.id_history_barang', '=', 'history_barang.id')
                ->where('history_barang.referensi_type', Faktur::class)
                ->where('history_barang.id_barang', $this->id_barang);
        }
        $hutang = (clone $data)->where('faktur.status', 'BELUM LUNAS')->sum('biaya_belanja');
        $data   = $data->orderBy($this->sortBy, $this->sortDir)->groupBy('faktur.id')->paginate($this->perPage);

        return view('livewire.faktur.faktur-list', ['fakturs' => $data, 'hutang' => $hutang]);
    }

    #[Computed(persist: true, seconds: 3600, cache: true)]
    public function suppliers()
    {
        return Supplier::select('id', 'nama')->get();
    }
}