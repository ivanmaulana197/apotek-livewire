<?php
namespace App\Livewire\DataMaster\Satuan;

use App\Models\DataMaster\Satuan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Attributes\On;
use Livewire\Component;

class SatuanForm extends Component
{
    public $isEdit  = false;
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
        if (! Auth::user()->can('satuan.create')) {
            return $this->dispatch('alert', status: 'error', message: 'Anda tidak memiliki izin untuk menambahkan Satuan');
        }
        $validate = $this->validate();
        $satuan   = Satuan::create($validate);
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
        if (! Auth::user()->can('satuan.edit')) {
            return $this->dispatch('alert', status: 'error', message: 'Anda tidak memiliki izin untuk mengedit Satuan');
        }
        $this->isEdit = true;
        $this->satuan = $satuan;
        $this->nama   = $satuan->nama;
    }

    public function update()
    {
        if (! Auth::user()->can('satuan.edit')) {
            return $this->dispatch('alert', status: 'error', message: 'Anda tidak memiliki izin untuk mengedit Satuan');
        }
        $validate = $this->validate();
        $satuan   = tap($this->satuan)->update($validate);

        $this->dispatch('alert', status: 'info', message: 'Berhasil Mengupdate Satuan');
        $this->resetForm();
        $this->dispatch('satuan-created', satuan: $satuan);
    }

    #[On('close-modal-satuan')]
    public function resetForm()
    {
        $this->isEdit = false;
        $this->satuan = new Satuan();
        $this->nama   = null;
    }
}
