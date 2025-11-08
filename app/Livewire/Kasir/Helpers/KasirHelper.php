<?php
namespace App\Livewire\Kasir\Helpers;

use App\Helpers\FormatHelper;
use App\Models\Barang\Barang;
use App\Models\DataMaster\Member;
use App\Models\HistoryBarang;
use App\Models\Kasir\PromoPesananKasir;
use App\Models\Promo\Promo;
use App\Models\Promo\PromoBarang;
use App\Models\Promo\PromoTransaksi;
use App\Models\Transaksi\DetailTransaksiBarang;
use App\Models\Transaksi\TransaksiListPromo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

trait KasirHelper
{
    public function checkModeShift()
    {
        $currentHour = Carbon::now()->hour;
        return $currentHour < 14 ? 'PAGI' : 'MALAM';
    }

    public function hargaJualResep($hargaLama, $persen_resep)
    {
        return $hargaLama + ($hargaLama * $persen_resep) / 100;
    }

    public function getListPromoBarang($id_barang, $jumlah_dalam_satuan_utama, $id_member = null)
    {
        $id_profesi = null;
        // dd($id_member);
        if ($id_member) {
            $member     = Member::find($id_member);
            $id_profesi = $member->id_profesi;
        }

        $promoBarang = PromoBarang::query()
            ->where('id_barang', $id_barang)
            ->whereHas('promo', function ($q) use ($id_profesi) {
                $q->aktif()
                    ->when($id_profesi, function ($q) use ($id_profesi) {
                        $q->where(function ($query) use ($id_profesi) {
                            $query->whereDoesntHave('profesies') // promo umum
                                ->orWhereHas('profesies', fn($q) => $q->where('id_profesi', $id_profesi));
                        });
                    }, function ($q) {
                        // Jika user tidak punya profesi, ambil hanya promo umum
                        $q->whereDoesntHave('profesies');
                    });
            })->get()
            ->filter(function ($barangPromo) use ($jumlah_dalam_satuan_utama) {
                // Ambil hanya promo yang memenuhi syarat
                return $barangPromo->SyaratQtyDalamSatuanUtama <= $jumlah_dalam_satuan_utama;
            })
            ->pluck('id')
            ->toArray();

        return $promoBarang;

    }

    public function getListPromoTransaksi($harga, $id_member = null)
    {
        $id_profesi = null;
        // dd($id_member);
        if ($id_member) {
            $member     = Member::find($id_member);
            $id_profesi = $member->id_profesi;
        }
        // dd($id_profesi);
        $promo = PromoTransaksi::where('minimal_belanja', '<=', $harga)
            ->whereHas('promo', function ($q) use ($id_profesi) {
                $q->aktif()
                    ->when($id_profesi, function ($q) use ($id_profesi) {
                        $q->where(function ($query) use ($id_profesi) {
                            $query->whereDoesntHave('profesies') // promo umum
                                ->orWhereHas('profesies', fn($q) => $q->where('id_profesi', $id_profesi));
                        });
                    }, function ($q) {
                        // Jika user tidak punya profesi, ambil hanya promo umum
                        $q->whereDoesntHave('profesies');
                    });
            })
            ->get()
            ->sortByDesc('minimal_belanja')
            ->pluck('id')
            ->toArray();
        return $promo;
    }
    public function diskonTotalHarga($harga, $id_member = null)
    {
        $id_profesi = null;
        // dd($id_member);
        if ($id_member) {
            $member     = Member::find($id_member);
            $id_profesi = $member->id_profesi;
        }
        // dd($id_profesi);
        $promo = Promo::aktif()
            ->whereHas('promoTransaksi', function ($q) use ($harga) {
                $q->where('minimal_belanja', '<=', $harga);
            })
            ->when($id_profesi, function ($q) use ($id_profesi) {
                $q->where(function ($query) use ($id_profesi) {
                    $query->whereDoesntHave('profesies') // promo umum
                        ->orWhereHas('profesies', fn($q) => $q->where('id_profesi', $id_profesi));
                });
            }, function ($q) {
                // Jika user tidak punya profesi, ambil hanya promo umum
                $q->whereDoesntHave('profesies');
            })
            ->with(['promoTransaksi', 'profesies'])
            ->get()
            ->sortByDesc(fn($promo) => optional($promo->promoTransaksi)->minimal_belanja)->first();

        if (! $promo) {
            return 0;
        }
        if ($promo->promoTransaksi->tipe_transaksi == "PERSEN") {
            $promo->promoTransaksi->diskon_nominal = $harga * $promo->promoTransaksi->diskon_persen / 100;
        }
        return $promo->promoTransaksi->diskon_nominal;
    }

    public function setNominalPromo($promoBarang, $harga_sub_total, $jumlah_beli)
    {
        if ($promoBarang->tipe == 'PERSEN') {
            $diskon    = $harga_sub_total * $promoBarang->diskon_persen / 100;
            $deskripsi = 'Promo ' . $promoBarang->barang->nama . ' Diskon (' . FormatHelper::formatNumberToString($promoBarang->diskon_persen) . '%) = Rp. ' . FormatHelper::formatNumberToString($diskon);
        } elseif ($promoBarang->tipe == 'HARGA') {
            $diskon = $promoBarang->diskon_nominal;
            if ($promoBarang->tipe_qty == 'KELIPATAN') {
                $kelipatan        = $promoBarang->barang->calculateJumlahDalamSatuanUtama($promoBarang->syarat_qty, $promoBarang->id_satuan);
                $jumlah_kelipatan = floor($jumlah_beli / $kelipatan);
                $diskon           = $jumlah_kelipatan * $diskon;
            }
            $deskripsi = 'Promo ' . $promoBarang->barang->nama . ' Potongan Harga Rp. ' . FormatHelper::formatNumberToString($diskon);
        }
        return [$diskon, $deskripsi];
    }

    public function setPromoBarangKasir($id_barang, $id_promo_barang = null)
    {

        // 🔹 1. Jika promo dihapus
        if (! $id_promo_barang) {
            $this->pesanan_kasir->promo_pesanan_kasir()
                ->whereHas('promo_barang', fn($q) => $q->where('id_barang', $id_barang))
                ->delete();
            return;
        }

        // 🔹 2. Ambil data yang dibutuhkan dalam satu kali query
        $promoBarang = PromoBarang::with(['barang', 'barang_bonus', 'satuan_bonus'])->find($id_promo_barang);
        if (! $promoBarang) {
            return;
        }

        $harga_sub_total = $this->pesanan_kasir->barang_pesanan_kasir()->totalHargaBarang($id_barang);
        $jumlah_beli     = $this->pesanan_kasir->barang_pesanan_kasir()->totalJumlahDalamSatuanUtama($id_barang, 0);

        // 🔹 3. Set tipe promo
        if ($promoBarang->tipe == 'PERSEN' || $promoBarang->tipe == 'HARGA') {
            $tipe_promo                  = 'BARANG DISKON';
            [$nominal_promo, $deskripsi] = $this->setNominalPromo($promoBarang, $harga_sub_total, $jumlah_beli);
        } elseif ($promoBarang->tipe == 'BARANG BONUS') {
            $tipe_promo = 'BARANG BONUS';
            $jumlah     = $promoBarang->bonus_qty;
            if ($promoBarang->tipe_qty == 'KELIPATAN') {
                $kelipatan        = $promoBarang->barang_bonus->calculateJumlahDalamSatuanUtama($promoBarang->syarat_qty, $promoBarang->id_satuan);
                $jumlah_kelipatan = floor($jumlah_beli / $kelipatan);
                $jumlah           = $jumlah_kelipatan * $promoBarang->bonus_qty;
            }
            $deskripsi = 'FREE ' . $promoBarang->barang_bonus->nama . ': [ ' . $jumlah . ' / ' . $promoBarang->satuan_bonus->nama . ']';
        }

        // 🔹 4. Hapus promo lama untuk barang sama tapi promo berbeda
        $this->pesanan_kasir->promo_pesanan_kasir()
            ->whereHas('promo_barang', fn($q) => $q->where('id_barang', $id_barang))
            ->where('id_promo_barang', '!=', $id_promo_barang)
            ->delete();

        // 🔹 Cek apakah promo ini sudah ada di pesanan
        $promoPesananKasir = $this->pesanan_kasir->promo_pesanan_kasir()->where('id_promo_barang', $id_promo_barang)->first();
        $dataPromo         = [
            'id_pesanan_kasir' => $this->pesanan_kasir->id,
            'id_promo_barang'  => $id_promo_barang,
            'tipe_promo'       => $tipe_promo,
            'nominal_promo'    => $nominal_promo ?? null,
            'jumlah'           => $jumlah ?? null,
            'id_satuan'        => $promoBarang->id_satuan_bonus ?? null,
            'deskripsi'        => $deskripsi,
        ];

        if ($promoPesananKasir) {
            // Jika promo tidak cocok dengan barang yang sama, hapus dulu
            if ($promoPesananKasir->promo_barang->id_barang != $id_barang) {
                $promoPesananKasir->delete();
            } else {
                $promoPesananKasir->update($dataPromo);
                return $promoPesananKasir;
            }
        }

        return PromoPesananKasir::create($dataPromo);

    }

    public function cekStokPromoBarang($id_barang, $total_jumlah)
    {
        $promoPesananKasir = $this->pesanan_kasir->promo_pesanan_kasir()
            ->whereHas('promo_barang', function ($q) use ($id_barang) {
                $q->where('id_barang', $id_barang)->where('tipe_promo', 'BARANG BONUS');
            })
            ->with(['promo_barang:id,id_barang_bonus', 'promo_barang.barang_bonus:id,stok,id_satuan_utama'])
            ->first();
        // 🔸 Tidak ada promo bonus → stok aman
        if (! $promoPesananKasir) {
            return true;
        }

        $barang_bonus = $promoPesananKasir->promo_barang->barang_bonus;

        if (! $barang_bonus) {
            return true; // tidak ada data bonus → abaikan
        }

        // 🔹 Hitung jumlah barang bonus dalam satuan utama
        $jumlah_yang_akan_keluar = $barang_bonus->calculateJumlahDalamSatuanUtama(
            $promoPesananKasir->jumlah,
            $promoPesananKasir->id_satuan
        );

        // Jika barang bonus adalah barang yang sama → tambahkan jumlah beli juga
        if ($barang_bonus->id == $id_barang) {
            $jumlah_yang_akan_keluar += $total_jumlah;
        }
        // dd($jumlah_yang_akan_keluar, $total_jumlah);
        // 🔹 Cek stok
        return $jumlah_yang_akan_keluar <= $barang_bonus->stok;
    }

    public function setPromoTransaksiKasir($total_harga, $id_promo_transaksi = null)
    {
        $promo_pesanan_kasir = $this->pesanan_kasir->promo_pesanan_kasir()->whereNotNull('id_promo_transaksi')->first();
        if (! $id_promo_transaksi) {
            $promo_pesanan_kasir?->delete();
            return;
        }

        $promo_transaksi = PromoTransaksi::with(['promo'])->find($id_promo_transaksi);
        if ($promo_transaksi->tipe_transaksi == "PERSEN") {
            $nominal_promo = $total_harga * $promo_transaksi->diskon_persen / 100;
            $deskripsi     = 'Promo ' . $promo_transaksi->nama . ' Potongan ' . $promo_transaksi->diskon_persen . '%  (Rp. ' . FormatHelper::formatNumberToString($nominal_promo) . ')';
        } else {
            $nominal_promo = $promo_transaksi->diskon_nominal;
            $deskripsi     = 'Promo ' . $promo_transaksi->promo->nama . ' Potongan Rp. ' . FormatHelper::formatStringToNumber($nominal_promo);
        }

        $data = [
            'tipe_promo'         => 'TRANSAKSI',
            'id_promo_transaksi' => $id_promo_transaksi,
            'nominal_promo'      => $nominal_promo,
            'deskripsi'          => $deskripsi,
        ];

        $promo_pesanan_kasir
            ? $promo_pesanan_kasir->update($data)
            : PromoPesananKasir::create([
            'id_pesanan_kasir' => $this->pesanan_kasir->id,
        ] + $data);

    }

    public function setPromoTransaksiKasirOtomatis($total_harga, $id_profesi = null)
    {

        $promoTransaksi = PromoTransaksi::where('minimal_belanja', '<=', $total_harga)
            ->whereHas('promo', function ($q) use ($id_profesi) {
                $q->aktif()
                    ->where(function ($query) use ($id_profesi) {
                        $query->when($id_profesi, fn($q) =>
                            $q->whereDoesntHave('profesies') // promo umum
                                ->orWhereHas('profesies', fn($q2) => $q2->where('id_profesi', $id_profesi))
                        )->when(is_null($id_profesi), fn($q) =>
                            $q->whereDoesntHave('profesies')
                        );
                    });
            })
            ->orderByDesc('minimal_belanja')
            ->first();

        $promoPesananKasir = $this->pesanan_kasir->promo_pesanan_kasir()->whereNotNull('id_promo_transaksi')->first();
        // ❌ Tidak ada promo transaksi yang cocok
        if (! $promoTransaksi) {
            $promoPesananKasir?->delete();
            return;
        }
        if ($promoTransaksi->tipe_transaksi == "PERSEN") {
            $nominal_promo = $total_harga * $promoTransaksi->diskon_persen / 100;
            $deskripsi     = 'Promo ' . $promoTransaksi->nama . ' Potongan ' . $promoTransaksi->diskon_persen . '%  (' . $nominal_promo . ')';
        } else {
            $nominal_promo = $promoTransaksi->diskon_nominal;
            $deskripsi     = 'Promo ' . $promoTransaksi->nama . ' Potongan Rp. ' . FormatHelper::formatStringToNumber($nominal_promo);
        }

        $data = [
            'tipe_promo'         => 'TRANSAKSI',
            'id_promo_transaksi' => $promoTransaksi->id,
            'nominal_promo'      => $nominal_promo,
            'deskripsi'          => $deskripsi,
        ];

        $promoPesananKasir
            ? $promoPesananKasir->update($data)
            : PromoPesananKasir::create([
            'id_pesanan_kasir' => $this->pesanan_kasir->id,
        ] + $data);
        return $nominal_promo;
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
        $rasio      = $barang_pesanan->barang->calculateJumlahDalamSatuanUtama(1, $barang_pesanan->id_satuan);
        $harga_beli = $barang_pesanan->barang->harga_beli_default * $rasio;
        $komisi     = $barang_pesanan->sub_total - ($harga_beli * $barang_pesanan->jumlah_dalam_satuan_utama);

        DetailTransaksiBarang::create([
            'id_history_barang'  => $history_barang->id,
            'id_transaksi'       => $id_transaksi,
            'harga_beli'         => $harga_beli,
            'komisi'             => $komisi,
            'harga'              => $barang_pesanan->harga,
            'harga_satuan_utama' => $barang_pesanan->harga_satuan_utama,
            'total_harga'        => $barang_pesanan->sub_total,
        ]);

        $get_tgl_expired = $this->updateStokBarang($barang_pesanan->id_barang, $barang_pesanan->jumlah_dalam_satuan_utama);

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
            $data = $promo_pesanan_kasir->map(function ($item) use ($id_transaksi) {
                if ($item->tipe_promo == 'BARANG BONUS') {
                    $id_barang_bonus           = $item->promo_barang->id_barang_bonus;
                    $jumlah_dalam_satuan_utama = $item->promo_barang->barang_bonus->calculateJumlahDalamSatuanUtama($item->jumlah, $item->id_satuan);
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
}
