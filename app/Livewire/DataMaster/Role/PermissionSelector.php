<?php
namespace App\Livewire\DataMaster\Role;

use App\Models\Spatie\Permission;
use App\Models\Spatie\Role;
use Livewire\Component;

class PermissionSelector extends Component
{
    public $roleId;
    public $selected = [];

    public function mount($id = null)
    {
        if ($id) {
            $role           = Role::find($id);
            $this->selected = $role->permissions->pluck('name')->toArray();
        }
    }

    public function toggle($permission)
    {
        $role = Role::find($this->roleId);

        if (in_array($permission, $this->selected)) {
            $role->revokePermissionTo($permission);
            $this->selected = array_diff($this->selected, [$permission]);
        } else {
            $role->givePermissionTo($permission);
            $this->selected[] = $permission;
        }
    }
    public function render()
    {
        $permissions = Permission::orderBy('name')->get();

        // Grouping berdasarkan prefix sebelum titik:
        $grouped = [];

        foreach ($permissions as $p) {
            $group             = explode('.', $p->name)[0] ?? 'other';
            $grouped[$group][] = $p;
        }
        // dd($grouped);

        return view('livewire.data-master.role.permission-selector', [
            'groupedPermissions' => $grouped,
        ]);
    }
}
