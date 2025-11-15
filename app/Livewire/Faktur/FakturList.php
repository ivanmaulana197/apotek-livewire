<?php
namespace App\Livewire\Faktur;

use App\Models\Barang\Barang;
use App\Models\Barang\StokBatchBarang;
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
        $this->sortBy                = $this->defaultSortBy                = 'faktur.updated_at';
        $this->filters['created_at'] = [
            'start' => now()->startOfMonth(),
            'end'   => now()->endOfMonth(),
        ];
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
            $faktur = Faktur::with(['detail_faktur.detail_barang.barang:id,nama,kode,id_satuan_utama,stok,stok_min'])->findOrFail($id);
            foreach ($faktur->detail_faktur as $detail_faktur) { //history_barang
                $faktur_barang = $detail_faktur->detail_barang;
                //delete history_barangnya juga
                // $faktur_barang->append('jumlah_dalam_satuan_utama');
                $jumlah_hapus = $faktur_barang->jumlah_dalam_satuan_utama;
                // $faktur_barang->barang->decrement('stok', $jumlah_hapus);

                $faktur_barang->barang->update(['stok' => $faktur_barang->barang->stok - $jumlah_hapus]);
                $tgl_expired = $faktur_barang->tgl_expired[0]['tgl'];

                $stok = StokBatchBarang::where('id_barang', $faktur_barang->id_barang)->where('tgl_expired', $tgl_expired)->first();
                if ($stok) {
                    $stok->decrement('jumlah', $jumlah_hapus);
                    if ($stok->jumlah - $jumlah_hapus <= 0) {
                        $stok->delete();
                    }
                }
                $detail_faktur->detail_barang->delete();
                $detail_faktur->delete();
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
        // return Faktur::with(['supplier:id,nama'])
        //     ->select('faktur.*')
        //     ->join('supplier', 'faktur.id_supplier', '=', 'supplier.id')
        //     ->join('users', 'faktur.id_user', '=', 'users.id')
        //     ->search($this->search, ['faktur.no_faktur', 'faktur.total_biaya', 'faktur.status'], ['supplier' => ['supplier.nama'], 'user' => ['nama']])
        //     ->filter($this->filters);

        return Faktur::query()
            ->with(['supplier:id,nama', 'user:id,name'])
            ->when($this->id_barang, function ($query) {
                $query->whereHas('detail_faktur_barang', function ($query) {
                    $query->where('id_barang', $this->id_barang);
                })
                    ->with(['detail_faktur_barang' => function ($query) {
                        $query->select('jumlah', 'tgl_expired', 'id_satuan', 'id_barang')
                            ->with(['satuan:id,nama', 'barang:id,id_satuan_utama', 'barang.satuan_utama:id,nama']);
                    }]);
            })
            ->search($this->search,
                ['no_faktur', 'total_biaya'],
                ['supplier' => ['nama'], 'user' => ['name']])
            ->filter($this->filters)
            ->orderBy($this->sortBy, $this->sortDir)->paginate($this->perPage)
        ;
    }

    public function getDataRekapInfo()
    {
        return Faktur::query()
            ->when($this->id_barang, function ($query) {
                $query->whereHas('detail_faktur_barang', function ($query) {
                    $query->where('id_barang', $this->id_barang);
                });
            })
            ->selectRaw('
                    SUM(CASE WHEN status = "BELUM LUNAS" THEN total_biaya ELSE 0 END) AS total_belum_lunas,
                    SUM(CASE WHEN status = "LUNAS" THEN total_biaya ELSE 0 END) AS total_lunas,
                    COUNT(*) AS total_faktur
                ')
            ->filter($this->filters)
            ->first();
    }

    public function render()
    {
        $data  = $this->getFaktur();
        $rekap = $this->getDataRekapInfo();
        return view('livewire.faktur.faktur-list', ['fakturs' => $data, 'rekap' => $rekap]);
    }

    #[Computed(persist: true, seconds: 3600, cache: true)]
    public function suppliers()
    {
        return Supplier::select('id', 'nama')->get();
    }
}