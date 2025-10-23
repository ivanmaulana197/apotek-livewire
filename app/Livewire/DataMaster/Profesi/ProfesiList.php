<?php

namespace App\Livewire\DataMaster\Profesi;

use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\WithPagination;
use App\Traits\CustomDataTable;
use App\Models\DataMaster\Profesi;
use Illuminate\Support\Facades\Auth;

class ProfesiList extends Component
{
    use WithPagination, CustomDataTable;

    public function confirmationDelete($id)
    {
        $this->dispatch('alert-confirm', id: $id, method: 'delete-profesi');
    }
    #[On('delete-profesi')]
    public function delete(Profesi $id): void
    {
        if (Auth::user()->hasRole('admin')) {
            //nanti update yang mempunyai permission
            $id->delete();
            $this->dispatch('alert', message: 'Berhasil Menghapus Profesi');
        } else {
            $this->dispatch('alert', message: 'Kamu Tidak Memiliki Akses', status: 'error');
        }
    }

    #[On('profesi-created')]
    public function render()
    {
        $data = Profesi::search($this->search, ['nama'])
            ->orderBy($this->sortBy, $this->sortDir)
            ->paginate($this->perPage);

        return view('livewire.data-master.profesi.profesi-list', [
            'profesies' => $data,
        ]);
    }
}