<?php
namespace App\Livewire\DataMaster\Role;

use App\Models\Spatie\Role;
use App\Traits\CustomDataTable;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class RoleList extends Component
{
    use WithPagination, CustomDataTable;

    #[On('delete-role')]
    public function deleteRole($id)
    {
        if (! Auth::user()->can('role.delete')) {
            $this->dispatch('alert', status: 'error', message: 'Anda tidak memiliki izin untuk menghapus Role');
            return;
        }
        try {
            $role = Role::findOrFail($id);
            if ($role->name == 'Super Admin') {
                $this->dispatch('alert', status: 'error', message: 'Role Super Admin tidak dapat dihapus');
                return;
            }
            $role->delete();
            $this->dispatch('alert', status: 'success', message: 'Role Berhasil Dihapus');
        } catch (\Exception $e) {
            $this->dispatch('alert', status: 'error', message: $e->getMessage());
            throw $e;
        }
    }

    public function getDataRole()
    {
        return Role::query()
            ->search($this->search, ['name'])
            ->orderBy($this->sortBy, $this->sortDir)
            ->paginate($this->perPage);

    }

    public function render()
    {
        $data = $this->getDataRole();
        return view('livewire.data-master.role.role-list', ['roles' => $data]);
    }
}
