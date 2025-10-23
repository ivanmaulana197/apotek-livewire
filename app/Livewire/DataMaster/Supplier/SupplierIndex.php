<?php

namespace App\Livewire\DataMaster\Supplier;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class SupplierIndex extends Component
{
    public function mount(){
        // if(!Auth::user()->can('supplier.index')){
        //     abort(403, 'Tidak punya akses.');
        // }
    }

    public function render()
    {
        return view('livewire.data-master.supplier.supplier-index');
    }
}
