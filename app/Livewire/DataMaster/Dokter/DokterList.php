<?php
namespace App\Livewire\DataMaster\Dokter;

use App\Models\DataMaster\Dokter;
use App\Traits\CustomDataTable;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class DokterList extends Component
{
    use WithPagination, CustomDataTable;

    public function confirmationDelete($id)
    {
        $this->dispatch('alert-confirm', id: $id, method: 'delete-dokter');
    }

    #[On('delete-dokter')]
    public function delete(Dokter $id)
    {
        if (! Auth::user()->can('dokter.delete')) {
            return $this->dispatch('alert', status: 'error', message: 'Anda tidak memiliki izin untuk menghapus dokter');
        }

        $id->delete();
        $this->dispatch('alert', message: 'Berhasil Menghapus Dokter');

    }

    #[On('dokter-created')]
    public function render()
    {
        $data = Dokter::search($this->search, ['nama', 'spesialis', 'alamat'])
            ->orderBy($this->sortBy, $this->sortDir)
            ->paginate($this->perPage);

        return view('livewire.data-master.dokter.dokter-list', ['dokters' => $data]);
    }

}
