<?php
namespace App\Livewire\DataMaster\User;

use App\Models\User;
use App\Traits\CustomDataTable;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class UserList extends Component
{
    use WithPagination, CustomDataTable;

    #[On('delete-user')]
    public function delete(User $id): void
    {
        if (Auth::user()->can('user.delete')) { //nanti update yang mempunyai permission
            $id->delete();
            $this->dispatch('alert', message: 'Berhasil Menghapus User');
        } else {
            $this->dispatch('alert', message: 'Kamu Tidak Memiliki Akses', status: 'error');
        }
    }

    #[On('user-created')]
    public function render()
    {
        $data = User::with('roles')
            ->search($this->search, ['nama', 'email'], ['roles' => 'roles.name'])
            ->orderBy($this->sortBy, $this->sortDir)
            ->paginate($this->perPage);
        return view('livewire.data-master.user.user-list', ['users' => $data]);
    }
}
