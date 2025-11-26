<?php
namespace App\Livewire\KasirCadangan;

use App\Helpers\FormatHelper;
use App\Livewire\KasirCadangan\Helpers\BarangPesananHelper;
use App\Livewire\KasirCadangan\Helpers\KasirHelper;
use App\Models\Kasir\PesananKasir;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;
use Livewire\Component;

class BarangPesananList extends Component
{
    use KasirHelper, BarangPesananHelper;
    public $pesanan_kasir, $persen_resep, $mode_kasir;
    public $list_total_jumlah_dalam_satuan_utama;
    public $barang_pesanan_kasir, $promo_pesanan_kasir;
    public $ceklistDeleteBarang = [];
    public $promo               = [];
    public $jumlah_barang_input = [], $jumlah_barang_input_dalam_satuan_utama = [], $total_jumlah_dalam_satuan_utama_by_barang = [];
    public $id_member;

    public function mount()
    {

        $this->barang_pesanan_kasir = $this->pesanan_kasir->barang_pesanan_kasir->toArray();
        $this->promo_pesanan_kasir  = $this->pesanan_kasir->promo_pesanan_kasir->toArray();
        $this->id_member            = $this->pesanan_kasir->id_member;
        $this->mode_kasir           = $this->pesanan_kasir->mode_kasir;
    }

    public function updatePesananBarang($id_barang_pesanan)
    {
        try {
            $this->jumlah_barang_input[$id_barang_pesanan] = FormatHelper::formatStringToNumber($this->jumlah_barang_input[$id_barang_pesanan]);
            $this->validate([
                'jumlah_barang_input.' . $id_barang_pesanan => 'required|numeric|min:1',
            ], ['required' => 'Kolom Ini Wajib Diisi', 'numeric' => 'Inputan Harus Angka', 'min' => 'Minimal 1']);
            DB::beginTransaction();
            $jumlah_input = $this->jumlah_barang_input[$id_barang_pesanan];

            $this->pesanan_kasir->refresh()->loadMissing([
                'promo_pesanan_kasir.promo_barang',
                'barang_pesanan_kasir.barang.opsi_satuan',
                'barang_pesanan_kasir.barang.satuan_utama',
                'barang_pesanan_kasir.barang.opsi_harga.satuan:id,nama',
                // 'barang_pesanan_kasir.barang.opsi_harga.barang:id,id_satuan_utama',
                // 'barang_pesanan_kasir.barang.opsi_harga.barang.opsi_satuan:id',
                'barang_pesanan_kasir.satuan',
            ]);
            $barang_pesanan = $this->pesanan_kasir->barang_pesanan_kasir->where('id', $id_barang_pesanan)->first();

            $total_jumlah_keseluruhan_before  = $this->pesanan_kasir->barang_pesanan_kasir->where('id_barang', $barang_pesanan->id_barang)->sum('jumlah_dalam_satuan_utama');
            $jumlah_dalam_satuan_utama_before = $barang_pesanan->jumlah_dalam_satuan_utama;
            $jumlah_dalam_satuan_utama_after  = $this->calculateJumlahDalamSatuanUtama($barang_pesanan->barang, $jumlah_input, $barang_pesanan->id_satuan);

            $total_jumlah = $total_jumlah_keseluruhan_before - $jumlah_dalam_satuan_utama_before + $jumlah_dalam_satuan_utama_after;

            if (! $this->cekStokBarang($total_jumlah, $barang_pesanan->barang->stok)) {
                DB::rollBack();
                return ['status' => 'error'];
            }

            [$harga_jual, $harga_satuan_utama] = $this->getHargaJual($barang_pesanan->barang, $total_jumlah, $barang_pesanan->id_satuan);
            if ($this->mode_kasir == 'RESEP') {
                $harga_jual         = $this->hargaJualResep($harga_jual, $this->persen_resep);
                $harga_satuan_utama = $this->hargaJualResep($harga_satuan_utama, $this->persen_resep);
            }

            $subtotal = $jumlah_input * $harga_jual;
            $barang_pesanan->update([
                'jumlah'             => $jumlah_input,
                'harga'              => $harga_jual,
                'harga_satuan_utama' => $harga_satuan_utama,
                'sub_total'          => $subtotal,
            ]);

            // update harga satuan utama dan subtotal pada pesanan lain
            $this->updateHargaPesananLain($barang_pesanan->barang, $id_barang_pesanan, $harga_satuan_utama);

            $total_by_barang   = $this->pesanan_kasir->barang_pesanan_kasir->where('id_barang', $barang_pesanan->id_barang)->sum('sub_total');
            $id_promo_selected = $this->getIdPromoBarangSelected($barang_pesanan->id_barang);
            $promo             = $this->applyPromoBarang($barang_pesanan->id_barang, $total_jumlah, $total_by_barang, $id_promo_selected);
            if ($promo && ! $promo['success']) {
                $this->dispatch('alert', message: $promo['error'], status: 'error');
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
            // dd($promo);
            $this->pesanan_kasir->update([
                'total_harga' => $this->pesanan_kasir->barang_pesanan_kasir->sum('sub_total'),
            ]);
            DB::commit();

            $this->syncFrontEnd();
            if ($promo && ($promo['success'] ?? false) && ($promo['error'] ?? false)) {
                return ['status' => 'success'];
            } else {
                $this->dispatch('alert', message: 'Barang berhasil ditambahkan.', status: 'success');
                return ['status' => 'success'];
            }
        } catch (\Exception $e) {
            $this->jumlah_barang_input[$id_barang_pesanan] = FormatHelper::formatNumberToString($this->jumlah_barang_input[$id_barang_pesanan]);
            $this->dispatch('alert', message: $e->getMessage(), status: 'error');
            throw $e;
        }
    }

    public function updatePromoBarang($id_promo_barang, $id_barang, $total_jumlah, $total_harga_barang)
    {

        $this->pesanan_kasir = PesananKasir::query()
            ->with([
                'promo_pesanan_kasir.promo_barang',
            ])
            ->find($this->pesanan_kasir['id']);
        try {
            DB::BeginTransaction();
            $promo = $this->applyPromoBarang($id_barang, $total_jumlah, $total_harga_barang, $id_promo_barang);
            // dd($promo);
            if ($promo && ! $promo['success']) {
                $this->dispatch('alert', message: $promo['error'], status: 'error');
                $this->dispatch('focus-input-jumlah');
                DB::rollback();
                return;
            }

            $this->pesanan_kasir->load([
                'promo_pesanan_kasir.promo_barang',
                // 'promo_pesanan_kasir.promo_transaksi',
                'barang_pesanan_kasir.satuan',
                'barang_pesanan_kasir.barang.opsi_satuan',
                'barang_pesanan_kasir.barang.satuan_utama',
                'barang_pesanan_kasir.barang.opsi_harga.satuan:id,nama',
                // 'barang_pesanan_kasir.barang.opsi_harga.barang:id,id_satuan_utama',
                // 'barang_pesanan_kasir.barang.opsi_harga.barang.opsi_satuan:id',
            ]);
            $id_promo_transaksi_selected = $this->pesanan_kasir->promo_pesanan_kasir->whereNotNull('id_promo_transaksi')->first()?->id_promo_transaksi ?? null;
            $promo                       = $this->sinkronPromoTransaksi($id_promo_transaksi_selected);
            if ($promo && ! $promo['success']) {
                $this->dispatch('alert', message: $promo['error'], status: 'error');
                DB::rollback();
                return;
            }
            DB::commit();
            $this->syncFrontEnd();
            $this->dispatch('alert', message: 'Berhasil Mengubah Promo Barang', status: 'success');
        } catch (\Exception $e) {
            DB::rollBack();
            // dd($e);
            $this->dispatch('alert', message: $e->getMessage(), status: 'error');
        }
    }

    public function render()
    {

        return view('livewire.kasir-cadangan.barang-pesanan-list');
    }

    public function deleteAllPesananBarang()
    {
        DB::BeginTransaction();
        $this->pesanan_kasir->barang_pesanan_kasir()->delete();
        $this->pesanan_kasir->promo_pesanan_kasir()->whereNotNull('id_promo_barang')->delete();
        // $this->updateTotalHarga();
        $this->pesanan_kasir->update([
            'total_harga' => $this->pesanan_kasir->barang_pesanan_kasir->sum('sub_total'),
        ]);
        DB::commit();
        $this->syncFrontEnd();
        $this->dispatch('alert', message: 'Semua Barang Berhasil Dihapus', status: 'success');
    }

    public function deleteWithSelectedItem()
    {
        $this->pesanan_kasir->refresh()->loadMissing([
            'promo_pesanan_kasir.promo_barang',
            'barang_pesanan_kasir.barang.opsi_satuan',
            'barang_pesanan_kasir.barang.opsi_harga',
        ]);
        $barang_pesanan_selected = $this->pesanan_kasir->barang_pesanan_kasir->whereIn('id', $this->ceklistDeleteBarang)->values();
        $barang_pesanan_selected->each(function ($barang_pesanan) {
            $masih_ada = $this->pesanan_kasir->barang_pesanan_kasir
                ->where('id_barang', $barang_pesanan->id_barang)
                ->where('id', '!=', $barang_pesanan->id) // selain yang dihapus
                ->isNotEmpty();
            // dd($masih_ada);
            if (! $masih_ada) {
                $this->pesanan_kasir->promo_pesanan_kasir()
                    ->whereHas('promo_barang', function ($q) use ($barang_pesanan) {
                        $q->where('id_barang', $barang_pesanan->id_barang);
                    })->delete();
            } else {
                $total_jumlah                      = $this->pesanan_kasir->barang_pesanan_kasir->where('id_barang', $barang_pesanan->id_barang)->where('id', '!=', $barang_pesanan->id)->sum('jumlah_dalam_satuan_utama');
                [$harga_jual, $harga_satuan_utama] = $this->getHargaJual($barang_pesanan->barang, $total_jumlah, $barang_pesanan->id_satuan);
                if ($this->mode_kasir == 'RESEP') {
                    $harga_jual         = $this->hargaJualResep($harga_jual, $this->persen_resep);
                    $harga_satuan_utama = $this->hargaJualResep($harga_satuan_utama, $this->persen_resep);
                }
                $this->updateHargaPesananLain($barang_pesanan->barang, $barang_pesanan->id, $harga_satuan_utama);
                $total_harga = $this->pesanan_kasir->barang_pesanan_kasir->where('id_barang', $barang_pesanan->id_barang)->where('id', '!=', $barang_pesanan->id)->sum('sub_total');

                $id_promo_selected = $this->getIdPromoBarangSelected($barang_pesanan->id_barang);
                $promo             = $this->applyPromoBarang($barang_pesanan->id_barang, $total_jumlah, $total_harga, $id_promo_selected);
                if ($promo && ! $promo['success']) {
                    $this->dispatch('alert', message: $promo['error'], status: 'error');
                    return;
                } elseif ($promo && $promo['success'] && ! empty($promo['error'])) {
                    $this->pesanan_kasir->promo_pesanan_kasir()
                        ->whereHas('promo_barang', function ($q) use ($barang_pesanan) {
                            $q->where('id_barang', $barang_pesanan->id_barang);
                        })->delete();
                }
            }

            $barang_pesanan->delete();
        });
        $this->pesanan_kasir->load([
            'promo_pesanan_kasir.promo_barang',
            'barang_pesanan_kasir.satuan:id,nama',
            'barang_pesanan_kasir.barang.opsi_satuan',
            'barang_pesanan_kasir.barang.satuan_utama',
            'barang_pesanan_kasir.barang.opsi_harga.satuan',
            // 'barang_pesanan_kasir.barang.opsi_harga.barang:id,id_satuan_utama',
            // 'barang_pesanan_kasir.barang.opsi_harga.barang.opsi_satuan:id',
        ]);
        $this->ceklistDeleteBarang = [];
        $this->pesanan_kasir->update([
            'total_harga' => $this->pesanan_kasir->barang_pesanan_kasir()->sum('sub_total'),
        ]);
        $this->syncFrontEnd();
        $this->dispatch('alert', message: 'Barang yang Di Pilih Berhasil Dihapus', status: 'success');
    }

    #[On('delete-barang-pesanan-kasir')]
    public function destroy($id) // id_barang_pesanan
    {
        // dd($this->ceklistDeleteBarang);
        $this->pesanan_kasir->refresh()->loadMissing([
            'promo_pesanan_kasir.promo_barang',
            'barang_pesanan_kasir.barang.opsi_satuan',
            'barang_pesanan_kasir.barang.opsi_harga',
        ]);
        $barang_pesanan = $this->pesanan_kasir->barang_pesanan_kasir->findOrFail($id);
        if (Auth::check() && (Auth::user()->hasRole('admin') || $this->pesanan_kasir->id_user == Auth::user()->id)) {
            DB::BeginTransaction();
            $masih_ada = $this->pesanan_kasir->barang_pesanan_kasir
                ->where('id_barang', $barang_pesanan->id_barang)
                ->where('id', '!=', $id) // selain yang dihapus
                ->isNotEmpty();
            if (! $masih_ada) {
                $this->pesanan_kasir->promo_pesanan_kasir()
                    ->whereHas('promo_barang', function ($q) use ($barang_pesanan) {
                        $q->where('id_barang', $barang_pesanan->id_barang);
                    })->delete();
            } else {
                $total_jumlah                      = $this->pesanan_kasir->barang_pesanan_kasir->where('id_barang', $barang_pesanan->id_barang)->where('id', '!=', $barang_pesanan->id)->sum('jumlah_dalam_satuan_utama');
                [$harga_jual, $harga_satuan_utama] = $this->getHargaJual($barang_pesanan->barang, $total_jumlah, $barang_pesanan->id_satuan);
                if ($this->mode_kasir == 'RESEP') {
                    $harga_jual         = $this->hargaJualResep($harga_jual, $this->persen_resep);
                    $harga_satuan_utama = $this->hargaJualResep($harga_satuan_utama, $this->persen_resep);
                }
                $this->updateHargaPesananLain($barang_pesanan->barang, $barang_pesanan->id, $harga_satuan_utama);
                $total_harga = $this->pesanan_kasir->barang_pesanan_kasir->where('id_barang', $barang_pesanan->id_barang)->where('id', '!=', $barang_pesanan->id)->sum('sub_total');

                $id_promo_selected = $this->getIdPromoBarangSelected($barang_pesanan->id_barang);
                $promo             = $this->applyPromoBarang($barang_pesanan->id_barang, $total_jumlah, $total_harga, $id_promo_selected);
                if ($promo && ! $promo['success']) {
                    $this->dispatch('alert', message: $promo['error'], status: 'error');
                    DB::rollback();
                    return;
                } elseif ($promo && $promo['success'] && ! empty($promo['error'])) {
                    $this->pesanan_kasir->promo_pesanan_kasir()
                        ->whereHas('promo_barang', function ($q) use ($barang_pesanan) {
                            $q->where('id_barang', $barang_pesanan->id_barang);
                        })->delete();
                }
            }

            $this->pesanan_kasir->barang_pesanan_kasir()->findOrFail($id)->delete();

            $this->pesanan_kasir->load([
                'promo_pesanan_kasir.promo_barang',
                'barang_pesanan_kasir.satuan:id,nama',
                'barang_pesanan_kasir.barang.opsi_satuan',
                'barang_pesanan_kasir.barang.satuan_utama',
                'barang_pesanan_kasir.barang.opsi_harga.satuan',
                // 'barang_pesanan_kasir.barang.opsi_harga.barang:id,id_satuan_utama',
                // 'barang_pesanan_kasir.barang.opsi_harga.barang.opsi_satuan:id',
            ]);

            $this->pesanan_kasir->update([
                'total_harga' => $this->pesanan_kasir->barang_pesanan_kasir->sum('sub_total'),
            ]);
            // dd($this->pesanan_kasir->toArray());
            DB::commit();

            $this->syncFrontEnd();
            $this->dispatch('alert', message: 'Berhasil Menghapus Barang Pesanan Kasir');
        } else {
            $this->dispatch('alert', message: 'Kamu Tidak Memiliki Akses', status: 'error');
        }
    }

    // #[On('barang_pesanan_updated')]
    // public function barang_pesanan_updated($list_total_jumlah_dalam_satuan_utama, $pesanan_kasir)
    // {
    //     $this->list_total_jumlah_dalam_satuan_utama = $list_total_jumlah_dalam_satuan_utama;
    //     // dd($pesanan_kasir);
    //     $this->pesanan_kasir        = $pesanan_kasir;
    //     $this->barang_pesanan_kasir = $this->pesanan_kasir['barang_pesanan_kasir'] ?? [];
    //     $this->promo_pesanan_kasir  = $this->pesanan_kasir['promo_pesanan_kasir'] ?? [];

    //     $this->setPromo();
    //     $this->combinePesananAndPromo();
    // }

    // public function setPromo()
    // {
    //     $this->promo = collect($this->promo_pesanan_kasir)
    //         ->filter(fn($p) => ! empty($p['promo_barang']['id_barang'] ?? null))
    //         ->keyBy(fn($p) => $p['promo_barang']['id_barang']);
    // }

    // public function combinePesananAndPromo()
    // {
    //     $this->barang_pesanan_kasir = collect($this->barang_pesanan_kasir)->map(function ($item, $index) {
    //         $next = $this->barang_pesanan_kasir[$index + 1] ?? null;

    //         $item['promo_barang'] = (! $next || $next['id_barang'] !== $item['id_barang'])
    //             ? ($this->promo[$item['id_barang']] ?? null)
    //             : null;

    //         return $item;
    //     })->toArray();
    // }
}
