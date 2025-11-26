<?php
namespace App\Livewire\KasirCadangan\Helpers;

use App\Helpers\FormatHelper;
use App\Models\Promo\PromoBarang;
use App\Models\Promo\PromoTransaksi;
use Carbon\Carbon;

trait KasirHelper
{
    public function checkModeShift()
    {
        $currentHour = Carbon::now()->hour;
        return $currentHour < 14 ? 'PAGI' : 'MALAM';
    }

    public function hargaJualResep($hargaLama, $persen_resep)
    {
        return ceil($hargaLama + ($hargaLama * $persen_resep) / 100);
    }

    // public function getTotalJumlahDalamSatuanUtama($id_barang)
    // {
    //     return $this->pesanan_kasir->barang_pesanan_kasir->where('id_barang', $id_barang)->sum('jumlah_dalam_satuan_utama');
    // }

    public function getTotalJumlahDalamSatuanUtama($id_barang, $jumlah_dalam_satuan_utama = 0, $except_id = null)
    {
        //load terlebih dahulu barang_pesanan_kasirnya
        $total = $this->pesanan_kasir->barang_pesanan_kasir->where('id_barang', $id_barang)
            ->when($except_id, fn($c) => $c->where('id', '!=', $except_id))
            ->sum('jumlah_dalam_satuan_utama');

        return $total + $jumlah_dalam_satuan_utama;
    }

    public function getTotalHargaBarang($id_barang, $except_id = null)
    {
        //load terlebih dahulu barang_pesanan_kasirnya
        $total = $this->pesanan_kasir->barang_pesanan_kasir
            ->where('id_barang', $id_barang)
            ->when($except_id, fn($c) => $c->where('id', '!=', $except_id))
            ->sum('sub_total');

        return $total;
    }

    public function setNominalPromo($promoBarang, $harga_sub_total, $jumlah_beli)
    {

        if ($promoBarang->tipe == 'PERSEN') {
            $diskon    = $harga_sub_total * $promoBarang->diskon_persen / 100;
            $deskripsi = 'Promo ' . $promoBarang->barang->nama . ' Diskon (' . FormatHelper::formatNumberToString($promoBarang->diskon_persen) . '%) = Rp. ' . FormatHelper::formatNumberToString($diskon);
        } elseif ($promoBarang->tipe == 'HARGA') {
            $diskon = $promoBarang->diskon_nominal;
            if ($promoBarang->tipe_qty == 'KELIPATAN') {
                $kelipatan        = $promoBarang->barang->hitungJumlahDalamSatuanUtama($promoBarang->syarat_qty, $promoBarang->id_satuan);
                $jumlah_kelipatan = floor($jumlah_beli / $kelipatan);
                $diskon           = $jumlah_kelipatan * $diskon;
            }
            $deskripsi = 'Promo ' . $promoBarang->barang->nama . ' Potongan Harga Rp. ' . FormatHelper::formatNumberToString($diskon);
        }
        return [$diskon, $deskripsi];
    }

    public function applyPromoBarang($id_barang, $jumlah_dalam_satuan_utama = 0, $sub_total = 0, $id_promo_barang = null)
    {
        // ----------------------------------------------------
        // 1. HAPUS PROMO
        // ----------------------------------------------------
        if (! $id_promo_barang) {
            $promoLama = $this->pesanan_kasir->promo_pesanan_kasir?->filter(fn($p) => $p->promo_barang?->id_barang == $id_barang);
            if ($promoLama->count() >= 0) {
                $promoLama->each->delete(); // QUERY 1
            }
            return;
        }

        // ----------------------------------------------------
        // 2. Ambil promo (1 query)
        // ----------------------------------------------------
        $promoBarang = PromoBarang::with(['barang.opsi_satuan', 'barang_bonus.opsi_satuan', 'satuan_bonus'])->find($id_promo_barang);
        // QUERY 2
        if (! $promoBarang) {
            return;
        }

        // ----------------------------------------------------
        // 3. Ambil total harga & total jumlah
        // ----------------------------------------------------
        $total_harga = $sub_total;

        $total_jumlah = $jumlah_dalam_satuan_utama;
        if ($promoBarang->syarat_qty_dalam_satuan_utama > $total_jumlah) {
            $this->pesanan_kasir->promo_pesanan_kasir()->whereHas('promo_barang', fn($c) => $c->where('id_barang', $id_barang))->delete();
            $this->pesanan_kasir->setRelation(
                'promo_pesanan_kasir',
                $this->pesanan_kasir->promo_pesanan_kasir->reject(function ($item) use ($id_barang) {
                    return $item->promo_barang?->id_barang == $id_barang;
                })->values()
            );
            $this->dispatch('alert-info', message: 'Jumlah barang tidak mencukupi untuk promo ini', status: 'warning', subtitle: 'silahkan tambahkan jumlah pesanan');
            return [
                'success' => true,
                'error'   => 'Jumlah barang tidak mencukupi untuk promo ini',
            ];
        }

        $tipe_promo    = null;
        $nominal_promo = null;
        $jumlah_bonus  = null;
        $deskripsi     = null;

        // ----------------------------------------------------
        // 4. Hitung promo DISKON (tanpa query)
        // ----------------------------------------------------
        if ($promoBarang->tipe == 'PERSEN' || $promoBarang->tipe == 'HARGA') {
            $tipe_promo                  = 'BARANG DISKON';
            [$nominal_promo, $deskripsi] = $this->setNominalPromo($promoBarang, $total_harga, $total_jumlah);
        }
        // ----------------------------------------------------
        // 5. Hitung promo BONUS (tanpa query)
        // ----------------------------------------------------
        elseif ($promoBarang->tipe == 'BARANG BONUS') {

            $tipe_promo   = 'BARANG BONUS';
            $jumlah_bonus = $promoBarang->bonus_qty;
            if ($promoBarang->tipe_qty == 'KELIPATAN') {
                $kelipatan    = $promoBarang->barang_bonus->hitungJumlahDalamSatuanUtama($promoBarang->syarat_qty, $promoBarang->id_satuan);
                $jumlah_bonus = floor($total_jumlah / $kelipatan) * $promoBarang->bonus_qty;
            }
            $deskripsi = "FREE {$promoBarang->barang_bonus->nama} [{$jumlah_bonus} / {$promoBarang->satuan_bonus->nama}]";

            // CEK STOK BONUS (tanpa query)
            $bonus_dalam_satuan_utama = $promoBarang->barang_bonus->hitungJumlahDalamSatuanUtama($jumlah_bonus, $promoBarang->id_satuan_bonus);
            if ($bonus_dalam_satuan_utama > $promoBarang->barang_bonus->stok) {
                return [
                    'success' => false,
                    'error'   => 'Stok barang bonus tidak cukup.',
                ];
            }
        }

        // ----------------------------------------------------
        // 6. Hapus promo lama (tanpa query)
        // ----------------------------------------------------
        $items = $this->pesanan_kasir->promo_pesanan_kasir?->filter(fn($item) => ($item->promo_barang?->id_barang == $id_barang && $item->id_promo_barang != $id_promo_barang));
        if ($items->count() >= 0) {
            $items->each->delete(); // QUERY 3
        }

        // $this->pesanan_kasir->promo_pesanan_kasir()->where('id_promo_barang', $id_promo_barang)->first();
        $pesanan_kasir = $this->pesanan_kasir->promo_pesanan_kasir->filter(fn($item) => $item->id_promo_barang == $id_promo_barang)->first();
        $dataPromo     = [
            'id_pesanan_kasir' => $this->pesanan_kasir->id,
            'id_promo_barang'  => $id_promo_barang,
            'tipe_promo'       => $tipe_promo,
            'nominal_promo'    => $nominal_promo ?? null,
            'jumlah'           => $jumlah_bonus ?? null,
            'id_satuan'        => $promoBarang->id_satuan_bonus ?? null,
            'deskripsi'        => $deskripsi,
        ];

        if ($pesanan_kasir) {
            $hasil  = $pesanan_kasir->update($dataPromo);
            $status = 'updated';
        } else {
            // $hasil = PromoPesananKasir::create($dataPromo);
            $hasil = $this->pesanan_kasir->promo_pesanan_kasir()->create($dataPromo);
            $hasil->load('promo_barang');
            // $this->pesanan_kasir->load('promo_pesanan_kasir.promo_barang');
            $this->pesanan_kasir->setRelation('promo_pesanan_kasir', $this->pesanan_kasir->promo_pesanan_kasir->push($hasil));
            $status = 'created';
        }

        return [
            'success' => true,
            'promo'   => $hasil,
            'status'  => $status,
        ];
    }

    public function applyPromoTransaksi($id_promo_transaksi = null, $sub_total = 0)
    {
        // ----------------------------------------------------
        // 1. HAPUS PROMO
        // ----------------------------------------------------

        if (! $id_promo_transaksi) {
            $promoLama = $this->pesanan_kasir->promo_pesanan_kasir->whereNotNull('id_promo_transaksi')->each->delete();
            $this->pesanan_kasir->setRelation(
                'promo_pesanan_kasir',
                $this->pesanan_kasir->promo_pesanan_kasir->reject(function ($item) {
                    return $item->id_promo_transaksi != null;
                })->values()
            );
            return;
        }
        // ----------------------------------------------------
        // 2. Ambil promo (1 query)
        // ----------------------------------------------------
        $promo_transaksi = PromoTransaksi::query()
            ->select('promo.nama as nama_promo', 'promo_transaksi.*')
            ->join('promo', 'promo.id', '=', 'promo_transaksi.id_promo')
            ->find($id_promo_transaksi);
        // QUERY 2
        if (! $promo_transaksi) {
            return;
        }

        // ----------------------------------------------------
        // 3. Antisipasi total transaksi < minimal belanja
        // ----------------------------------------------------
        if ($promo_transaksi->minimal_belanja > $sub_total) {
            $promoLama = $this->pesanan_kasir->promo_pesanan_kasir->whereNotNull('id_promo_transaksi')->each->delete();
            $this->pesanan_kasir->setRelation(
                'promo_pesanan_kasir',
                $this->pesanan_kasir->promo_pesanan_kasir->reject(function ($item) {
                    return $item->id_promo_transaksi != null;
                })->values()
            );
            $this->dispatch('alert-info', message: 'Total Transaksi tidak mencukupi untuk promo ini', status: 'warning', subtitle: 'silahkan tambahkan pesanan');
            return [
                'success' => true,
                'error'   => 'Total transaksi tidak mencukupi untuk promo ini',
            ];
        }

        if ($promo_transaksi->tipe_transaksi == "PERSEN") {
            $nominal_promo = $sub_total * $promo_transaksi->diskon_persen / 100;
            $deskripsi     = 'Promo ' . $promo_transaksi->nama_promo . ' Potongan ' . $promo_transaksi->diskon_persen . '%  (Rp. ' . FormatHelper::formatNumberToString($nominal_promo) . ')';
        } else {
            $nominal_promo = $promo_transaksi->diskon_nominal;
            $deskripsi     = 'Promo ' . $promo_transaksi->nama_promo . ' Potongan Rp. ' . FormatHelper::formatStringToNumber($nominal_promo);
        }

        $promo_pesanan_saat_ini = $this->pesanan_kasir->promo_pesanan_kasir->where('id_promo_transaksi', $id_promo_transaksi)->first();
        $dataPromo              = [
            'tipe_promo'         => 'TRANSAKSI',
            'id_promo_transaksi' => $id_promo_transaksi,
            'nominal_promo'      => $nominal_promo,
            'deskripsi'          => $deskripsi,
        ];

        if ($promo_pesanan_saat_ini) {
            $hasil  = $promo_pesanan_saat_ini->update($dataPromo);
            $status = 'updated';
        } else {
            $hasil = $this->pesanan_kasir->promo_pesanan_kasir()->create($dataPromo);
            $hasil->load('promo_transaksi');
            $this->pesanan_kasir->setRelation('promo_pesanan_kasir', $this->pesanan_kasir->promo_pesanan_kasir->push($hasil));
            $status = 'created';
        }

        return [
            'success' => true,
            'promo'   => $hasil,
            'status'  => $status,
        ];

    }

    public function sinkronPromoTransaksi($id_promo_transaksi_selected)
    {
        $sub_total    = $this->pesanan_kasir->barang_pesanan_kasir->sum('sub_total');
        $promo_barang = $this->pesanan_kasir->promo_pesanan_kasir->whereNull('id_promo_transaksi')->sum('nominal_promo');
        $sub_total    = $sub_total - $promo_barang;
        return $this->applyPromoTransaksi($id_promo_transaksi_selected, $sub_total);
    }

    public function getAllSatuan($data_barang)
    {
        return collect([
            [
                'id_satuan' => $data_barang['id_satuan_utama'],
                // 'satuan'    => $data_barang['satuan_utama']['nama'],
                'rasio'     => 1,
            ],
        ])->merge(
            collect($data_barang['opsi_satuan'])->map(function ($item) {
                return [
                    'id_satuan' => $item['id'],
                    // 'satuan'    => $item['nama'],
                    'rasio'     => $item['pivot']['rasio_satuan_utama'] / $item['pivot']['rasio_satuan_opsi'],
                ];
            })
        )->values()->toArray();
    }

    public function calculateJumlahDalamSatuanUtama($data_barang, $jumlah, $id_satuan = null)
    {
        $id_satuan = $id_satuan ?? $data_barang['id_satuan_utama'];
        $item      = collect($this->getAllSatuan($data_barang))->first(fn($i) => $i['id_satuan'] == $id_satuan);
        return ($item ? $item['rasio'] ?? 1 : 1) * $jumlah;
    }

    //barang, opsi_satuan, opsi_harga
    public function getHargaJual($data_barang, $jumlah_dalam_satuan_utama = null, $id_satuan = null)
    {
        $id_satuan = $id_satuan ?? $data_barang['id_satuan_utama'];

        if (! $jumlah_dalam_satuan_utama) {
            $jumlah_dalam_satuan_utama = $this->calculateJumlahDalamSatuanUtama($data_barang, 1, $id_satuan);
        }
        $opsi_harga = collect($data_barang['opsi_harga'])
            ->map(function ($item) use ($data_barang, $id_satuan) {
                if (is_object($item)) {
                    $item = $item->toArray();
                }
                return array_merge($item, [
                    'jumlah_dalam_satuan_utama' => $this->calculateJumlahDalamSatuanUtama(
                        $data_barang,
                        $item['jumlah_min'],
                        $item['id_satuan']
                    ),
                ]);
            })
            ->concat([
                [
                    'jumlah_dalam_satuan_utama' => 1,
                    'harga'                     => $data_barang['harga_jual_utama'],
                    'harga_satuan_utama'        => $data_barang['harga_jual_utama'],
                ],
            ])
            ->sortBy('jumlah_dalam_satuan_utama')
            ->values();
        $harga_terpilih = $opsi_harga
            ->filter(fn($i) => $i['jumlah_dalam_satuan_utama'] <= $jumlah_dalam_satuan_utama)
            ->last();

        $harga_satuan_utama = ceil($harga_terpilih['harga_satuan_utama'] ?? $data_barang['harga_jual_utama']);

        //mengambil harga berdasarkan jumlah rasionya
        $harga = ceil($this->calculateJumlahDalamSatuanUtama($data_barang, $harga_satuan_utama, $id_satuan));

        return [
            $harga,
            $harga_satuan_utama,
        ];
    }

    private function syncFrontEnd()
    {
        $this->list_total_jumlah_dalam_satuan_utama =
        $this->pesanan_kasir->barang_pesanan_kasir
            ->groupBy('id_barang')
            ->map(fn($items) => $items->sum('jumlah_dalam_satuan_utama'))
            ->toArray();

        $this->dispatch('barang_pesanan_updated',
            list_total_jumlah_dalam_satuan_utama: $this->list_total_jumlah_dalam_satuan_utama,
            pesanan_kasir: $this->pesanan_kasir->toArray(),
        );
    }

    private function totalJumlahBarangDalamSatuanUtama($barang, $jumlah_dalam_satuan_utama = 0, $except_id_barang_pesanan = null)
    {
        return $this->pesanan_kasir->barang_pesanan_kasir
            ->where('id_barang', $barang['id'])
            ->when($except_id_barang_pesanan, fn($q) => $q->where('id', '!=', $except_id_barang_pesanan))
            ->sum('jumlah_dalam_satuan_utama') + $jumlah_dalam_satuan_utama;
    }

    private function totalHargaBarang($id_barang, $except_id_barang_pesanan = null)
    {
        return $this->pesanan_kasir->barang_pesanan_kasir
            ->where('id_barang', $id_barang)
            ->when($except_id_barang_pesanan, fn($q) => $q->where('id', '!=', $except_id_barang_pesanan))
            ->sum('sub_total');
    }

    public function getIdPromoBarangSelected($id_barang)
    {
        return $this->pesanan_kasir->promo_pesanan_kasir
            ->filter(fn($promo) => $promo->promo_barang?->id_barang == $id_barang)
            ->first()?->promo_barang?->id;
    }

}
