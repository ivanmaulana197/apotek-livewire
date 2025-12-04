<?php
namespace App\Livewire\KasirCadangan;

use App\Helpers\GenerateHelper;
use App\Livewire\KasirCadangan\Helpers\KasirHelper;
use App\Livewire\KasirCadangan\Helpers\KasirModeHelper;
use App\Models\DataMaster\MetodePembayaran;
use App\Models\Kasir\PesananKasir;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class KasirIndex extends Component
{
    use KasirHelper, KasirModeHelper;
    public $pesanan_kasir, $setting;
    public $persen_resep;

    // pesanan_kasir form
    public $mode_kasir, $mode_shift;

    public $list_total_jumlah_dalam_satuan_utama;

    public function mount($id_pesanan_kasir = null)
    {
        if ($id_pesanan_kasir) {
            $this->pesanan_kasir = PesananKasir::query()
                ->findOrFail($id_pesanan_kasir);
            if (! Auth::user()->can('kasir-show-all') && ! (Auth::user()->can('kasir-show-own') && $this->pesanan_kasir->id_user == Auth::id())) {
                return redirect()->back()->with([
                    'alert' => [
                        'title'   => 'Tidak Diizinkan',
                        'message' => 'Anda tidak memiliki akses yang diperlukan.',
                        'icon'    => 'error', // Ikon SweetAlert ('success', 'error', 'warning', 'info')
                    ],
                ]);
            }
        } else {
            $this->pesanan_kasir = PesananKasir::query()
                ->where('id_user', Auth::id())
                ->whereDate('created_at', Carbon::today())
                ->latest()->first();
            if (! $this->pesanan_kasir) {
                $this->pesanan_kasir = $this->createPesananKasir();
            }
        }
        // $this->loadRelation();
        $this->pesanan_kasir->loadMissing([
            'barang_pesanan_kasir' => function ($query) {
                $query->with([
                    'barang.satuan_utama',
                    'barang.opsi_satuan',
                    'satuan:id,nama',
                    'barang.opsi_harga.satuan:id,nama',
                ]);
            },
            'promo_pesanan_kasir.promo_barang',
        ]);
        $this->setFormPesananKasir();
        $this->setting      = Setting::select(['id', 'persentase_jual_resep', 'pembulatan_harga_barang', 'pembulatan_resep', 'pembulatan_total'])->first();
        $this->persen_resep = $this->setting->persentase_jual_resep;
        $this->updateValueAllBarangPesananKasir();
        $this->list_total_jumlah_dalam_satuan_utama = $this->pesanan_kasir->barang_pesanan_kasir->groupBy('id_barang')
            ->map(fn($items) => $items->sum('jumlah_dalam_satuan_utama'))->toArray();
        // dd($this->pesanan_kasir);
    }

    public function updateValueAllBarangPesananKasir()
    {
        $barang_pesanan_stok_error = [];
        $status                    = 'success';
        // $this->pesanan_kasir->barang_pesanan_kasir->each(function ($barang_pesanan, $index) use (&$barang_pesanan_stok_error, &$status) {
        foreach ($this->pesanan_kasir->barang_pesanan_kasir as $index => $barang_pesanan) {
            $total_jumlah = $this->totalJumlahBarangDalamSatuanUtama($barang_pesanan->barang);
            if ($barang_pesanan->barang->stok < $total_jumlah) {
                // $this->dispatch('alert-info', message: 'Stok barang <b class="!font-extrabold">[' . $barang_pesanan->barang->nama . '</b>] tidak mencukupi.', status: 'warning', subtitle: 'silahkan perbarui stok barang atau perbarui jumlah pesanan');
                $barang_pesanan_stok_error[] = [
                    'id_barang_pesanan' => $barang_pesanan->id,
                    'nama_barang'       => $barang_pesanan->barang->nama,
                ];
            }
            [$harga_jual, $harga_satuan_utama] = $this->getHargaJual($barang_pesanan->barang, $total_jumlah, $barang_pesanan->id_satuan);
            if ($this->mode_kasir == 'RESEP') {
                $harga_jual         = $this->hargaJualResep($harga_jual, $this->persen_resep);
                $harga_satuan_utama = $this->hargaJualResep($harga_satuan_utama, $this->persen_resep);
            }
            $harga_new = ceil($this->calculateJumlahDalamSatuanUtama($barang_pesanan->barang, $harga_satuan_utama, $barang_pesanan->id_satuan));
            if ($harga_new != $barang_pesanan->harga || $harga_satuan_utama != $barang_pesanan->harga_satuan_utama) {
                $barang_pesanan->update([
                    'harga'              => $harga_new,
                    'harga_satuan_utama' => $harga_satuan_utama,
                    'sub_total'          => $barang_pesanan->jumlah * $harga_new,
                ]);
            }
            $next = $this->pesanan_kasir->barang_pesanan_kasir->get($index + 1);
            if ($next?->id_barang == $barang_pesanan->id_barang) {
                continue;
            }
            $total_harga_by_barang = $this->totalHargaBarang($barang_pesanan->id_barang);
            $id_promo_selected     = $this->getIdPromoBarangSelected($barang_pesanan->id_barang);
            $promo                 = $this->applyPromoBarang($barang_pesanan->id_barang, $total_jumlah, $total_harga_by_barang, $id_promo_selected);
            if ($promo && $promo['status'] == 'warning') {
                $status = 'warning';
            }
            // });
        }
        if (count($barang_pesanan_stok_error) > 0) {
            $nama_barang = collect($barang_pesanan_stok_error)->pluck('nama_barang')->join(', ');
            $this->dispatch('alert-info', message: 'Stok barang <b class="!font-extrabold">[' . $nama_barang . '</b>] tidak mencukupi.', status: 'warning', subtitle: 'silahkan perbarui stok barang atau perbarui jumlah pesanan');
            $ids = array_column($barang_pesanan_stok_error, 'id_barang_pesanan');
            $this->pesanan_kasir->barang_pesanan_kasir()->whereIn('id', $ids)->delete();
            $this->pesanan_kasir->refresh()->loadMissing([
                'promo_pesanan_kasir.promo_barang',
                'barang_pesanan_kasir.barang.opsi_satuan',
                'barang_pesanan_kasir.barang.satuan_utama',
                'barang_pesanan_kasir.barang.opsi_harga.satuan:id,nama',
                'barang_pesanan_kasir.satuan',
            ]);
        }
        $id_promo_transaksi_selected = $this->pesanan_kasir->promo_pesanan_kasir->whereNotNull('id_promo_transaksi')->first()?->id_promo_transaksi;
        $promo                       = $this->sinkronPromoTransaksi($id_promo_transaksi_selected);

        $this->pesanan_kasir->update([
            'total_harga' => $this->pesanan_kasir->barang_pesanan_kasir->sum('sub_total'),
        ]);
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

    public function setFormPesananKasir()
    {
        $this->mode_kasir = $this->pesanan_kasir->mode_kasir;
        $this->mode_shift = $this->pesanan_kasir->mode_shift;
    }

    public function tambahPesananKasir()
    {
        if (Auth::user()->can('pesanan-kasir.create')) {
            $this->createPesananKasir();
            session()->flash('alert', [
                'title'   => 'Berhasil!',
                'message' => 'Berhasil Membuat Pesanan Kasir Baru',
                'icon'    => 'success', // Bisa 'success', 'error', 'warning', dll.
            ]);
            return $this->redirect(route('kasir-cadangan'), true);
        } else {
            $this->dispatch('alert', message: 'Kamu Tidak Memiliki Akses', status: 'error');
        }

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

    public function render()
    {
        return view('livewire.kasir-cadangan.kasir-index');
    }
}
