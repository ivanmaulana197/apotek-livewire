<?php
namespace App\Livewire\StokOpname;

use App\Helpers\FormatHelper;
use App\Models\Barang\Barang;
use App\Models\DataMaster\Kategori;
use App\Models\DataMaster\Rak;
use App\Traits\CustomDataTable;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class StokOpnameList extends Component
{
    use WithPagination, CustomDataTable;
    public $stok_real = [];
    public $total_kerugian;
    public $filters = [
        'id_kategories' => [],
        'id_raks'       => [],
    ];

    public function mount()
    {
        $this->sortBy         = 'barang.nama';
        $this->defaultSortBy  = 'barang.nama';
        $this->defaultSortDir = 'asc';
        $this->sortDir        = 'asc';
        $this->total_kerugian = Barang::selectRaw('SUM((COALESCE(stok_real, 0) - stok) * harga_beli_default) as total_kerugian')->value('total_kerugian');
    }

    public function simpanStokReal($id_barang)
    {
        $barang = Barang::findOrFail($id_barang);
        if ($barang && isset($this->stok_real[$id_barang])) {
            $barang->stok_real = FormatHelper::formatStringToNumber($this->stok_real[$id_barang]);
            $barang->save();
            $this->updateStokReal();
            return $this->dispatch('alert', status: 'success', message: 'Berhasil Menyimpan Stok Real');
        }
        return $this->dispatch('alert', status: 'error', message: 'Gagal Menyimpan Stok Real');
    }

    public function getDataBarang()
    {
        return Barang::with(['kategories:id,nama', 'raks:id,nama', 'opsi_satuan:id,nama'])
            ->select([
                'barang.id',
                'barang.kode',
                'barang.nama',
                'barang.stok',
                'barang.harga_jual_utama',
                'barang.harga_beli_default',
                'barang.id_satuan_utama',
                'barang.stok_real',
                'satuan_utama.nama as nama_satuan_utama',
            ])
            ->leftjoin('kategori_barang', 'kategori_barang.id_barang', '=', 'barang.id')
            ->leftjoin('kategori', 'kategori.id', '=', 'kategori_barang.id_kategori')
            ->leftjoin('rak_barang', 'rak_barang.id_barang', '=', 'barang.id')
            ->leftjoin('rak', 'rak.id', '=', 'rak_barang.id_rak')
            ->leftjoin('satuan as satuan_utama', 'satuan_utama.id', '=', 'barang.id_satuan_utama')
        // ->leftjoin('satuan_barang', 'satuan_barang.id_barang', '=', 'barang.id')
        // ->leftjoin('satuan as opsi_satuan', 'opsi_satuan.id', '=', 'satuan_barang.id_satuan')
            ->search($this->search, ['kode', 'barang.nama', 'stok', 'harga_jual_utama', 'harga_beli_default'], ['kategories' => ['kategori.nama'], 'raks' => ['rak.nama'], 'satuan_utama' => ['satuan_utama.nama']])
            ->filter($this->filters)
            ->orderBy($this->sortBy, $this->sortDir)
            ->groupBy('barang.id')
            ->paginate($this->perPage);
    }
    #[On('update-stok-real')]
    public function updateStokReal()
    {
        $this->total_kerugian = Barang::selectRaw('SUM((COALESCE(stok_real, 0) - stok) * harga_beli_default) as total_kerugian')->value('total_kerugian');
    }

    public function render()
    {
        $data = $this->getDataBarang();
        foreach ($data as $item) {
            // $this->stok_real[$item->id] = FormatHelper::formatNumberToString($item->stok_real);
            if (! isset($this->stok_real[$item->id])) {
                $this->stok_real[$item->id] = FormatHelper::formatNumberToString($item->stok_real);
            }
        }

        return view('livewire.stok-opname.stok-opname-list', [
            'barangs' => $data,
            // 'total_kerugian' => $total_kerugian,
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