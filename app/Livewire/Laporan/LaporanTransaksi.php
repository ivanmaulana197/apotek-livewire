<?php
namespace App\Livewire\Laporan;

use App\Models\User;
use Livewire\Attributes\Computed;
use Livewire\Component;

class LaporanTransaksi extends Component
{
    public $by_user;
    public $shift_transaksi;
    public $filter_date;

    public function mount()
    {
        $this->shift_transaksi = 'SEMUA';
        $this->filter_date     = [
            'start' => now()->startOfMonth()->format('Y-m-d'),
            'end'   => now()->endOfMonth()->format('Y-m-d'),
        ];
        $this->by_user = 'SEMUA';
    }

    #[Computed(persist: true, seconds: 3600, cache: true)]
    public function users()
    {
        return User::select('id', 'name')->get();
    }

    public function render()
    {
        return view('livewire.laporan.laporan-transaksi');
    }
}