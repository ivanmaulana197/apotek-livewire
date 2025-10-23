<?php

namespace App\Livewire\DataMaster\Dokter;

use App\Models\DataMaster\Dokter;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class DokterForm extends Component
{
    public $isEdit = false;
    public Dokter $dokter;
    public $nama, $spesialis, $no_hp, $alamat;
    public $isModal=false;


    protected function rules(){
        return [
            'nama' =>'required',
            'spesialis' =>'required',
            'no_hp'=>'nullable',
            'alamat'=>'nullable',
        ];
    }

     public function save(){
        $this->nama = strtoupper($this->nama);
        $validate = $this->validate();
        $validate = collect($validate)->transform(function ($val) {
            return $val === '' ? null : $val;
        }) // buang semua yang null
        ->toArray();
        $dokter = Dokter::create($validate);
        if ($this->isModal) {
            $this->dispatch('toggle-modal-dokter',dokterModal:false);
        }
        $this->dispatch('alert',status:'success',message:'Berhasil Menambahkan Dokter');
        $this->resetForm();
        $this->dispatch('dokter-created', dokter: $dokter);
    }

    #[On('edit-dokter')]
    public function editFormDokter(Dokter $dokter){
        $this->isEdit = true;
        $this->dokter = $dokter;
        $this->nama = $dokter->nama;
        $this->spesialis = $dokter->spesialis;
        $this->no_hp = $dokter->no_hp;
        $this->alamat = $dokter->alamat;
    }

    public function update(){
        $this->nama = strtoupper($this->nama);
        $validate = $this->validate();
        $validate = collect($validate)->transform(function ($val) {
            return $val === '' ? null : $val;
        }) // buang semua yang null
        ->toArray();
        $dokter = tap($this->dokter)->update($validate);

        $this->dispatch('alert',status:'info',message:'Berhasil Mengupdate Dokter');
        $this->resetForm();
        $this->dispatch('dokter-created',  dokter: $dokter->id);
    }

    #[On('reset-form-dokter')]
    public function resetForm(){
        $this->isEdit = false;
        $this->dokter = new Dokter();
        $this->nama = null;
        $this->spesialis =null;
        $this->no_hp =null;
        $this->alamat = null;
    }


    public function render()
    {
        return view('livewire.data-master.dokter.dokter-form');
    }
}
