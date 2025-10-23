<?php

namespace App\Livewire\DataMaster\Supplier;

use Livewire\Component;
use App\Models\DataMaster\Supplier;
use App\Traits\CustomDataTable;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\WithPagination;

class SupplierList extends Component
{
    use WithPagination, CustomDataTable;


    public function confirmationDelete($id)
    {
        $this->dispatch('alert-confirm', id: $id,method: 'delete-supplier');
    }

    #[On('delete-supplier')]
    public function delete(Supplier $id): void
    {
        if (Auth::user()->hasRole('admin')) { //nanti update yang mempunyai permission
            $id->delete();
            $this->dispatch('alert', message: 'Berhasil Menghapus Supplier');
        }else{
            $this->dispatch('alert', message: 'Kamu Tidak Memiliki Akses', status: 'error');
        }
    }


    #[On('supplier-created')]
    public function render()
    {
        $data = Supplier::search($this->search,['nama','alamat', 'asal', 'no_hp'])
            ->orderBy($this->sortBy, $this->sortDir)
            ->paginate($this->perPage);

        return view('livewire.data-master.supplier.supplier-list',['suppliers'=>$data]);
    }
}