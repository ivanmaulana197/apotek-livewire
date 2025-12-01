<?php
namespace App\Livewire\DataMaster\Kategori;

use App\Models\DataMaster\Kategori;
use App\Traits\CustomDataTable;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class KategoriList extends Component
{
    use WithPagination, CustomDataTable;

    public function confirmationDelete($id)
    {
        $this->dispatch('alert-confirm', id: $id, method: 'delete-kategori');
    }
    #[On('delete-kategori')]
    public function delete(Kategori $id): void
    {
        if (Auth::user()->can('kategori.delete')) {
            //nanti update yang mempunyai permission
            $id->delete();
            $this->dispatch('alert', message: 'Berhasil Menghapus Kategori');
        } else {
            $this->dispatch('alert', message: 'Kamu Tidak Memiliki Akses', status: 'error');
        }
    }

    #[On('kategori-created')]
    public function render()
    {
        $data = Kategori::search($this->search, ['nama'])
            ->orderBy($this->sortBy, $this->sortDir)
            ->paginate($this->perPage);

        return view('livewire.data-master.kategori.kategori-list', [
            'kategories' => $data,
        ]);
    }
}
