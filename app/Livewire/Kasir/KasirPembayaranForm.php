<?php
namespace App\Livewire\Kasir;

use App\Helpers\GenerateHelper;
use App\Helpers\PembulatanHelper;
use App\Livewire\Kasir\Helpers\KasirHelper;
use App\Models\DataMaster\Dokter;
use App\Models\DataMaster\Member;
use App\Models\DataMaster\MetodePembayaran;
use App\Models\DataMaster\Pasien;
use App\Models\Transaksi\Transaksi;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

class KasirPembayaranForm extends Component
{
    use KasirHelper;
    public $pesanan_kasir, $sub_total, $barang_pesanan_kasir, $diskon, $total_bayar;
    public $nama_pasien, $alamat_pasien, $no_hp_pasien, $dokter_pasien;
    public $id_metode_pembayaran, $id_member;

    public $id_list_diskon_transaksi, $id_promo_transaksi_selected;

    public $bayar, $kembalian;

    public function mount()
    {
        $this->setValue();
        $this->id_promo_transaksi_selected = $this->pesanan_kasir->promo_pesanan_kasir()->whereNotNull('id_promo_transaksi')->first()?->id_promo_transaksi;
    }

    public function updated($name, $value)
    {
        if ($name == 'id_member') {
            $this->validate(['id_member' => 'nullable|exists:member,id']);
            $this->pesanan_kasir->id_member = $value == '' ? null : $value;
            $this->pesanan_kasir->save();
            $this->dispatch('update_pesanan_kasir');
        } elseif ($name == 'id_metode_pembayaran') {
            $this->validate(['id_metode_pembayaran' => 'required|exists:metode_pembayaran,id']);
            $this->pesanan_kasir->id_metode_pembayaran = $value == '' ? null : $value;
            $this->pesanan_kasir->save();
            $this->dispatch('update_pesanan_kasir');
        } elseif ($name == 'dokter_pasien') {
            $this->validate(['dokter_pasien' => 'nullable|exists:dokter,id']);
            $this->pesanan_kasir->id_dokter = $value == '' ? null : $value;
            $this->pesanan_kasir->save();
            $this->dispatch('update_pesanan_kasir');
        } elseif ($name == 'nama_pasien') {
            $this->pesanan_kasir->nama_pasien = strtoupper($value);
            $this->pesanan_kasir->save();
            $this->dispatch('update_pesanan_kasir');
        } elseif ($name == 'alamat_pasien') {
            $this->pesanan_kasir->alamat_pasien = strtoupper($value);
            $this->pesanan_kasir->save();
            $this->dispatch('update_pesanan_kasir');
        } elseif ($name == 'no_hp_pasien') {
            $this->pesanan_kasir->no_hp_pasien = $value;
            $this->pesanan_kasir->save();
            $this->dispatch('update_pesanan_kasir');
        }
    }

    public function showDiskon()
    {
        $this->dispatch('toggle-modal-diskon_transaksi', diskon_transaksiModal: true);
        $this->dispatch('list-diskon-transaksi', id_promo_transaksi_selected: $this->id_promo_transaksi_selected, id_promo_transaksis: $this->id_list_diskon_transaksi, sub_total: $this->sub_total);
    }

    #[On('select-diskon-transaksi')]
    public function setDiskonSelected($id_promo_transaksi)
    {
        $this->id_promo_transaksi_selected = $id_promo_transaksi;
        // $this->setPromoTransaksiKasir($this->sub_total, $this->id_promo_transaksi_selected);
    }

    public function handlePembayaran($bayar, $kembalian)
    {
        try {
            DB::beginTransaction();
            $bayar;
            $kembalian;
            $jenis_transaksi = $this->pesanan_kasir->mode_kasir;
            $id_pasien       = null;
            $id_dokter       = null;
            if ($this->pesanan_kasir->mode_kasir == 'RESEP') {
                $data_pasien = $this->validate([
                    'nama_pasien'   => 'required',
                    'alamat_pasien' => 'nullable',
                    'no_hp_pasien'  => 'nullable',
                    'dokter_pasien' => 'nullable|exists:dokter,id',
                ]);
                $pasien = Pasien::firstOrCreate(
                    [
                        'nama_pasien'   => strtoupper($data_pasien['nama_pasien']),
                        'alamat_pasien' => strtoupper($data_pasien['alamat_pasien'] ?? '-'),
                        'no_hp_pasien'  => $data_pasien['no_hp_pasien'] ?? '-',
                    ]
                );
                $id_pasien = $pasien->id;
                $id_dokter = $this->dokter_pasien;
                if ($this->id_member) {
                    $jenis_transaksi = 'MEMBERSHIP RESEP';
                }
            } else if ($this->pesanan_kasir->mode_kasir == 'UMUM') {
                if ($this->id_member) {
                    $jenis_transaksi = 'MEMBERSHIP';
                }
            }

            $transaksi = Transaksi::create([
                'no_transaksi'         => GenerateHelper::generateTransaksiId(),
                'sub_total'            => $this->sub_total,
                'potongan_diskon'      => $this->diskon,
                'total_bayar'          => $this->total_bayar,
                'pembulatan'           => $this->total_bayar - ($this->sub_total - $this->diskon),
                'bayar'                => $bayar,
                'kembalian'            => $kembalian,
                'id_user'              => Auth::user()->id,
                'id_metode_pembayaran' => $this->id_metode_pembayaran,
                'jenis_transaksi'      => $jenis_transaksi,
                'shift_transaksi'      => $this->pesanan_kasir->mode_shift,
                'status'               => 'TERJUAL',
                'id_member'            => $this->id_member,
                'id_dokter'            => $id_dokter,
                'id_pasien'            => $id_pasien,
            ]);
            $barang_pesanan_kasir = $this->pesanan_kasir->load(['barang_pesanan_kasir.barang'])->barang_pesanan_kasir;

            foreach ($barang_pesanan_kasir as $barang_pesanan_kasir) {
                if ($barang_pesanan_kasir->jumlah_dalam_satuan_utama > $barang_pesanan_kasir->barang->stok) {
                    $this->dispatch('error-stok', id_barang_pesanan: $barang_pesanan_kasir->id);
                    DB::rollBack();
                    return;
                }
                $total_jumlah = $this->pesanan_kasir->barang_pesanan_kasir()->totalJumlahDalamSatuanUtama($barang_pesanan_kasir->id_barang);
                if (! $this->cekStokPromoBarang($barang_pesanan_kasir->id_barang, $total_jumlah)) {
                    $this->dispatch('error-stok', id_barang_pesanan: $barang_pesanan_kasir->id);
                    $this->dispatch('alert', message: 'Stok barang bonus tidak mencukupi.', status: 'error');
                    DB::rollback();
                    return;
                }
                $this->saveDetailTransaksi($barang_pesanan_kasir, $transaksi->id);
            }

            $this->savePromoTransaksi($this->pesanan_kasir->load('promo_pesanan_kasir.promo_barang.barang_bonus')->promo_pesanan_kasir, $transaksi->id);
            $this->dispatch('struk-pembayaran', id_transaksi: $transaksi->id);
            $this->dispatch('delete-pesanan-kasir', id: $this->pesanan_kasir->id, message: 'Transaksi Berhasil Dilakukan');
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            $this->dispatch('alert', status: 'error', message: 'Gagal Melakukan Pembayaran', subtitle: $e->getMessage());
            throw $e;
        }

    }

    #[On('update_pesanan_kasir')]
    public function render()
    {
        $this->sub_total                = $this->pesanan_kasir->barang_pesanan_kasir()->sum('sub_total');
        $this->id_list_diskon_transaksi = $this->getListPromoTransaksi($this->sub_total, $this->pesanan_kasir->id_member);
        $this->setPromoTransaksiKasir($this->sub_total, $this->id_promo_transaksi_selected);
        $this->diskon = $this->pesanan_kasir->promo_pesanan_kasir()->sum('nominal_promo');
        // $this->setPromoTransaksiKasirOtomatis($this->pesanan_kasir->total_harga, $this->pesanan_kasir->member?->id_profesi);
        $this->total_bayar = PembulatanHelper::pembulatanTotalHarga($this->sub_total - $this->diskon, $this->pesanan_kasir->mode_kasir == 'RESEP' ? 'RESEP' : 'UMUM', false);
        $this->dispatch('total_bayar_update', total_bayar: $this->total_bayar);
        return view('livewire.kasir.kasir-pembayaran-form');
    }

    public function setValue()
    {
        $this->id_metode_pembayaran = $this->pesanan_kasir->id_metode_pembayaran;
        $this->id_member            = $this->pesanan_kasir->id_member;
        $this->nama_pasien          = $this->pesanan_kasir->nama_pasien;
        $this->alamat_pasien        = $this->pesanan_kasir->alamat_pasien;
        $this->no_hp_pasien         = $this->pesanan_kasir->no_hp_pasien;
        $this->dokter_pasien        = $this->pesanan_kasir->id_dokter;
    }

    #[Computed(persist: true, seconds: 3600, cache: true)]
    public function metode_pembayarans()
    {
        return MetodePembayaran::select('id', 'nama')->get();
    }

    #[Computed(persist: true, seconds: 3600, cache: true)]
    public function members()
    {
        return Member::select('id', 'nama')->get();
    }

    #[Computed(persist: true, seconds: 3600, cache: true)]
    public function dokters()
    {
        return Dokter::select('id', 'nama')->get();
    }
}
