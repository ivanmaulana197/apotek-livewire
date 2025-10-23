<?php
namespace App\Livewire\Kasir;

use App\Models\Setting;
use Livewire\Component;
use Illuminate\Support\Carbon;
use App\Helpers\GenerateHelper;
use App\Models\Kasir\PesananKasir;
use Illuminate\Support\Facades\Auth;
use App\Livewire\Kasir\Helpers\KasirHelper;
use App\Models\DataMaster\MetodePembayaran;
use Livewire\Attributes\On;

class KasirIndex extends Component
{
    use KasirHelper;

    public $pesanan_kasir, $setting;

    public function mount($id_pesanan_kasir = null)
    {
        if ($id_pesanan_kasir) {
            $this->pesanan_kasir = PesananKasir::query()
            ->findOrFail($id_pesanan_kasir);
        } else {
            $this->pesanan_kasir = PesananKasir::query()
                ->where('id_user', Auth::id())
                ->where('mode_shift', $this->checkModeShift())
                ->whereDate('created_at', Carbon::today())
                ->latest()->first();
            if (! $this->pesanan_kasir) {
                $this->pesanan_kasir = $this->createPesananKasir();
            }
        }
        $this->setting = Setting::first();
        // dd($this->pesanan_kasir->promo_pesanan_kasir);

    }

    public function createPesananKasir()
    {
        $metode_pembayaran = MetodePembayaran::where('nama', 'TUNAI')->first('id');
        return PesananKasir::create([
            'id_user'              => Auth::id(),
            'no_pesanan_kasir'     => GenerateHelper::generateNoPesananKasir(),
            'mode_kasir'           => 'UMUM',
            'mode_shift'           => $this->checkModeShift(),
            'total_harga'          => 0,
            'id_metode_pembayaran' => $metode_pembayaran->id,
        ]);
    }

    public function tambahPesananKasir(){
        $this->createPesananKasir();
        session()->flash('alert', [
            'title'   => 'Berhasil!',
            'message' => 'Berhasil Membuat Pesanan Kasir Baru',
            'icon'    => 'success', // Bisa 'success', 'error', 'warning', dll.
        ]);
        return $this->redirect(route('kasir'), true);
    }

    public function render()
    {

        // $this->setPromoTransaksiKasir($this->pesanan_kasir->total_harga, $this->pesanan_kasir->member?->id_profesi);
        return view('livewire.kasir.kasir-index');
    }
}
