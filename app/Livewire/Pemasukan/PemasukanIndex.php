<?php
namespace App\Livewire\Pemasukan;

use App\Helpers\GenerateHelper;
use App\Livewire\Pemasukan\Helpers\TraitBarangPesananKasir;
use App\Livewire\Pemasukan\Helpers\TraitKasirMode;
use App\Models\DataMaster\MetodePembayaran;
use App\Models\Kasir\PesananKasir;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class PemasukanIndex extends Component
{
    use PemasukanHelper, TraitKasirMode, TraitBarangPesananKasir;

    public $pesanan_kasir, $setting;
    public $persen_resep;

    // pesanan_kasir form
    public $mode_kasir, $mode_shift;

    //barang pesanan kasir form
    public $data_barang;
    public $id_barang, $jumlah, $id_satuan, $harga_jual, $diskon, $harga_satuan_utama, $sub_total;
    public $id_promo_barang;

    public $list_total_jumlah_dalam_satuan_utama;

    public function mount($id_pesanan_kasir = null)
    {
        if ($id_pesanan_kasir) {
            $this->pesanan_kasir = PesananKasir::query()
                ->findOrFail($id_pesanan_kasir);
        } else {
            $this->pesanan_kasir = PesananKasir::query()
                ->where('id_user', Auth::id())
            // ->where('mode_shift', $this->checkModeShift())
                ->whereDate('created_at', Carbon::today())
                ->latest()->first();
            if (! $this->pesanan_kasir) {
                $this->pesanan_kasir = $this->createPesananKasir();
            }
        }
        $this->loadRelation();
        $this->setFormPesananKasir();
        // dd($this->pesanan_kasir->toArray());
        $this->setting                              = Setting::select(['id', 'persentase_jual_resep'])->first();
        $this->persen_resep                         = $this->setting->persentase_jual_resep;
        $this->list_total_jumlah_dalam_satuan_utama = $this->pesanan_kasir
            ->barang_pesanan_kasir
            ->groupBy('id_barang')
            ->map(fn($items) => $items->sum('jumlah_dalam_satuan_utama'))->toArray();
        // dd($this->list_total_jumlah_dalam_satuan_utama);
    }

    public function loadRelation()
    {
        $this->pesanan_kasir->loadMissing([
            'promo_pesanan_kasir.promo_barang',
            'promo_pesanan_kasir.promo_transaksi',

            'barang_pesanan_kasir.satuan:id,nama',
            'barang_pesanan_kasir.barang.opsi_satuan',
            'barang_pesanan_kasir.barang.satuan_utama',
            'barang_pesanan_kasir.barang.opsi_harga',
            'barang_pesanan_kasir.barang.opsi_harga.barang:id,id_satuan_utama',
            'barang_pesanan_kasir.barang.opsi_harga.barang.opsi_satuan:id',
        ]);
    }

    public function render()
    {
        $this->loadRelation();
        $this->loadRelationDataBarang();
        return view('livewire.pemasukan.pemasukan-index');
    }

    public function setFormPesananKasir()
    {
        $this->mode_kasir = $this->pesanan_kasir->mode_kasir;
        $this->mode_shift = $this->pesanan_kasir->mode_shift;
    }

    public function tambahPesananKasir()
    {
        $this->createPesananKasir();
        session()->flash('alert', [
            'title'   => 'Berhasil!',
            'message' => 'Berhasil Membuat Pesanan Kasir Baru',
            'icon'    => 'success', // Bisa 'success', 'error', 'warning', dll.
        ]);
        return $this->redirect(route('pemasukan'), true);
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
}
