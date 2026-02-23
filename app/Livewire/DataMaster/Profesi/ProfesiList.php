<?php
namespace App\Livewire\DataMaster\Profesi;

use App\Models\DataMaster\Profesi;
use App\Models\Promo\PromoProfesiMember;
use App\Traits\CustomDataTable;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class ProfesiList extends Component
{
    use WithPagination, CustomDataTable;

    public function confirmationDelete($id)
    {
        $this->dispatch('alert-confirm', id: $id, method: 'delete-profesi');
    }
    #[On('delete-profesi')]
    public function delete(Profesi $id)
    {
        if (! Auth::user()->can('profesi.delete')) {
            return $this->dispatch('alert', message: 'Kamu Tidak Memiliki Akses', status: 'error');
        }

        //
        if (PromoProfesiMember::where('id_profesi', $id->id)->exists()) {
            return $this->dispatch('alert', message: 'Profesi Ini Sedang Digunakan Pada Promo', status: 'error');
        }

        $id->delete();
        $this->dispatch('alert', message: 'Berhasil Menghapus Profesi', status: 'success');

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
