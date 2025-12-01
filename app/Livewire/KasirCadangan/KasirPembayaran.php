<?php
namespace App\Livewire\KasirCadangan;

use App\Helpers\FormatHelper;
use App\Helpers\GenerateHelper;
use App\Helpers\PembulatanHelper;
use App\Livewire\KasirCadangan\Helpers\KasirHelper;
use App\Models\Barang\Barang;
use App\Models\DataMaster\Dokter;
use App\Models\DataMaster\Member;
use App\Models\DataMaster\MetodePembayaran;
use App\Models\DataMaster\Pasien;
use App\Models\HistoryBarang;
use App\Models\Transaksi\DetailTransaksiBarang;
use App\Models\Transaksi\Transaksi;
use App\Models\Transaksi\TransaksiListPromo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
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
        $this->setValue();
        $this->metode_pembayarans  = MetodePembayaran::all();
        $this->members             = Member::all();
        $this->dokters             = Dokter::all();
        $this->setting             = $this->setting->toArray();
        $this->mode_kasir          = $this->pesanan_kasir->mode_kasir;
        $this->promo_pesanan_kasir = $this->pesanan_kasir->promo_pesanan_kasir->toArray();
        $this->sub_total           = ceil($this->pesanan_kasir->total_harga);
        $this->diskon              = ceil($this->pesanan_kasir->promo_pesanan_kasir->sum('nominal_promo'));
        $this->total_bayar         = ceil(PembulatanHelper::pembulatanTotalHarga($this->sub_total - $this->diskon, $this->mode_kasir == 'RESEP' ? 'RESEP' : 'UMUM', false));
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

    public function updated($name, $value)
    {
        if ($name == 'id_member') {
            $this->validate(['id_member' => 'nullable|exists:member,id']);
            $this->pesanan_kasir->id_member = $value == '' ? null : $value;
            $this->pesanan_kasir->save();
        } elseif ($name == 'id_metode_pembayaran') {
            $this->validate(['id_metode_pembayaran' => 'required|exists:metode_pembayaran,id']);
            $this->pesanan_kasir->id_metode_pembayaran = $value == '' ? null : $value;
            $this->pesanan_kasir->save();
        } elseif ($name == 'dokter_pasien') {
            $this->validate(['dokter_pasien' => 'nullable|exists:dokter,id']);
            $this->pesanan_kasir->id_dokter = $value == '' ? null : $value;
            $this->pesanan_kasir->save();
        } elseif ($name == 'nama_pasien') {
            $this->pesanan_kasir->nama_pasien = strtoupper($value);
            $this->pesanan_kasir->save();
        } elseif ($name == 'alamat_pasien') {
            $this->pesanan_kasir->alamat_pasien = strtoupper($value);
            $this->pesanan_kasir->save();
        } elseif ($name == 'no_hp_pasien') {
            $this->pesanan_kasir->no_hp_pasien = $value;
            $this->pesanan_kasir->save();
        }
    }

    public function updatePromoTransaksi($id_promo_transaksi_selected)
    {
        try {
            $this->pesanan_kasir->refresh()->loadMissing([
                'promo_pesanan_kasir.promo_barang',
                'barang_pesanan_kasir.satuan',
                'barang_pesanan_kasir.barang.opsi_satuan',
                'barang_pesanan_kasir.barang.satuan_utama',
                'barang_pesanan_kasir.barang.opsi_harga.satuan:id,nama',
            ]);
            DB::beginTransaction();

            $promo = $this->sinkronPromoTransaksi($id_promo_transaksi_selected);

            DB::commit();

            $this->syncFrontEnd();

            if ((empty($promo) || ($promo['status'] ?? null) === 'success')) {
                $this->dispatch('alert', message: 'Barang berhasil ditambahkan.', status: 'success');
                return ['status' => 'success'];
            }
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

    }

    public function handlePembayaran()
    {
        if (! Auth::user()->can('kasir') || (! Auth::user()->can('kasir-show-all') && ! (Auth::user()->can('kasir-show-own') && $this->pesanan_kasir->id_user == Auth::user()->id))) {
            return $this->dispatch('alert', message: 'Anda tidak memiliki izin untuk melakukan pembayaran', status: 'error');
        }
        try {
            $this->bayar     = FormatHelper::formatStringToNumber($this->bayar);
            $this->kembalian = FormatHelper::formatStringToNumber($this->kembalian);
            $this->validate([
                'bayar'     => 'required|numeric',
                'kembalian' => 'required|numeric',
            ]);
            DB::beginTransaction();

            $this->pesanan_kasir->refresh()->loadMissing([
                'promo_pesanan_kasir.promo_barang',
                'barang_pesanan_kasir.satuan',
                'barang_pesanan_kasir.barang.opsi_satuan',
                'barang_pesanan_kasir.barang.satuan_utama',
                'barang_pesanan_kasir.barang.opsi_harga.satuan:id,nama',
            ]);
            $jenis_transaksi   = $this->pesanan_kasir->mode_kasir;
            $id_pasien         = null;
            $id_dokter         = null;
            $this->sub_total   = ceil($this->pesanan_kasir->total_harga);
            $this->diskon      = ceil($this->pesanan_kasir->promo_pesanan_kasir->sum('nominal_promo'));
            $this->total_bayar = ceil(PembulatanHelper::pembulatanTotalHarga($this->sub_total - $this->diskon, $this->mode_kasir == 'RESEP' ? 'RESEP' : 'UMUM', false));
            $this->kembalian   = $this->bayar - $this->total_bayar;
            if ($this->bayar < $this->total_bayar || $this->kembalian < 0) {
                $this->dispatch('alert', message: 'Pembayaran kurang.', status: 'error');
                DB::rollback();
                return;
            }

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
                'bayar'                => $this->bayar,
                'kembalian'            => $this->kembalian,
                'id_user'              => Auth::user()->id,
                'id_metode_pembayaran' => $this->id_metode_pembayaran,
                'jenis_transaksi'      => $jenis_transaksi,
                'shift_transaksi'      => $this->pesanan_kasir->mode_shift,
                'status'               => 'TERJUAL',
                'id_member'            => $this->id_member,
                'id_dokter'            => $id_dokter,
                'id_pasien'            => $id_pasien,
            ]);

            foreach ($this->pesanan_kasir->barang_pesanan_kasir as $barang_pesanan_kasir) {
                if ($barang_pesanan_kasir->jumlah_dalam_satuan_utama > $barang_pesanan_kasir->barang->stok) {
                    $this->dispatch('alert-info', message: 'Stok Barang [' . $barang_pesanan_kasir->barang->nama_barang . '] tidak mencukupi', status: 'warning', subtitle: 'silahkan ubah pesanan');
                    DB::rollBack();
                    return;
                }
                $total_jumlah             = $this->totalJumlahBarangDalamSatuanUtama($barang_pesanan_kasir->barang);
                $id_promo_barang_selected = $this->getIdPromoBarangSelected($barang_pesanan_kasir->id_barang);
                $sub_total                = $this->pesanan_kasir->barang_pesanan_kasir->where('id_barang', $barang_pesanan_kasir->id_barang)->sum('sub_total');
                // 🔸 Cek stok bonus barang

                $promo = $this->applyPromoBarang($barang_pesanan_kasir->id_barang, $total_jumlah, $sub_total, $id_promo_barang_selected);
                if ($promo && $promo['status'] == 'warning') {
                    // $this->dispatch('alert-info', message: $promo['message'], status: 'warning', subtitle: 'silahkan ubah pesanan');
                    DB::rollBack();
                    return;
                }

                $this->saveDetailTransaksi($barang_pesanan_kasir, $transaksi->id);
            }

            $this->savePromoTransaksi($this->pesanan_kasir->promo_pesanan_kasir, $transaksi->id);
            $this->dispatch('struk-pembayaran', id_transaksi: $transaksi->id);
            $this->dispatch('delete-pesanan-kasir', id: $this->pesanan_kasir->id, message: 'Transaksi Berhasil Dilakukan');
            DB::commit();

        } catch (\Exception $e) {
            $this->bayar     = FormatHelper::formatNumberToString($this->bayar);
            $this->kembalian = FormatHelper::formatNumberToString($this->kembalian);
            $this->dispatch('alert', message: $e->getMessage(), status: 'error');
            DB::rollBack();
            throw $e;
        }
    }

    public function render()
    {
        return view('livewire.kasir-cadangan.kasir-pembayaran');
    }

    public function saveDetailTransaksi($barang_pesanan, $id_transaksi)
    {
        $history_barang = HistoryBarang::create([
            // 'referensi_id'   => $id_transaksi,
            // 'referensi_type' => Transaksi::class,
            'id_barang' => $barang_pesanan->id_barang,
            'jumlah'    => $barang_pesanan->jumlah,
            'id_satuan' => $barang_pesanan->id_satuan,
            'status'    => 'TERJUAL',
            'id_user'   => Auth::user()->id,
        ]);
        // $rasio      = $barang_pesanan->barang->calculateJumlahDalamSatuanUtama(1, $barang_pesanan->id_satuan);
        // $harga_beli = $barang_pesanan->barang->harga_beli_default * $rasio;
        // $komisi     = $barang_pesanan->sub_total - ($harga_beli * $barang_pesanan->jumlah_dalam_satuan_utama);

        $rasio      = $this->calculateJumlahDalamSatuanUtama($barang_pesanan->barang, 1, $barang_pesanan->id_satuan);
        $harga_beli = $barang_pesanan->barang->harga_beli_default * $rasio;
        $komisi     = $barang_pesanan->sub_total - ($harga_beli * ($barang_pesanan->jumlah * $rasio));

        DetailTransaksiBarang::create([
            'id_history_barang'  => $history_barang->id,
            'id_transaksi'       => $id_transaksi,
            'harga_beli'         => $harga_beli,
            'komisi'             => $komisi,
            'harga'              => $barang_pesanan->harga,
            'harga_satuan_utama' => $barang_pesanan->harga_satuan_utama,
            'total_harga'        => $barang_pesanan->sub_total,
        ]);

        $get_tgl_expired = $this->updateStokBarang($barang_pesanan->id_barang, $barang_pesanan->jumlah * $rasio);

        $history_barang->update(['tgl_expired' => $get_tgl_expired]);
    }

    public function updateStokBarang($id_barang, $jumlah_keluar_dalam_satuan_utama)
    {
        // Gunakan eager load + urutkan batch paling cepat expired dulu
        $barang        = Barang::with(['stok_batch_barang' => fn($q) => $q->orderBy('tgl_expired', 'asc')])->find($id_barang);
        $jumlah_keluar = $jumlah_keluar_dalam_satuan_utama;
        $jumlah_sisa   = $jumlah_keluar;
        $tgl_expired   = [];

        foreach ($barang->stok_batch_barang as $stok_batch_barang) {
            if ($jumlah_sisa > $stok_batch_barang->jumlah) { //berarti stok batch barang info habis
                                                                 // kalau ada sisanya barang info saat ini delete
                $tgl_expired[] = ['tgl' => $stok_batch_barang->tgl_expired, 'jumlah' => $stok_batch_barang->jumlah];
                $jumlah_sisa -= $stok_batch_barang->jumlah;
                $stok_batch_barang->delete();
            } elseif ($jumlah_sisa < $stok_batch_barang->jumlah) { //berarti stok batch barang info cukup
                                                                       // jumlah keluar lebih kecil dari stok barang info
                $tgl_expired[] = ['tgl' => $stok_batch_barang->tgl_expired, 'jumlah' => $jumlah_sisa];
                $stok_batch_barang->decrement('jumlah', $jumlah_sisa);
                break;
            } else {
                //jika barang keluar habis (jumlah_keluar == stok dari barang info)
                $tgl_expired[] = ['tgl' => $stok_batch_barang->tgl_expired, 'jumlah' => $stok_batch_barang->jumlah];
                $stok_batch_barang->delete();
                break;
            }
        }
        // update stok barang
        // $barang->decrement('stok', $jumlah_keluar);
        $barang->update(['stok' => $barang->stok - $jumlah_keluar]);

        return $tgl_expired;
    }

    public function savePromoTransaksi($promo_pesanan_kasir, $id_transaksi)
    {
        if ($promo_pesanan_kasir->isNotEmpty()) {
            $promo_pesanan_kasir->loadMissing([
                'promo_barang.barang.opsi_satuan',
                'promo_barang.barang_bonus.opsi_satuan',
                'promo_barang.satuan_bonus',
            ]);
            $data = $promo_pesanan_kasir->map(function ($item) use ($id_transaksi) {
                if ($item->tipe_promo == 'BARANG BONUS') {
                    $id_barang_bonus           = $item->promo_barang->id_barang_bonus;
                    $jumlah_dalam_satuan_utama = $item->promo_barang->barang_bonus->hitungJumlahDalamSatuanUtama($item->jumlah, $item->id_satuan);
                    $item->nominal_promo       = $item->promo_barang->barang_bonus->harga_beli_default * $jumlah_dalam_satuan_utama;
                    $get_tgl_expired           = $this->updateStokBarang($id_barang_bonus, $jumlah_dalam_satuan_utama);
                }

                if ($item->id_promo_barang) {
                    $id_barang = $item->promo_barang->id_barang;
                }

                return [
                    'id'              => Str::ulid(),
                    'id_transaksi'    => $id_transaksi,
                    'tipe_promo'      => $item->tipe_promo,
                    'id_barang'       => $id_barang ?? null,
                    'id_barang_bonus' => $id_barang_bonus ?? null,
                    'jumlah'          => $item->jumlah,
                    'tgl_expired'     => isset($get_tgl_expired) ? json_encode($get_tgl_expired) : null,
                    'id_satuan'       => $item->id_satuan,
                    'nominal_promo'   => $item->nominal_promo,
                    'deskripsi'       => $item->deskripsi,
                ];
            })->toArray();

            TransaksiListPromo::insert($data);
        }
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
