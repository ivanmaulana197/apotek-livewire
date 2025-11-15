<?php
namespace App\Livewire\Barang\BarangDetail;

use Livewire\Component;

class BarangDetailExpired extends Component
{
    public $id;
    public function render()
    {
        return view('livewire.barang.barang-detail.barang-detail-expired');
    }
}