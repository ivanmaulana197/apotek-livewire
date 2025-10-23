<?php
namespace App\Livewire\Kasir\Components;

use App\Models\Kasir\PesananKasir as KasirPesananKasir;
use Livewire\Component;

class SelectModeShift extends Component
{
    public $id_pesanan_kasir, $mode_shift;

    public function updatedModeShift($value)
    {
        try {
            $this->validate(['mode_shift' => 'required|in:PAGI,MALAM']);
            $pesanan_kasir             = KasirPesananKasir::findOrFail($this->id_pesanan_kasir);
            $pesanan_kasir->mode_shift = $value;
            $pesanan_kasir->save();
            $this->dispatch('update_pesanan_kasir');
            $this->dispatch('alert', message: 'Berhasil Mengubah Mode Shift', status: 'success');
        } catch (\Exception $e) {
            $this->dispatch('alert', message: $e->getMessage(), status: 'error');
            throw $e;
        }

    }
    public function render()
    {
        return view('livewire.kasir.components.select-mode-shift');
    }
}
