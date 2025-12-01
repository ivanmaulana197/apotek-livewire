<?php
namespace App\Livewire\DataMaster\User;

use App\Models\Spatie\Role;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

class UserForm extends Component
{
    public $isEdit  = false;
    public $isModal = false;
    public User $user;

    public $name, $email, $password, $password_confirmation, $roles_selected;

    public function save()
    {
        if (Auth::user()->can('user.create')) {
            $validate = $this->validate();
            $user     = User::create($validate);
            $user->syncRoles($this->roles_selected);
            if ($this->isModal) {
                $this->dispatch('toggle-modal-user', userModal: false);
            }
            $this->dispatch('user-created');
            $this->resetForm();
            $this->dispatch('alert', status: 'success', message: 'Berhasil Menambahkan User');
        } else {
            $this->dispatch('alert', status: 'error', message: 'Anda tidak memiliki izin untuk menambahkan user');
        }
    }

    #[On('edit-user')]
    public function editFormUser(User $user)
    {
        if (Auth::user()->can('user.edit')) {
            $user->load('roles');
            $this->isEdit         = true;
            $this->user           = $user;
            $this->name           = $user->name;
            $this->email          = $user->email;
            $this->roles_selected = $user->roles->pluck('id')->toArray();
        } else {
            $this->dispatch('alert', status: 'error', message: 'Anda tidak memiliki izin untuk mengupdate user');
        }
    }

    public function update()
    {
        if (Auth::user()->can('user.edit')) {
            try {
                $validate = $this->validate();
                DB::BeginTransaction();
                $user = tap($this->user)->update($validate);
                $user->syncRoles($this->roles_selected);
                DB::commit();
                $this->dispatch('alert', status: 'info', message: 'Berhasil Mengupdate User');
                $this->resetForm();
                $this->dispatch('user-created');
            } catch (\Exception $e) {
                DB::rollBack(); // Batalkan transaksi jika terjadi error
                $this->dispatch('alert', status: 'error', message: 'Gagal Mengupdate User', subtitle: $e->getMessage());
                throw $e;
            }
        } else {
            $this->dispatch('alert', status: 'error', message: 'Anda tidak memiliki izin untuk mengupdate user');
        }
    }

    public function render()
    {
        return view('livewire.data-master.user.user-form');
    }

    protected function resetForm()
    {
        $this->reset(['isEdit', 'user', 'name', 'email', 'password', 'roles_selected', 'password_confirmation']);
    }

    protected function rules()
    {
        $rule = [
            'name'             => 'required|string|max:255',
            'email'            => ['required', $this->isEdit ? Rule::unique('users', 'email')->ignore($this->user->id) : 'unique:users,email'],
            'roles_selected'   => 'required|array|min:1',
            'roles_selected.*' => 'required|exists:roles,id',
        ];
        if (! $this->isEdit) {
            $rule['password']              = 'required|string|min:8|confirmed';
            $rule['password_confirmation'] = 'required';
        }
        return $rule;
    }

    #[Computed(persist: true, seconds: 3600, cache: true)]
    public function roles()
    {
        return Role::select('id', 'name')->get();
    }
}
