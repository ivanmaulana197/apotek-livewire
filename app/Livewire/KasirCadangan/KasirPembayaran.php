<?php
namespace App\Livewire\KasirCadangan;

use App\Helpers\PembulatanHelper;
use App\Livewire\KasirCadangan\Helpers\KasirHelper;
use App\Models\DataMaster\Dokter;
use App\Models\DataMaster\Member;
use App\Models\DataMaster\MetodePembayaran;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class KasirPembayaran extends Component
{
    use KasirHelper;
    public $pesanan_kasir, $setting;
    public $nama_pasien, $alamat_pasien, $no_hp_pasien, $dokter_pasien;
    public $id_metode_pembayaran, $id_member;

    public $bayar, $kembalian;

    public $mode_kasir, $sub_total, $diskon, $total_bayar;
    public $promo_pesanan_kasir;

    public $metode_pembayarans, $members, $dokters;

    public function mount()
    {
        $this->metode_pembayarans  = MetodePembayaran::all();
        $this->members             = Member::all();
        $this->dokters             = Dokter::all();
        $this->setting             = $this->setting->toArray();
        $this->mode_kasir          = $this->pesanan_kasir->mode_kasir;
        $this->id_member           = $this->pesanan_kasir->id_member;
        $this->promo_pesanan_kasir = $this->pesanan_kasir->promo_pesanan_kasir->toArray();
        $this->sub_total           = ceil($this->pesanan_kasir->total_harga);
        $this->diskon              = ceil($this->pesanan_kasir->promo_pesanan_kasir->sum('nominal_promo'));
        $this->total_bayar         = ceil(PembulatanHelper::pembulatanTotalHarga($this->sub_total - $this->diskon, $this->mode_kasir == 'RESEP' ? 'RESEP' : 'UMUM', false));
        // dd($this->total_bayar, $this->sub_total, $this->diskon);
    }

    public function updatePromoTransaksi($id_promo_transaksi_selected)
    {
        // dd($id_promo_transaksi_selected);
        try {
            $this->pesanan_kasir->refresh()->loadMissing([
                'promo_pesanan_kasir.promo_barang',
                // 'promo_pesanan_kasir.promo_transaksi',
                'barang_pesanan_kasir.barang.opsi_satuan',
                'barang_pesanan_kasir.barang.satuan_utama',
                'barang_pesanan_kasir.barang.opsi_harga.satuan:id,nama',
                // 'barang_pesanan_kasir.barang.opsi_harga.barang:id,id_satuan_utama',
                // 'barang_pesanan_kasir.barang.opsi_harga.barang.opsi_satuan:id',
                'barang_pesanan_kasir.satuan',
            ]);
            DB::beginTransaction();

            $promo = $this->sinkronPromoTransaksi($id_promo_transaksi_selected);
            if ($promo && ! $promo['success']) {
                $this->dispatch('alert', message: $promo['error'], status: 'error');
                DB::rollback();
                return;
            }
            // dd($this->pesanan_kasir);
            DB::commit();

            $this->syncFrontEnd();
            if ($promo && ($promo['success'] ?? false) && ($promo['error'] ?? false)) {
                return ['status' => 'success'];
            } else {
                $this->dispatch('alert', message: 'Barang berhasil ditambahkan.', status: 'success');
                return ['status' => 'success'];
            }
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

    }

    public function render()
    {
        return view('livewire.kasir-cadangan.kasir-pembayaran');
    }

    // #[Computed(persist: true, seconds: 3600, cache: true)]
    // public function metode_pembayarans()
    // {
    //     logger('computed members dipanggil');
    //     return MetodePembayaran::select('id', 'nama')->get();
    // }

    // #[Computed(persist: true, seconds: 3600, cache: true)]
    // public function members()
    // {
    //     return Member::select('id', 'nama')->get();
    // }

    // #[Computed(persist: true, seconds: 3600, cache: true)]
    // public function dokters()
    // {
    //     return Dokter::select('id', 'nama')->get();
    // }
}
