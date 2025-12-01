<?php
namespace App\Livewire\DataMaster\Rak;

use App\Models\DataMaster\Rak;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Attributes\On;
use Livewire\Component;

class RakForm extends Component
{
    public $isEdit = false;

    public $nama;
    public Rak $rak;

    public $isModal = false;

    protected function rules()
    {
        return [
            'nama' => ['required', $this->isEdit ? Rule::unique('rak', 'nama')->ignore($this->rak->id) : 'unique:rak,nama'],
        ];
    }

    public function save()
    {
        if (Auth::user()->can('rak.create')) {
            $validate = $this->validate();
            $rak      = Rak::create($validate);
            if ($this->isModal) {
                $this->dispatch('toggle-modal-rak', rakModal: false);
            }
            $this->dispatch('alert', status: 'success', message: 'Berhasil Menambahkan Rak');
            $this->resetForm();
            $this->dispatch('rak-created', rak: $rak);
        } else {
            $this->dispatch('alert', status: 'error', message: 'Anda tidak memiliki izin untuk menambahkan Rak');
        }
    }

    #[On('edit-rak')]
    public function editFormRak(Rak $rak)
    {
        if (Auth::user()->can('rak.edit')) {
            $this->isEdit = true;
            $this->rak    = $rak;
            $this->nama   = $rak->nama;
        } else {
            $this->dispatch('alert', status: 'error', message: 'Anda tidak memiliki izin untuk mengedit Rak');
        }
    }

    public function update()
    {
        if (Auth::user()->can('rak.edit')) {
            $validate = $this->validate();
            $rak      = tap($this->rak)->update($validate);

            $this->dispatch('alert', status: 'info', message: 'Berhasil Mengupdate Rak');
            $this->resetForm();
            $this->dispatch('rak-created', rak: $rak);
        } else {
            $this->dispatch('alert', status: 'error', message: 'Anda tidak memiliki izin untuk mengupdate Rak');
        }
    }

    #[On('close-modal-rak')]
    public function resetForm()
    {
        $this->isEdit = false;
        $this->rak    = new Rak();
        $this->nama   = null;
    }
}
