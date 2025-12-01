<?php
namespace App\Livewire\Transaksi;

use App\Helpers\GeneralHelper;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;

class TransaksiIndex extends Component
{
    public $transaksi;
    #[Url( as : 'date')]
    public $filter_date;
    #[Url( as : 'shift')]
    public $shift_transaksi;

    public function mount()
    {
        $this->shift_transaksi = GeneralHelper::checkModeShift();
        $this->filter_date     = [
            'start' => now()->format('Y-m-d'),
            'end'   => now()->format('Y-m-d'),
        ];
        if (Auth::user()->can('transaksi.filter-transaksi')) {
            if (request()->has('date.start') && request()->has('date.end')) {
                $this->filter_date = [
                    'start' => request('date.start'),
                    'end'   => request('date.end'),
                ];
            }
            if (request()->has('shift')) {
                $this->shift_transaksi = request('shift');
            }
        }

    }

    #[On('update_value_shift_transaksi')]
    public function updateShiftTransaksi($value)
    {
        if (! Auth::user()->can('transaksi.filter-transaksi')) {
            return $this->dispatch('alert', status: 'error', message: 'Kamu Tidak Memiliki Akses');
        }
        $this->shift_transaksi = $value;
    }

    #[On('update_value_filter_date')]
    public function updateFilterDate($value)
    {
        if (! Auth::user()->can('transaksi.filter-transaksi')) {
            return $this->dispatch('alert', status: 'error', message: 'Kamu Tidak Memiliki Akses');
        }
        $this->filter_date = $value;
    }

    public function render()
    {
        return view('livewire.transaksi.transaksi-index');
    }
}
