<?php
namespace App\Livewire\Kasir\BarangPesananKasir;

use App\Helpers\FormatHelper;
use App\Livewire\Kasir\Helpers\KasirHelper;
use App\Models\Kasir\BarangPesananKasir;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;
use Livewire\Component;

class BarangPesananKasirList extends Component
{
    use KasirHelper;
    public $pesanan_kasir, $barang_pesanan_kasir, $persen_resep;
    public $id_list_diskon, $id_diskon_selected;
    public $ceklistDeleteBarang   = [];
    public $jumlah_barang_pesanan = [];

    // public function updatingJumlahBarangPesanan($jumlah, $id_barang_pesanan)
    public function updateJumlahBarang($id_barang_pesanan)
    {
        try {
            $this->jumlah_barang_pesanan[$id_barang_pesanan] = FormatHelper::formatStringToNumber($this->jumlah_barang_pesanan[$id_barang_pesanan]);
            $this->validate([
                'jumlah_barang_pesanan.' . $id_barang_pesanan => 'required|numeric|min:1',
            ], ['required' => 'Kolom Ini Wajib Diisi', 'numeric' => 'Inputan Harus Angka', 'min' => 'Minimal 1']);
            $jumlah = $this->jumlah_barang_pesanan[$id_barang_pesanan];
            DB::beginTransaction();

            // 🔹 Ambil barang pesanan (dengan relasi yang dibutuhkan)
            $barang_pesanan = $this->pesanan_kasir->barang_pesanan_kasir()->with(['barang.opsi_harga', 'barang.opsi_satuan'])->findOrFail($id_barang_pesanan);
            $barang         = $barang_pesanan->barang;

            //menghitung jumlah dalam satuan utama
            $jumlah_dalam_satuan_utama = $barang->calculateJumlahDalamSatuanUtama($jumlah, $barang_pesanan->id_satuan);
            $total_jumlah              = $this->pesanan_kasir->barang_pesanan_kasir()->totalJumlahDalamSatuanUtama($barang->id, $jumlah_dalam_satuan_utama, $barang_pesanan->id);

            if ($total_jumlah > $barang->stok) {
                // $this->getJumlahBarangPesanan(); //disini pakai event agar nilai jumlah berubah ke nilai sebelum error
                $this->dispatch('alert', message: 'Stok barang tidak mencukupi.', status: 'error');
                return;
            }

            [$harga_jual, $harga_satuan_utama] = $barang->hargaJual($total_jumlah, $barang_pesanan->id_satuan);
            if ($this->pesanan_kasir->mode_kasir == 'RESEP') {
                $harga_jual         = $this->hargaJualResep($harga_jual, $this->persen_resep);
                $harga_satuan_utama = $this->hargaJualResep($harga_satuan_utama, $this->persen_resep);
            }
            $subtotal = $jumlah * $harga_jual;

            $barang_pesanan->update([
                'jumlah'             => $jumlah,
                'harga'              => $harga_jual,
                'harga_satuan_utama' => $harga_satuan_utama,
                'sub_total'          => $subtotal,
            ]);
            $this->updateTotalHarga();
            $this->setPromoBarangKasir($barang->id, $this->id_diskon_selected[$id_barang_pesanan]);
            if (! $this->cekStokPromoBarang($barang->id, $total_jumlah)) {
                $this->dispatch('alert', message: 'Stok barang bonus tidak mencukupi.', status: 'error');
                DB::rollback();
                return;
            }
            $this->dispatch('update_pesanan_kasir');
            $this->dispatch('alert', message: 'Jumlah Barang Berhasil Diubah', status: 'success');
            $this->jumlah_barang_pesanan[$id_barang_pesanan] = FormatHelper::formatNumberToString($this->jumlah_barang_pesanan[$id_barang_pesanan]);
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            $this->jumlah_barang_pesanan[$id_barang_pesanan] = FormatHelper::formatNumberToString($this->jumlah_barang_pesanan[$id_barang_pesanan]);
            $this->dispatch('alert', message: $e->getMessage(), status: 'error');
            throw $e;
            return false;
        }
    }

    public function setListDiskon($id_barang_pesanan)
    {
        //menghitung jumlah dalam satuan utama
        $barang_pesanan = $this->pesanan_kasir->barang_pesanan_kasir()->select(['id', 'id_barang'])->find($id_barang_pesanan);
        if (! $barang_pesanan) {
            $this->dispatch('alert', message: 'Data barang pesanan tidak ditemukan.', status: 'error');
            return;
        }
        $total_jumlah = $this->pesanan_kasir->barang_pesanan_kasir()->totalJumlahDalamSatuanUtama($barang_pesanan->id_barang, 0);

        //menggunakan total_jumlah agar mendapatkan harga jual dan harga satuan utama untuk seluruh nya
        $this->id_list_diskon[$id_barang_pesanan] = $this->getListPromoBarang($barang_pesanan->id_barang, $total_jumlah, $this->pesanan_kasir->id_member);
    }

    public function showListDiskonBarang($id_barang_pesanan)
    {
        $this->dispatch('toggle-modal-diskon', diskonModal: true);
        $this->dispatch('list-diskon', id_promo_barang_selected: $this->id_diskon_selected[$id_barang_pesanan], id_promo_barangs: $this->id_list_diskon[$id_barang_pesanan]);
    }

    #[On('select-diskon')]
    public function setDiskon($id_promo_barang, $id_barang)
    {
        try {
            DB::BeginTransaction();
            $this->setPromoBarangKasir($id_barang, $id_promo_barang);
            $total_jumlah = $this->pesanan_kasir->barang_pesanan_kasir()->totalJumlahDalamSatuanUtama($id_barang);
            if (! $this->cekStokPromoBarang($id_barang, $total_jumlah)) {
                $this->dispatch('alert', message: 'Stok barang bonus tidak mencukupi.', status: 'error');
                DB::rollback();
                return;
            }
            $this->dispatch('update_pesanan_kasir');
            DB::commit();
        } catch (\Exception $e) {
            $this->dispatch('alert', message: $e->getMessage(), status: 'error');
        }
    }

    public function listBarangPesanan()
    {
        // $this->pesanan_kasir->loadMissing('barang_pesanan_kasir');
        // 1. Eager Load semua relasi yang dibutuhkan di Blade
        $items = $this->pesanan_kasir->barang_pesanan_kasir()
            ->with(['satuan:id,nama',
                'barang.kategories',
                'barang.opsi_satuan',
                'barang.raks',
                'barang.satuan_utama',
                'barang.opsi_harga.satuan'])->orderBy('id_barang')->get();

        // 2. Mengambil data jumlah barang pesanan
        $this->jumlah_barang_pesanan = $items->pluck('jumlah', 'id')->toArray();

        $promoPesanan = $this->pesanan_kasir->load('promo_pesanan_kasir')->promo_pesanan_kasir()
            ->with('promo_barang')
            ->get()
            ->groupBy(fn($p) => $p->promo_barang->id_barang ?? null);

        // 3. Pre-calculate deskripsi promo untuk menghindari query di loop Blade
        return $items->map(function ($item, $key) use ($items, $promoPesanan) {

            // Ambil promo terpilih dari hasil groupBy di atas
            $selectedPromo = optional($promoPesanan->get($item->id_barang))->first();

            // Mengambil id promo yang telah terselect untuk promo barang
            // Set id promo yang sedang terpilih
            $this->id_diskon_selected[$item->id] = $selectedPromo?->id_promo_barang;
            // Menset List Diskon
            $this->setListDiskon($item->id);

            // Hapus promo jika list kosong atau diskon terpilih tidak ada di list
            if (
                ! $this->id_list_diskon[$item->id] ||                                              //hapus jika list diskon kosong
                ! in_array($this->id_diskon_selected[$item->id], $this->id_list_diskon[$item->id]) //hapus jika diskon terpilih tidak ada di list
            ) {
                //hapus promo barangnya
                $this->setPromoBarangKasir($item->id_barang);
                $this->id_diskon_selected[$item->id] = null;
            }

            $next                  = $items->get($key + 1);
            $item->promo_deskripsi = null;

            // Logika cek promo (diambil dari Blade sebelumnya)
            if (! $next || $item->id_barang != $next->id_barang) {
                // Eager loading di atas tidak membawa relasi ini, jadi tetap perlu query
                // Kecuali kita refactor PesananKasir model untuk pre-load promo_pesanan_kasir
                // Untuk sementara, kita pertahankan query ini, tapi hanya dipanggil saat kondisi terpenuhi
                $item->promo_deskripsi = $selectedPromo?->deskripsi;
            }
            return $item;
        });
    }

    public function render()
    {
        $this->pesanan_kasir->load('promo_pesanan_kasir');
        foreach ($this->pesanan_kasir->promo_pesanan_kasir as $promo_pesanan_kasir) {
            if ($promo_pesanan_kasir->tipe_promo == 'BARANG BONUS' || $promo_pesanan_kasir->tipe_promo == 'BARANG DISKON') {
                $this->setPromoBarangKasir($promo_pesanan_kasir->promo_barang->id_barang, $promo_pesanan_kasir->id_promo_barang);
            }
        }
        $this->barang_pesanan_kasir = $this->listBarangPesanan();
        return view('livewire.kasir.barang-pesanan-kasir.barang-pesanan-kasir-list');
    }

    #[On('update_pesanan_kasir')]
    public function updateTotalHarga()
    {
        $this->pesanan_kasir->load('barang_pesanan_kasir');
        $this->pesanan_kasir->update([
            'total_harga' => $this->pesanan_kasir->barang_pesanan_kasir()->sum('sub_total'),
        ]);
    }

    public function deleteAllPesananBarang()
    {
        $this->pesanan_kasir->barang_pesanan_kasir()->delete();
        $this->pesanan_kasir->promo_pesanan_kasir()->whereNotNull('id_promo_barang')->delete();
        $this->updateTotalHarga();
        $this->dispatch('update_pesanan_kasir');
        $this->dispatch('alert', message: 'Semua Barang Berhasil Dihapus', status: 'success');
    }

    public function deleteWithSelectedItem()
    {
        $barang_pesanan_selected = $this->pesanan_kasir->barang_pesanan_kasir()->whereIn('id', $this->ceklistDeleteBarang)->get();
        $barang_pesanan_selected->each(function ($barang_pesanan) {
            $masih_ada = $this->pesanan_kasir->barang_pesanan_kasir()
                ->where('id_barang', $barang_pesanan->id_barang)
                ->where('id', '!=', $barang_pesanan->id) // selain yang dihapus
                ->exists();
            if (! $masih_ada) {
                $this->pesanan_kasir->promo_pesanan_kasir()
                    ->whereHas('promo_barang', function ($q) use ($barang_pesanan) {
                        $q->where('id_barang', $barang_pesanan->id_barang);
                    })->delete();
            }
            $barang_pesanan->delete();
        });
        $this->ceklistDeleteBarang = [];
        $this->updateTotalHarga();
        $this->dispatch('update_pesanan_kasir');
        $this->dispatch('alert', message: 'Barang yang Di Pilih Berhasil Dihapus', status: 'success');
    }
    #[On('delete-barang-pesanan-kasir')]
    public function destroy(BarangPesananKasir $id)
    {
        if (Auth::check() && (Auth::user()->hasRole('admin') || $id->load('pesanan_kasir')->pesanan_kasir->id_user == Auth::user()->id)) {
            $masih_ada = $this->pesanan_kasir->barang_pesanan_kasir()
                ->where('id_barang', $id->id_barang)
                ->where('id', '!=', $id->id) // selain yang dihapus
                ->exists();
            if (! $masih_ada) {
                $this->pesanan_kasir->promo_pesanan_kasir()
                    ->whereHas('promo_barang', function ($q) use ($id) {
                        $q->where('id_barang', $id->id_barang);
                    })->delete();
            }
            $id->delete();
            $this->updateTotalHarga();
            $this->dispatch('update_pesanan_kasir');
            $this->dispatch('alert', message: 'Berhasil Menghapus Barang Pesanan Kasir');
        } else {
            $this->dispatch('alert', message: 'Kamu Tidak Memiliki Akses', status: 'error');
        }
    }

    #[On('error-stok')]
    public function errorStok($id_barang_pesanan)
    {
        $this->addError('barang_pesanan.' . $id_barang_pesanan, 'Error Background Merah Karena Stok Tidak Mencukupi');
        return;
    }
}
