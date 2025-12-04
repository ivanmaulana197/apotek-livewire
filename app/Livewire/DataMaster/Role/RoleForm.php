<?php
namespace App\Livewire\DataMaster\Role;

use App\Models\Spatie\Permission;
use App\Models\Spatie\Role;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Validate;
use Livewire\Component;

class RoleForm extends Component
{
    public $isEdit = false;
    public Role $role;
    public $name;
    public $selected_permission = [];

    public function mount($id = null)
    {
        if (! Auth::user()->can('role.create') && ! Auth::user()->can('role.edit')) {
            $this->dispatch('alert', status: 'error', message: 'Anda tidak memiliki izin untuk membuat atau mengedit Role');
            return $this->redirect(route('data-master.role'), true);
        }
        if ($id) {
            $this->isEdit = true;
            $this->edit($id);
        }
    }

    public function edit($id)
    {
        $this->role = Role::with('permissions')->findOrFail($id);
        if ($this->role->name == 'Super Admin') {
            return redirect()->back()->with([
                'alert' => [
                    'title'   => 'Tidak Diizinkan',
                    'message' => 'Anda tidak memiliki akses yang diperlukan.',
                    'icon'    => 'error', // Ikon SweetAlert ('success', 'error', 'warning', 'info')
                ],
            ]);
        }
        $this->name                = $this->role->name;
        $this->selected_permission = $this->role->permissions->pluck('name')->toArray();
    }

    public function save()
    {
        try {
            $this->validate([
                'name'                  => 'required|string|max:255|unique:roles,name' . ($this->isEdit ? ',' . $this->role->id : ''),
                'selected_permission'   => 'required|array',
                'selected_permission.*' => 'exists:permissions,name',
            ]);
            $role = Role::updateOrCreate(['id' => $this->isEdit ? $this->role->id : null], ['name' => $this->name]);
            $role->syncPermissions($this->selected_permission);
            session()->flash('alert', [
                'title'   => 'Berhasil!',
                'message' => 'Berhasil' . ($this->isEdit ? ' Mengupdate' : ' Membuat') . ' Role',
                'icon'    => 'success', // Bisa 'success', 'error', 'warning', dll.
            ]);
            return $this->redirect(route('data-master.role'), true);
        } catch (\Exception $e) {
            $this->dispatch('alert', status: 'error', message: $e->getMessage());
            throw $e;
        }
    }

    public function getPermissionList()
    {
        $permissions = Permission::orderBy('name')->get();

        $grouped = [];

        foreach ($permissions as $p) {
            // ambil prefix sebelum '-' atau '.'
            $group = preg_split('/[-.]/', $p->name)[0] ?? 'other';

            $grouped[$group][] = $p;
        }

        return $grouped;

    }

    public function render()
    {
        $permissionList = $this->getPermissionList();
        return view('livewire.data-master.role.role-form', compact('permissionList'));
    }
}
