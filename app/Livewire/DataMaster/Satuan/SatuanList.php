<?php
namespace App\Livewire\DataMaster\Satuan;

use App\Models\DataMaster\Satuan;
use App\Traits\CustomDataTable;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class SatuanList extends Component
{
    use WithPagination, CustomDataTable;

    public function confirmationDelete($id)
    {
        $this->dispatch('alert-confirm', id: $id, method: 'delete-satuan');
    }
    #[On('delete-satuan')]
    public function delete(Satuan $id)
    {
        if (! Auth::user()->can('satuan.delete')) {
            return $this->dispatch('alert', status: 'error', message: 'Anda tidak memiliki izin untuk menghapus Satuan');
        }

        $id->delete();
        $this->dispatch('alert', message: 'Berhasil Menghapus Satuan');

    }

    #[On('satuan-created')]
    public function render()
    {
        $data = Satuan::search($this->search, ['nama'])
            ->orderBy($this->sortBy, $this->sortDir)
            ->paginate($this->perPage);

        return view('livewire.data-master.satuan.satuan-list', [
            'satuans' => $data,
        ]);
    }
}
