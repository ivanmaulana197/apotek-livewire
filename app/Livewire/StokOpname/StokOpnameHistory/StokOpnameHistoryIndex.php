<?php
namespace App\Livewire\StokOpname\StokOpnameHistory;

use App\Models\StokOpnameHistory;
use App\Traits\CustomDataTable;
use Livewire\Component;
use Livewire\WithPagination;

class StokOpnameHistoryIndex extends Component
{
    use WithPagination, CustomDataTable;
    public $bulan, $tahun;

    public function mount()
    {
        $this->sortBy = $this->defaultSortBy = 'created_at';
    }

    public function triggerSearch()
    {
        if (empty($this->tahun)) {
            $this->search = false;
        } else {
            $this->search = true;
        }
    }

    public function query()
    {
        return StokOpnameHistory::query()->selectRaw('SUM(id) as jumlah_barang, created_at, SUM(kerugian) as kerugian');
    }

    public function render()
    {
        $query = $this->query();

        if ($this->search == true) {
            $query = $query->when($this->tahun, fn($q) => $q->whereYear('created_at', $this->tahun))->when($this->bulan, fn($q) => $q->whereMonth('created_at', $this->bulan));
        }
        $stokOpnameHistories = $query->groupBy('created_at')->orderBy($this->sortBy, $this->sortDir)->paginate($this->perPage);
        // dd($stokOpnameHistories);
        return view('livewire.stok-opname.stok-opname-history.stok-opname-history-index', compact('stokOpnameHistories'));
    }
}
