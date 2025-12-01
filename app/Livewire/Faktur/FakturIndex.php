<?php
namespace App\Livewire\Faktur;

use Livewire\Component;

class FakturIndex extends Component
{
    // public $hutang;

    // public function mount(){
    //     $this->hutang = Faktur::where('status','BELUM LUNAS')->sum('total_biaya');
    //     // dd($this->hutang);
    // }

    public function render()
    {
        return view('livewire.faktur.faktur-index');
    }
}
