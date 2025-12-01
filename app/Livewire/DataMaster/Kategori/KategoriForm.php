<?php
namespace App\Livewire\DataMaster\Kategori;

use App\Models\DataMaster\Kategori;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Attributes\On;
use Livewire\Component;

class KategoriForm extends Component
{

    public $isEdit  = false;
    public $isModal = false;

    public $nama;
    public Kategori $kategori;

    protected function rules()
    {
        return [
            'nama' => ['required', $this->isEdit ? Rule::unique('kategori', 'nama')->ignore($this->kategori->id) : 'unique:kategori,nama'],
        ];
    }

    public function save()
    {
        if (Auth::user()->can('kategori.create')) {
            $validate = $this->validate();
            $kategori = Kategori::create($validate);
            if ($this->isModal) {
                $this->dispatch('toggle-modal-kategori', kategoriModal: false);
            }
            $this->dispatch('alert', status: 'success', message: 'Berhasil Menambahkan Kategori');
            $this->resetForm();
            $this->dispatch('kategori-created', kategori: $kategori);
        } else {
            $this->dispatch('alert', status: 'error', message: 'Anda tidak memiliki izin untuk menambahkan Kategori');
        }
    }

    #[On('edit-kategori')]
    public function editFormSupplier(Kategori $kategori)
    {
        if (Auth::user()->can('kategori.edit')) {
            $this->isEdit   = true;
            $this->kategori = $kategori;
            $this->nama     = $kategori->nama;
        } else {
            $this->dispatch('alert', status: 'error', message: 'Anda tidak memiliki izin untuk mengedit Kategori');
        }
    }

    public function update()
    {
        if (Auth::user()->can('kategori.edit')) {

            $validate = $this->validate();
            $kategori = tap($this->kategori)->update($validate);

            $this->dispatch('alert', status: 'info', message: 'Berhasil Mengupdate Kategori');
            $this->resetForm();
            $this->dispatch('kategori-created', kategori: $kategori);
        } else {
            $this->dispatch('alert', status: 'error', message: 'Anda tidak memiliki izin untuk mengedit Kategori');
        }
    }

    #[On('close-modal-kategori')]
    public function resetForm()
    {
        $this->isEdit   = false;
        $this->kategori = new Kategori();
        $this->nama     = null;
    }
}
