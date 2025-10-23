<?php
namespace App\Livewire\DataMaster\Kategori;

use App\Models\DataMaster\Kategori;
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
        $validate = $this->validate();
        $kategori = Kategori::create($validate);
        if ($this->isModal) {
            $this->dispatch('toggle-modal-kategori', kategoriModal: false);
        }
        $this->dispatch('alert', status: 'success', message: 'Berhasil Menambahkan Kategori');
        $this->resetForm();
        $this->dispatch('kategori-created', kategori: $kategori);
    }

    #[On('edit-kategori')]
    public function editFormSupplier(Kategori $kategori)
    {
        $this->isEdit   = true;
        $this->kategori = $kategori;
        $this->nama     = $kategori->nama;
    }

    public function update()
    {
        $validate = $this->validate();
        $kategori = tap($this->kategori)->update($validate);

        $this->dispatch('alert', status: 'info', message: 'Berhasil Mengupdate Kategori');
        $this->resetForm();
        $this->dispatch('kategori-created', kategori: $kategori);
    }

    #[On('close-modal-kategori')]
    public function resetForm()
    {
        $this->isEdit   = false;
        $this->kategori = new Kategori();
        $this->nama     = null;
    }
}