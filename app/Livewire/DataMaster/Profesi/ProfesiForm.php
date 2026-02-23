<?php
namespace App\Livewire\DataMaster\Profesi;

use App\Models\DataMaster\Profesi;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Attributes\On;
use Livewire\Component;

class ProfesiForm extends Component
{
    public $isEdit  = false;
    public $isModal = false;

    public $nama;
    public Profesi $profesi;

    protected function rules()
    {
        return [
            'nama' => ['required', $this->isEdit ? Rule::unique('profesi', 'nama')->ignore($this->profesi->id) : 'unique:profesi,nama'],
        ];
    }

    public function save()
    {
        if (! Auth::user()->can('profesi.create')) {
            return $this->dispatch('alert', status: 'error', message: 'Anda Tidak Memiliki Akses');
        }
        $validate = $this->validate();
        $profesi  = Profesi::create($validate);
        if ($this->isModal) {
            $this->dispatch('toggle-modal-profesi', profesiModal: false);
        }
        $this->dispatch('alert', status: 'success', message: 'Berhasil Menambahkan Profesi');
        $this->resetForm();
        $this->dispatch('profesi-created', profesi: $profesi);
    }

    #[On('edit-profesi')]
    public function editFormSupplier(Profesi $profesi)
    {
        if (! Auth::user()->can('profesi.edit')) {
            return $this->dispatch('alert', status: 'error', message: 'Anda Tidak Memiliki Akses');
        }
        $this->isEdit  = true;
        $this->profesi = $profesi;
        $this->nama    = $profesi->nama;
    }

    public function update()
    {
        if (! Auth::user()->can('profesi.edit')) {
            return $this->dispatch('alert', status: 'error', message: 'Anda Tidak Memiliki Akses');
        }
        $validate = $this->validate();
        $profesi  = tap($this->profesi)->update($validate);

        $this->dispatch('alert', status: 'info', message: 'Berhasil Mengupdate Profesi');
        $this->resetForm();
        $this->dispatch('profesi-created', profesi: $profesi);
    }

    #[On('close-modal-profesi')]
    public function resetForm()
    {
        $this->isEdit  = false;
        $this->profesi = new Profesi();
        $this->nama    = null;
    }
    public function render()
    {
        return view('livewire.data-master.profesi.profesi-form');
    }
}