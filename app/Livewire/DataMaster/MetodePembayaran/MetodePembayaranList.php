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
        $this->dispatch('alert-confirm', id: $id, method: 'delete-metodePembayaran');
    }

    #[On('delete-metodePembayaran')]
    public function delete(MetodePembayaran $id)
    {
        if (! Auth::user()->can('metode-pembayaran.delete')) {
            return $this->dispatch('alert', status: 'error', message: 'Kamu Tidak Memiliki Akses');
        }

        $id->delete();
        $this->dispatch('alert', message: 'Berhasil Menghapus Metode Pembayaran');

    }

    #[On('metodePembayaran-created')]
    public function render()
    {
        if (! Auth::user()->can('metode-pembayaran.list')) {
            return $this->dispatch('alert', status: 'error', message: 'Kamu Tidak Memiliki Akses');
        }

        $data = MetodePembayaran::search($this->search, ['nama'])
            ->orderBy($this->sortBy, $this->sortDir)
            ->paginate($this->perPage);

        return view('livewire.data-master.metode-pembayaran.metode-pembayaran-list', ['metodePembayarans' => $data]);
    }
}
