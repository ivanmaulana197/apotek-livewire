<?php

namespace App\Livewire\DataMaster\MetodePembayaran;

use App\Models\DataMaster\MetodePembayaran;
use App\Traits\CustomDataTable;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class MetodePembayaranList extends Component
{
    use WithPagination, CustomDataTable;

    public function confirmationDelete($id)
    {
        $this->dispatch('alert-confirm', id: $id,method: 'delete-metodePembayaran');
    }

    #[On('delete-metodePembayaran')]
    public function delete(MetodePembayaran $id): void
    {
        if (Auth::user()->hasRole('admin')) { //nanti update yang mempunyai permission
            $id->delete();
            $this->dispatch('alert', message: 'Berhasil Menghapus Metode Pembayaran');
        }else{
            $this->dispatch('alert', message: 'Kamu Tidak Memiliki Akses', status: 'error');
        }
    }


    #[On('metodePembayaran-created')]
    public function render()
    {
        $data = MetodePembayaran::search($this->search,['nama'])
            ->orderBy($this->sortBy, $this->sortDir)
            ->paginate($this->perPage);

        return view('livewire.data-master.metode-pembayaran.metode-pembayaran-list',['metodePembayarans'=>$data]);
    }
}
