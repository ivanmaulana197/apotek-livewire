<?php

namespace App\Livewire\DataMaster\Satuan;

use Livewire\Component;
use App\Models\DataMaster\Satuan;
use App\Traits\CustomDataTable;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\WithPagination;

class SatuanList extends Component
{
    use WithPagination, CustomDataTable;

    public function confirmationDelete($id)
    {
        $this->dispatch('alert-confirm', id: $id,method: 'delete-satuan');
    }
    #[On('delete-satuan')]
    public function delete(Satuan $id): void
    {

        if (Auth::user()->hasRole('admin')) { //nanti update yang mempunyai permission
            $id->delete();
            $this->dispatch('alert', message: 'Berhasil Menghapus Satuan');
        }else{
            $this->dispatch('alert', message: 'Kamu Tidak Memiliki Akses', status: 'error');
        }
    }



    #[On('satuan-created')]
    public function render()
    {
        $data = Satuan::search($this->search,['nama'])
        ->orderBy($this->sortBy, $this->sortDir)
        ->paginate($this->perPage);

        return view('livewire.data-master.satuan.satuan-list',[
            'satuans' => $data
        ]);
    }
}
