<?php
namespace App\Livewire\KasirCadangan;

use App\Helpers\FormatHelper;
use App\Livewire\KasirCadangan\Helpers\BarangPesananHelper;
use App\Livewire\KasirCadangan\Helpers\KasirHelper;
use App\Models\Barang\Barang;
use App\Models\DataMaster\Satuan;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class BarangPesananForm extends Component
{
    use KasirHelper, BarangPesananHelper;
    public $pesanan_kasir, $persen_resep;
    public $id_barang, $jumlah, $id_satuan, $harga_jual, $harga_satuan_utama, $sub_total, $id_promo_selected, $id_list_promo;
    public $list_total_jumlah_dalam_satuan_utama, $jumlah_dalam_satuan_utama, $total_jumlah_dalam_satuan_utama_by_barang;
    public $data_barang;

    public function getDataBarang($idBarang)
    {
        $this->pesanan_kasir->loadMissing(['promo_pesanan_kasir.promo_barang']);
        $this->id_promo_selected = $this->pesanan_kasir->promo_pesanan_kasir
            ->filter(fn($promo) => $promo->promo_barang?->id_barang == $idBarang)
            ->first()?->id_promo_barang;
        $this->dispatch('selectedPromoBarang', idPromoBarang: $this->id_promo_selected);
        return Barang::query()
            ->with(['satuan_utama', 'opsi_satuan', 'opsi_harga.satuan'])
            ->where('id', $idBarang)
            ->first();
    }

    public function addBarangPesananKasir()
    {
        try {
            $this->jumlah = FormatHelper::formatStringToNumber($this->jumlah);
            $this->validate(
                [
                    'id_barang' => 'required|exists:barang,id',
                    'jumlah'    => 'required|numeric|min:1',
                    'id_satuan' => 'required|exists:satuan,id',
                ],
                [
                    'jumlah.min' => 'Minimal pembelian 1 item.',
                ],
            );
            DB::beginTransaction();

            $this->pesanan_kasir->refresh()->loadMissing([
                'promo_pesanan_kasir.promo_barang',
                'barang_pesanan_kasir.barang.opsi_satuan',
                'barang_pesanan_kasir.barang.satuan_utama',
                'barang_pesanan_kasir.barang.opsi_harga.satuan:id,nama',
                // 'barang_pesanan_kasir.barang.opsi_harga.barang:id,id_satuan_utama',
                // 'barang_pesanan_kasir.barang.opsi_harga.barang.opsi_satuan:id',
                'barang_pesanan_kasir.satuan',
            ]);
            $existingBarang = $this->pesanan_kasir->barang_pesanan_kasir->firstWhere('id_barang', $this->id_barang);
            if ($existingBarang) {
                $data_barang = $existingBarang->barang;
            } else {
                $data_barang = Barang::with(['opsi_satuan', 'opsi_harga', 'satuan_utama'])->find($this->id_barang);
            }

            $total_jumlah_keseluruhan_before = $this->pesanan_kasir->barang_pesanan_kasir->where('id_barang', $this->id_barang)->sum('jumlah_dalam_satuan_utama');
            $jumlah_dalam_satuan_utama       = $this->calculateJumlahDalamSatuanUtama($data_barang, $this->jumlah, $this->id_satuan);

            $total_jumlah = $total_jumlah_keseluruhan_before + $jumlah_dalam_satuan_utama;

            if (! $this->cekStokBarang($total_jumlah, $data_barang->stok)) {
                $this->jumlah = FormatHelper::formatNumberToString($this->jumlah);
                $this->dispatch('focus-input-jumlah');
                DB::rollBack();
                return;
            }

            [$this->harga_jual, $this->harga_satuan_utama] = $this->getHargaJual($data_barang, $total_jumlah, $this->id_satuan);
            if ($this->pesanan_kasir->mode_kasir == 'RESEP') {
                $this->harga_jual         = $this->hargaJualResep($this->harga_jual, $this->persen_resep);
                $this->harga_satuan_utama = $this->hargaJualResep($this->harga_satuan_utama, $this->persen_resep);
            }

            $barang_pesanan    = $this->pesanan_kasir->barang_pesanan_kasir->where('id_barang', $this->id_barang)->where('id_satuan', $this->id_satuan)->first();
            $id_barang_pesanan = $barang_pesanan ? $this->updateExistingBarangPesanan($barang_pesanan) : $this->createNewBarangPesanan($data_barang);

            $this->updateHargaPesananLain($data_barang, $id_barang_pesanan, $this->harga_satuan_utama);

            // $pesanans          = $this->pesanan_kasir->barang_pesanan_kasir->where('id_barang', $this->id_barang)->where('id', '!=', $id_barang_pesanan)->values();
            // if ($pesanans->count() > 0) {
            //     foreach ($pesanans as $pesanan) {
            //         $harga = $this->calculateJumlahDalamSatuanUtama($data_barang, $this->harga_satuan_utama, $pesanan->id_satuan);
            //         $pesanan->update([
            //             'harga'              => $harga,
            //             'harga_satuan_utama' => $this->harga_satuan_utama,
            //             'sub_total'          => $pesanan->jumlah * $harga,
            //         ]);
            //     }
            // }
            // $all_sub_total = $pesanans->sum('sub_total') + ($this->jumlah * $this->harga_jual);

            $all_sub_total = $this->pesanan_kasir->barang_pesanan_kasir->where('id_barang', $this->id_barang)->sum('sub_total');
            $promo         = $this->applyPromoBarang($this->id_barang, $total_jumlah, $all_sub_total, $this->id_promo_selected);
            if ($promo && ! $promo['success']) {
                $this->dispatch('alert', message: $promo['error'], status: 'error');
                $this->dispatch('focus-input-jumlah');
                DB::rollback();
                return;
            }
            $id_promo_transaksi_selected = $this->pesanan_kasir->promo_pesanan_kasir->whereNotNull('id_promo_transaksi')->first()?->id_promo_transaksi ?? null;
            $promo                       = $this->sinkronPromoTransaksi($id_promo_transaksi_selected);
            if ($promo && ! $promo['success']) {
                $this->dispatch('alert', message: $promo['error'], status: 'error');
                DB::rollback();
                return;
            }

            // 'promo_pesanan_kasir.promo_barang',
            // 'barang_pesanan_kasir.barang.opsi_satuan',
            // 'barang_pesanan_kasir.barang.satuan_utama',
            // 'barang_pesanan_kasir.barang.opsi_harga',
            // 'barang_pesanan_kasir.barang.opsi_harga.barang:id,id_satuan_utama',
            // 'barang_pesanan_kasir.barang.opsi_harga.barang.opsi_satuan:id',
            // 'barang_pesanan_kasir.satuan',

            // dd($this->pesanan_kasir);
            $this->pesanan_kasir->update([
                'total_harga' => $this->pesanan_kasir->barang_pesanan_kasir->sum('sub_total'),
            ]);
            // dd($this->pesanan_kasir);
            DB::commit();
            $this->dispatch('focus-select-barang');
            $this->syncFrontEnd();
            $this->dispatch('alert', message: 'Barang berhasil ditambahkan.', status: 'success');
        } catch (\Exception $e) {
            DB::rollback();
            $this->jumlah = FormatHelper::formatNumberToString($this->jumlah);
            $this->dispatch('alert', status: 'error', message: $e->getMessage());
            throw $e;
        }
    }

    private function updateExistingBarangPesanan($barang_pesanan)
    {
        $barang_pesanan->update([
            'jumlah'             => $barang_pesanan->jumlah + $this->jumlah,
            'harga'              => $this->harga_jual,
            'harga_satuan_utama' => $this->harga_satuan_utama,
            // 'id_promo_barang'           => $this->id_promo_barang,
            'sub_total'          => ($barang_pesanan->jumlah + $this->jumlah) * $this->harga_jual,
        ]);

        return $barang_pesanan->id;
    }

    private function createNewBarangPesanan($data_barang)
    {
        // $data = BarangPesananKasir::with('barang.opsi_satuan')->create([])
        $data = $this->pesanan_kasir->barang_pesanan_kasir()->create([
            'id_barang'          => $this->id_barang,
            'jumlah'             => $this->jumlah,
            'id_satuan'          => $this->id_satuan,
            'harga'              => $this->harga_jual,
            'harga_satuan_utama' => $this->harga_satuan_utama,
            'sub_total'          => $this->jumlah * $this->harga_jual,
        ]);
        // $data->load(['barang.opsi_satuan', 'satuan:id,nama', 'barang.opsi_harga', 'barang.satuan_utama']);
        $data->setRelation('barang', $data_barang);
        $data->setRelation('satuan', Satuan::select('id', 'nama')->find($this->id_satuan));
        $this->pesanan_kasir->setRelation('barang_pesanan_kasir',
            $this->pesanan_kasir->barang_pesanan_kasir->push($data));
        // ->sortBy([
        //         ['id_barang', 'asc'],
        //         ['updated_at', 'asc'],
        //     ])->values()
        return $data->id;
    }

    public function render()
    {
        return view('livewire.kasir-cadangan.barang-pesanan-form');
    }
}
