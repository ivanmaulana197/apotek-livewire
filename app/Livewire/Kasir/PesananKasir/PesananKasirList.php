<?php
namespace App\Livewire\Kasir\PesananKasir;

use App\Models\Kasir\PesananKasir;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;

class PesananKasirList extends Component
{
    use WithPagination, WithoutUrlPagination;
    public $pesanan_kasir_selected;

    #[On('pesanan_kasir_selected')]
    public function pesananKasirSelected($id)
    {
        $this->pesanan_kasir_selected = $id;
    }

    #[On('delete-pesanan-kasir')]
    public function delete(PesananKasir $id, $message = 'Berhasil Menghapus Pesanan Kasir')
    {
        if (Auth::check() && (Auth::user()->hasRole('admin') || $id->id_user == Auth::user()->id)) {
            $id->barang_pesanan_kasir()->delete();
            $id->delete();
            if ($this->pesanan_kasir_selected == $id->id) {
                session()->flash('alert', [
                    'title'   => 'Berhasil!',
                    'message' => $message,
                    'icon'    => 'success', // Bisa 'success', 'error', 'warning', dll.
                ]);
                return $this->redirect(route('kasir'), true);
            }
            $this->dispatch('alert', message: $message, status: 'success');
        } else {
            $this->dispatch('alert', message: 'Kamu Tidak Memiliki Akses', status: 'error');
        }
    }

    #[On('load-data')]
    public function loadData()
    {}

    // #[On('update_pesanan_kasir')]
    public function render()
    {
        if (Auth::check() && Auth::user()->hasRole('admin')) {
            $data = PesananKasir::query()
            // ->with(['user:id,name'])
                ->join('users', 'users.id', '=', 'pesanan_kasir.id_user')
                ->select(['pesanan_kasir.id', 'no_pesanan_kasir', 'mode_shift', 'mode_kasir', 'id_user', 'total_harga', 'pesanan_kasir.updated_at', 'users.name as nama_user'])
                ->orderByDesc('pesanan_kasir.updated_at')->paginate(5);
        } else {
            $data = PesananKasir::query()
            // ->with(['user:id,name'])
                ->join('users', 'users.id', '=', 'pesanan_kasir.id_user')
                ->select(['pesanan_kasir.id', 'no_pesanan_kasir', 'mode_shift', 'mode_kasir', 'id_user', 'total_harga', 'pesanan_kasir.updated_at', 'users.name as nama_user'])
                ->where('id_user', Auth::user()->id)->orderByDesc('pesanan_kasir.updated_at')->paginate(5);
        }
        return view('livewire.kasir.pesanan-kasir.pesanan-kasir-list', [
            'pesanan_kasirs' => $data,
        ]);
    }
}
