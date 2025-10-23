<?php

namespace App\Livewire\DataMaster\Satuan;

use Livewire\Component;
use Illuminate\Validation\Rule;
use App\Models\DataMaster\Satuan;
use Livewire\Attributes\On;

class SatuanForm extends Component
{
    public $isEdit = false;
    public $isModal = false;
    public $nama;
    public Satuan $satuan;

    protected function rules()
    {
        return [
            'nama' => ['required', $this->isEdit ? Rule::unique('satuan', 'nama')->ignore($this->satuan->id) : 'unique:satuan,nama'],
        ];
    }

    public function save()
    {
        $validate = $this->validate();
        $satuan = Satuan::create($validate);
        if ($this->isModal) {
            $this->dispatch('toggle-modal-satuan', satuanModal: false);
        }
        $this->dispatch('alert', status: 'success', message: 'Berhasil Menambahkan Satuan');
        $this->resetForm();
        $this->dispatch('satuan-created', satuan: $satuan);
    }

    #[On('edit-satuan')]
    public function editFormSupplier(Satuan $satuan)
    {
        $this->isEdit = true;
        $this->satuan = $satuan;
        $this->nama = $satuan->nama;
    }

    public function update()
    {
        $validate = $this->validate();
        $satuan = tap($this->satuan)->update($validate);

        $this->dispatch('alert', status: 'info', message: 'Berhasil Mengupdate Satuan');
        $this->resetForm();
        $this->dispatch('satuan-created', satuan: $satuan);
    }


    #[On('close-modal-satuan')]
    public function resetForm()
    {
        $this->isEdit = false;
        $this->satuan = new Satuan();
        $this->nama = null;
    }
}