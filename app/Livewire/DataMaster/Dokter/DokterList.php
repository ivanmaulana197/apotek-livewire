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
    public function delete(Dokter $id): void
    {
        if (Auth::user()->hasRole('admin')) { //nanti update yang mempunyai permission
            $id->delete();
            $this->dispatch('alert', message: 'Berhasil Menghapus Dokter');
        } else {
            $this->dispatch('alert', message: 'Kamu Tidak Memiliki Akses', status: 'error');
        }
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
