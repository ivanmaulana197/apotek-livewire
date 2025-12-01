<?php
namespace App\Livewire\DataMaster\Rak;

use App\Models\DataMaster\Rak;
use App\Traits\CustomDataTable;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class RakList extends Component
{
    use WithPagination, CustomDataTable;

    public function confirmationDelete($id)
    {
        $this->dispatch('alert-confirm', id: $id, method: 'delete-rak');
    }

    #[On('delete-rak')]
    public function delete(Rak $id): void
    {
        if (Auth::user()->can('rak.delete')) { //nanti update yang mempunyai permission
            $id->delete();
            $this->dispatch('alert', message: 'Berhasil Menghapus Rak');
        } else {
            $this->dispatch('alert', message: 'Kamu Tidak Memiliki Akses', status: 'error');
        }
    }

    #[On('rak-created')]
    public function render()
    {
        $data = Rak::search($this->search, ['nama'])
            ->orderBy($this->sortBy, $this->sortDir)
            ->paginate($this->perPage);
        return view('livewire.data-master.rak.rak-list', ['raks' => $data]);
    }
}
