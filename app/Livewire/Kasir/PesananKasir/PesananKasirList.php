<?php
namespace App\Livewire\Kasir\PesananKasir;

use App\Models\Kasir\PesananKasir;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class PesananKasirList extends Component
{
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

    #[On('update_pesanan_kasir')]
    public function render()
    {
        if (Auth::check() && Auth::user()->hasRole('admin')) {
            $data = PesananKasir::with(['user:id,name'])
                ->select(['id', 'no_pesanan_kasir', 'mode_shift', 'mode_kasir', 'id_user', 'total_harga', 'updated_at'])
                ->orderByDesc('updated_at')->paginate(10);
        } else {
            $data = PesananKasir::with(['user:id,name'])
                ->select(['id', 'no_pesanan_kasir', 'mode_shift', 'mode_kasir', 'id_user', 'total_harga', 'updated_at'])
                ->where('id_user', Auth::user()->id)->orderByDesc('updated_at')->paginate(10);
        }
        return view('livewire.kasir.pesanan-kasir.pesanan-kasir-list', [
            'pesanan_kasirs' => $data,
        ]);
    }
}
