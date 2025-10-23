<?php

namespace App\Livewire\Faktur;

use App\Models\Barang\Barang;
use App\Traits\CustomDataTable;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;

class SelectBarang extends Component
{
     use WithPagination, WithoutUrlPagination, CustomDataTable;
    public $id_barang;
    public $index;


    #[On('toggle-modal-select_barang')]
    public function toggleModalSelectBarang($select_barangModal, $id_barang = null, $index = null){
        if($index === null && $id_barang == null){return;}

        $this->index = $index;
        $this->id_barang = $id_barang;
    }

    public function selected($id_barang){
        try{
            $barang = Barang::findOrFail($id_barang);
            $this->id_barang = $barang->id;
            
            $this->dispatch('toggle-modal-select_barang', select_barangModal: false);
            $this->dispatch('select-barang-faktur', id_barang: $this->id_barang, index: $this->index);
            // dd($this->index);
        }catch(\Exception $e){
            $this->dispatch('select-barang-faktur', id_barang: null, index:$this->index);
        }
    }

    #[On('select-barang')]
    public function selectBarang($id_barang){
        try{
            $this->id_barang = Barang::findOrFail($id_barang)->id;
        }catch(\Exception $e){
            $this->id_barang = null;
        }
    }


    public function getBarang(){
        return Barang::with('opsi_harga', 'opsi_harga.satuan')
            ->search($this->search, ['nama', 'kode'])
            ->orderBy('nama')
            ->paginate($this->perPage);
    }

    public function render()
    {
        // dd($this->index);
        return view('livewire.faktur.select-barang', [
            'barangs' => $this->getBarang(),
        ]);
    }

    public function highlight($text)
    {
        $tokens = explode(' ', $this->search);
        $pattern = '/' . implode('|', array_map('preg_quote', $tokens)) . '/i';

        return preg_replace($pattern, '<span class="highlight">$0</span>', $text);
    }
}
